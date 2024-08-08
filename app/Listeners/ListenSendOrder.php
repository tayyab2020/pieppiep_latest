<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ListenSendOrder implements ShouldQueue
{

    public function handle($event)
    {
        $msg = 'Hi' . $event->id;

        \Mail::send(array(), array(), function ($message) use ($msg) {
            $message->to('tayyabkhurram62@gmail.com')
                ->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'info@pieppiep.com')
                ->subject(__('text.Invoice Status Changed'))
                ->html($msg, 'text/html');
        });
    }
}
