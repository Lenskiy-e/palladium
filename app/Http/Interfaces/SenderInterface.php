<?php


namespace App\Http\Interfaces;


interface SenderInterface
{
    public function send($to, string $message, ?string $subject) : void;
}
