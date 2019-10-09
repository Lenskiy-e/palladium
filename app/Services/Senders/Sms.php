<?php


namespace App\Services\Senders;


use App\Http\Interfaces\SenderInterface;

class Sms implements SenderInterface
{
    /**
     * @param $to
     * @param string $message
     * @param string|null $subject
     */
    public function send($to, string $message, ?string $subject) : void
    {
        //send sms
    }
}
