<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\ForgotPassword;

class SendForgotPasswordEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function handle()
    {
        $password = Str::random(9) . rand(0, 9) . chr(rand(65, 90));
        $password = str_shuffle($password);

        Mail::to($this->user->email)
            ->send(new ForgotPassword($password, $this->user->ten_nguoi_dung));

        // Update password in the database
        $this->user->mat_khau = bcrypt($password);
        $this->user->save();
    }
}
