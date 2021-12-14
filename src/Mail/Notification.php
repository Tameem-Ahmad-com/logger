<?php

namespace Computan\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use JohnDoe\BlogPackage\Models\Post;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct()
    {
       
    }

    public function build()
    {
       
    }
}