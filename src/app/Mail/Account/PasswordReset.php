<?php

namespace App\Mail\Account;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordReset extends Mailable
{
    use Queueable;
    use SerializesModels;

    /** @var User $user */
    public $user;

    /** @var string */
    public $plainPassword;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, string $plainPassword)
    {
        $this->user = $user;
        $this->plainPassword  = $plainPassword;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): ResetPasswordMail
    {
        return $this
            ->markdown('emails.account.password-reset', [
                'user' => $this->user,
                'plainPassword' => $this->plainPassword
            ]);
    }
}
