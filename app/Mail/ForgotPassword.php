<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ForgotPassword extends Mailable
{
    use Queueable, SerializesModels;

    public $password;
    public $userName;

    public function __construct($password, $userName)
    {
        $this->password = $password;
        $this->userName = $userName;
    }

    public function build()
    {
        return $this->view('forgotPassword', ['password' => $this->password, 'user' => $this->userName])
            ->subject('KB&H Website');
    }
}
