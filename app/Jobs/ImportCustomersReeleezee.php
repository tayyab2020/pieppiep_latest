<?php

namespace App\Jobs;

use Auth;
use App\customers_details;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use PDF;
use App\Http\Controllers\UserController;

class ImportCustomersReeleezee implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $username = null;
    private $password = null;
    private $user = null;
    private $user_id = null;
    public $timeout = 0;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($username,$password,$user,$user_id)
    {
        $this->username = $username;
        $this->password = $password;
        $this->user = $user;
        $this->user_id = $user_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $username = $this->username;
        $password = $this->password;
        $user = $this->user;
        $user_id = $this->user_id;
        $user_controller = new UserController();
        $array_statuses = array();

        // ini_set('memory_limit', '-1');
        // ini_set('max_execution_time', -1);

        $url = "https://apps.reeleezee.nl/api/v1/customers";

        $headers = array(
            'Content-Type:application/json',
            'Authorization: Basic '. base64_encode($username.":".$password)
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response, true);

        if(!isset($response["value"]))
        {
            $array_statuses[] = $response["Message"];
        }
        else
        {
            $errors_array = $user_controller->ReeleezeeCustomersAPI($response,$user_id,$username,$password);
            $array_statuses = array_merge($array_statuses,$errors_array);

            while(isset($response["@odata.nextLink"]))
            {
                $url = $response["@odata.nextLink"];
    
                $headers = array(
                    'Content-Type:application/json',
                    'Authorization: Basic '. base64_encode($username.":".$password)
                );
        
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $response = curl_exec($ch);
                curl_close($ch);
                $response = json_decode($response, true);
    
                $errors_array = $user_controller->ReeleezeeCustomersAPI($response,$user_id,$username,$password);
                $array_statuses = array_merge($array_statuses,$errors_array);
            }

            $array_statuses[] = __("text.Customers imported successfully");
        }
        
        $msg = "";
        
        foreach($array_statuses as $i => $key)
        {
            $msg .= $i != 0 ? "<br>".$key : $key;
        }

        // \Mail::send(array(), array(), function ($message) use ($user,$msg) {
        //     $message->to($user->email)
        //         ->from('noreply@pieppiep.com')
        //         ->subject(__("text.Import customers from reeleezee job status!"))
        //         ->html($msg, 'text/html');
        // });
    }

    public function failed()
    {
        $user = $this->user;

        $msg = 'Job failed for importing reeleezee customers <br> Retailer: ' . $user->name .' ('.$user->company_name.')';

        \Mail::send(array(), array(), function ($message) use ($msg) {
            $message->to('tayyabkhurram62@gmail.com')
                ->from('info@pieppiep.com')
                ->subject('Job Failed')
                ->html($msg, 'text/html');
        });
    }
}
