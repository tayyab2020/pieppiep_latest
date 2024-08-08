<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mollie\Laravel\Facades\Mollie;
use App\orders;
use App\Generalsetting;
use App\users;
use App\bookings;
use App\invoices;
use App\Sociallink;


class MollieLastPayment extends Controller {

    public function handle(Request $request) {

        if (! $request->has('id')) {
            return;
        }
    
        $api_key = Generalsetting::findOrFail(1);
        $sl = Sociallink::findOrFail(1);
    
        $mollie = new \Mollie\Api\MollieApiClient();
        $mollie->setApiKey($api_key->mollie);
    
        $payment = $mollie->payments->get($request->id);
    
        if ($payment->isPaid()) {
    
            $data = $payment->metadata;
            $check = invoices::where('id','=',$data->invoice_id)->first();
    
            if($check->payment_id1 == NULL)
            {
                $invoice = invoices::where('id','=',$data->invoice_id)->update(['is_partial' => 0,'payment_id1' => $request->id]);
                $booking = bookings::where('invoice_id','=',$data->invoice_id)->update(['is_partial' => 0]);
    
                $handyman_name = $data->handyman_name;
                $client_name = $data->client_name;
                $handyman_dash = url('/').'/aanbieder/dashboard';
                $client_dash = url('/').'/aanbieder/quotation-requests';
    
                if($data->language == 'eng')
                {
    
                    \Mail::send(array(), array(), function ($message) use ($data, $client_name, $client_dash) {
                        $message->to($data->user_email)
                            ->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'info@vloerofferte.nl')
                            ->subject("Remaining Payment Received!")
                            ->html("Dear Mr/Mrs ". $client_name .",<br><br>Your full payment for your booking has been received. For further details visit your client panel through <a href='".$client_dash."'>here.</a><br><br>Kind regards,<br><br>Klantenservice<br><br> Vloerofferte", 'text/html');
                    });
    
                    \Mail::send(array(), array(), function ($message) use ($data, $handyman_name, $handyman_dash) {
                        $message->to($data->handyman_email)
                            ->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'info@vloerofferte.nl')
                            ->subject("Remaining Payment Submitted!")
                            ->html("Dear Mr/Mrs ". $handyman_name .",<br><br>Payment status for one of your bookings has been changed from partial to full. Kindly review your bookings before it get expired. You can visit your profile dashboard through <a href='".$handyman_dash."'>here.</a><br><br>Kind regards,<br><br>Klantenservice<br><br> Vloerofferte", 'text/html');
                    });
    
                }
                else
                {
    
                    \Mail::send(array(), array(), function ($message) use ($data, $client_name, $client_dash) {
                        $message->to($data->user_email)
                            ->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'info@vloerofferte.nl')
                            ->subject("Restant factuurbedrag voldaan!")
                            ->html("Beste ". $client_name .",<br><br>Wij hebben het resterende bedrag op je reservering ontvangen. Klik op account om de status van je klus te bekijken <a href='".$client_dash."'>account.</a><br><br>Met vriendelijke groeten,<br><br>Klantenservice<br><br> Vloerofferte", 'text/html');
                    });
    
                    \Mail::send(array(), array(), function ($message) use ($data, $handyman_name, $handyman_dash) {
                        $message->to($data->handyman_email)
                            ->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'info@vloerofferte.nl')
                            ->subject("Restant factuurbedrag voldaan!")
                            ->html("Beste ". $handyman_name .",<br><br>wij hebben het resterende bedrag op je reservering ontvangen van de oprachtgever. Zodra, de klus is afgerond maken wij het geld over op je rekening. Klik op account om de status van je klus te bekijken <a href='".$handyman_dash."'>account.</a><br><br>Met vriendelijke groeten,<br><br>Klantenservice<br><br> Vloerofferte", 'text/html');
                    });
    
                }
    
                \Mail::send(array(), array(), function ($message) use ($sl) {
                    $message->to($sl->admin_email)
                        ->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'info@vloerofferte.nl')
                        ->subject("Remaining Payment Received!")
                        ->html("Dear Nordin Adoui, Payment received for a booking. Payment status for this booking has been changed from partial to full. You can visit your admin panel to view all bookings.", 'text/html');
                });
    
            }
    
        }
    }

}
