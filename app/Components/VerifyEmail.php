<?php

namespace App\Components;

use App\User;

use App\Components\Toolkit;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * класс используется для билда
 * письма на верификацию емаил
 */
class VerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function build()
    {
        $activationLink = route('verifyEmail', [
            'id' => $this->user->id, 
            'token' => Toolkit::getVerifyEmailToken($this->user)
        ]);

        return $this->subject('Ezdunov - Подтверждение Email')
            ->view('emails.verifyEmail')->with([
                'link' => $activationLink,
                'user' => $this->user,
            ]);
    }
}