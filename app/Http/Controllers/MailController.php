<?php

namespace App\Http\Controllers;

use App\User;
use Swift_Mailer;
use Swift_Message;
use App\EmailSetting;
use Swift_Attachment;
use Swift_SmtpTransport;
use Purifier;
use Illuminate\Http\Request;
use Swift_Signers_DKIMSigner;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;
use Mews\Purifier\Facades\Purifier as FacadesPurifier;
use Mews\Purifier\Purifier as PurifierPurifier;

class MailController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:user');
    }

    public function showSendEmailForm()
    {
        $user = Auth::guard('user')->user();
        $user_role = $user->role_id;

        if ($user_role == 3) {
            return redirect()->route('user-login');
        }

        return view('user.send-email');
    }

    public function sendEmail(Request $request)
    {   
        $user = Auth::guard('user')->user();
        $organization_id = $user->organization->id;
        $company_name = $user->organization->company_name;

        $request->validate([
            'to' => 'required|email',
            'subject' => 'required|string',
            'message' => 'required|string',
            'attachments.*' => 'nullable|file|max:2048', // Validate attachments
        ]);

        $emailSettings = EmailSetting::where('organization_id', $organization_id)->first();
        
        if (!$emailSettings) {
            return redirect()->route('email-settings')->with('error', 'Email settings not configured.');
        }

        $details = [
            'to' => $request->input('to'),
            'subject' => $request->input('subject'),
            'body' => $request->input('message'),
        ];

        try {
            Mail::send('user.custom', ['body' => $details['body']], function ($message) use ($details, $emailSettings, $request, $company_name) {
                $message->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'info@pieppiep.com', $company_name);
                $message->to($details['to']);
                $message->subject($details['subject']);

                // Attach files if any
                if ($request->hasFile('attachments')) {
                    foreach ($request->file('attachments') as $file) {
                        $message->attach($file->getRealPath(), [
                            'as' => $file->getClientOriginalName(),
                            'mime' => $file->getClientMimeType(),
                        ]);
                    }
                }

                // DKIM signing
                // $privateKey = $emailSettings->dkim_private_key;
                // $selector = $emailSettings->dkim_selector;
                // $domain = $emailSettings->dkim_domain;

                // if ($privateKey && $selector && $domain) {
                //     $signer = new Swift_Signers_DKIMSigner($privateKey, $domain, $selector);
                //     $message->attachSigner($signer);
                // }
            });

            Log::info('Email sent successfully', ['details' => $details]);

            // Append to sent folder
            $this->appendToSentFolder($emailSettings, $details, $request);

            return redirect()->route('home')->with('success', 'Email sent successfully.');
        } catch (\Exception $e) {
            Log::error('Error sending email: ' . $e->getMessage());
            return redirect()->route('home')->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function emailSettings()
    {
        $user = Auth::guard('user')->user();
        $user_role = $user->role_id;

        if ($user_role == 3) {
            return redirect()->route('user-login');
        }

        $organization_id = $user->organization->id;

        $emailSettings = EmailSetting::where('organization_id',$organization_id)->first();
        
        if (!$emailSettings) {
            $emailSettings = new EmailSetting();
            $emailSettings->organization_id = $organization_id;
        }
        else
        {
            $emailSettings->password = Crypt::decryptString($emailSettings->password);
        }

        return view('user.email-settings', compact('emailSettings'));
    }

    public function saveEmailSettings(Request $request)
    {
        $user = Auth::guard('user')->user();
        $organization_id = $user->organization->id;

        $request->validate([
            'host' => 'required|string',
            'port' => 'required|integer',
            'encryption' => 'required|string',
            'username' => 'required|string',
            'password' => 'required|string',
            'imap_port' => 'required|integer',
        ]);

        $data = $request->only(['host', 'port', 'encryption', 'username', 'password', 'imap_port']);
        $data['password'] = Crypt::encryptString($data['password']);

        EmailSetting::updateOrCreate(
            ['organization_id' => $organization_id],
            $data
        );

        return redirect()->route('email-settings')->with('success', 'Email settings saved successfully.');
    }

    private function appendToSentFolder($emailSettings, $details, $request)
    {
        $decryptedPassword = Crypt::decryptString($emailSettings->password);

        try {
            $mailbox = "{" . $emailSettings->host . ":" . $emailSettings->imap_port . "/imap/ssl}INBOX.Sent";
            Log::info('Connecting to IMAP server', ['mailbox' => $mailbox, 'username' => $emailSettings->username]);

            $imapStream = imap_open($mailbox, $emailSettings->username, $decryptedPassword);

            if ($imapStream) {
                Log::info('Connected to IMAP server');
                $message = "To: " . $details['to'] . "\r\n";
                $message .= "Subject: " . $details['subject'] . "\r\n";
                $message .= "Date: " . date("r") . "\r\n";
                $message .= "From: " . $emailSettings->username . "\r\n";
                $message .= "\r\n" . $details['body'] . "\r\n";

                // Add attachments
                if ($request->hasFile('attachments')) {
                    foreach ($request->file('attachments') as $file) {
                        $attachment = chunk_split(base64_encode(file_get_contents($file->getRealPath())));
                        $message .= "\r\n--mixed-boundary\r\n";
                        $message .= "Content-Type: " . $file->getClientMimeType() . "; name=\"" . $file->getClientOriginalName() . "\"\r\n";
                        $message .= "Content-Transfer-Encoding: base64\r\n";
                        $message .= "Content-Disposition: attachment; filename=\"" . $file->getClientOriginalName() . "\"\r\n\r\n";
                        $message .= $attachment . "\r\n";
                    }
                }

                if (imap_append($imapStream, $mailbox, $message)) {
                    Log::info('Message appended to Sent folder');
                } else {
                    Log::warning('Failed to append message to Sent folder', ['imap_errors' => imap_errors()]);
                }

                imap_close($imapStream);
            } else {
                Log::warning('Could not connect to IMAP server', ['imap_errors' => imap_errors()]);
            }
        } catch (\Exception $e) {
            Log::error('Error appending to sent folder: ' . $e->getMessage());
        }
    }

    public function receiveEmails()
    {
        $user = Auth::guard('user')->user();
        $user_role = $user->role_id;

        if ($user_role == 3) {
            return redirect()->route('user-login');
        }

        $organization_id = $user->organization->id;
    
        $emailSettings = EmailSetting::where('organization_id', $organization_id)->first();
        
        if (!$emailSettings) {
            return redirect()->route('email-settings')->with('error', 'Email settings not configured.');
        }
    
        $decryptedPassword = Crypt::decryptString($emailSettings->password);
    
        try {
            $mailbox = "{" . $emailSettings->host . ":" . $emailSettings->imap_port . "/imap/ssl}INBOX";
            Log::info('Connecting to IMAP server', ['mailbox' => $mailbox, 'username' => $emailSettings->username]);
    
            $imapStream = imap_open($mailbox, $emailSettings->username, $decryptedPassword);
    
            if ($imapStream) {
                Log::info('Connected to IMAP server');
                $emails = imap_search($imapStream, 'ALL');
                $output = [];
    
                if ($emails) {
                    rsort($emails);
                    $emails = array_slice($emails, 0, 20); // Get only the last 20 emails
                    foreach ($emails as $email_number) {
                        $overview = imap_fetch_overview($imapStream, $email_number, 0)[0];
                        $structure = imap_fetchstructure($imapStream, $email_number);
                        $htmlContent = $this->getEmailBody($imapStream, $email_number, $structure);
    
                        // Clean HTML content using Purifier
                        $safeHtml = FacadesPurifier::clean($htmlContent);  // Make sure you are using the facade here
    
                        $output[] = [
                            'subject' => $overview->subject ?? 'No Subject',
                            'from' => $overview->from ?? 'Unknown Sender',
                            'date' => $overview->date ?? 'Unknown Date',
                            'message' => $safeHtml,
                        ];
                    }
                }
    
                imap_close($imapStream);
    
                return view('user.mailbox', [
                    'emails' => $output,
                    'folderName' => 'Inbox'
                ]);
            } else {
                Log::warning('Could not connect to IMAP server', ['imap_errors' => imap_errors()]);
                return redirect()->route('home')->with('error', 'Could not connect to IMAP server.');
            }
        } catch (\Exception $e) {
            Log::error('Error receiving emails: ' . $e->getMessage());
            return redirect()->route('home')->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    
    private function getEmailBody($imapStream, $email_number, $structure)
    {
        $body = '';
    
        if (!isset($structure->parts)) {
            // Single-part message
            $body = $this->decodeBody(imap_fetchbody($imapStream, $email_number, 1), $structure->encoding);
        } else {
            // Multi-part message
            foreach ($structure->parts as $partNumber => $part) {
                if ($part->type == 0) {
                    $body .= $this->decodeBody(imap_fetchbody($imapStream, $email_number, $partNumber + 1), $part->encoding);
                    if (isset($part->parameters)) {
                        foreach ($part->parameters as $parameter) {
                            if (strtolower($parameter->attribute) == 'charset' && strtolower($parameter->value) != 'us-ascii') {
                                $body = mb_convert_encoding($body, 'UTF-8', $parameter->value);
                            }
                        }
                    }
                } elseif ($part->type == 2) {
                    // Embedded message
                    $body .= $this->getEmailBody($imapStream, $email_number, $part);
                }
            }
        }
    
        return $body;
    }
    
    private function decodeBody($body, $encoding)
    {
        switch ($encoding) {
            case 0:
                return $body; // 7BIT
            case 1:
                return quoted_printable_decode($body); // 8BIT
            case 2:
                return imap_binary($body); // BINARY
            case 3:
                return base64_decode($body); // BASE64
            case 4:
                return quoted_printable_decode($body); // QUOTED_PRINTABLE
            case 5:
                return $body; // OTHER
            default:
                return $body; // UNKNOWN
        }
    }
    
    
}
