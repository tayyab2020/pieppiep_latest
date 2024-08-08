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
use App\Http\Controllers\APIController;

class ExportCustomersReeleezee implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $username = null;
    private $password = null;
    private $user = null;
    private $related_users = [];
    public $timeout = 0;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($username,$password,$user,$related_users)
    {
        $this->username = $username;
        $this->password = $password;
        $this->user = $user;
        $this->related_users = $related_users;
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
        $related_users = $this->related_users;
        $api_controller = new APIController();

        $api_controller->ExportCustomersReeleezee($user,$related_users,$username,$password);
    }

    public function failed()
    {
        $user = $this->user;

        $msg = 'Job failed for exporting customers to Reeleezee <br> Retailer: ' . $user->name .' ('.$user->company_name.')';

        \Mail::send(array(), array(), function ($message) use ($msg) {
            $message->to('tayyabkhurram62@gmail.com')
                ->from('info@pieppiep.com')
                ->subject('Job Failed')
                ->html($msg, 'text/html');
        });
    }
}
