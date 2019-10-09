<?php


namespace App\Services;


use App\Http\Interfaces\SenderInterface;
use App\Services\Senders\Mail;
use App\Services\Senders\Sms;

class Sender
{
    /**
     * @param string $type
     * @return SenderInterface
     */
    public static function generate(string $type) : SenderInterface
    {
        switch (strtolower($type))
        {
            case 'email':
                return new Mail();
                break;
            case 'sms':
                return new Sms();
                break;
        }
    }
}
