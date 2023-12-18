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
        $code = Str::random(4) . rand(0, 9) . chr(rand(65, 90));
        $code = str_shuffle($code);
        Mail::to($this->user->email)
            ->send(new ForgotPassword($code, $this->user->ten_nguoi_dung));
        $this->user->code = $code;
        $this->user->save();
    }
}
