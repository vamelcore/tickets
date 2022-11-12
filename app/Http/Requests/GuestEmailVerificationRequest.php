<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class GuestEmailVerificationRequest extends EmailVerificationRequest
{
    /**
     * @var User
     */
    private $user;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (!$this->route('id')) {
            return false;
        }

        $this->user = User::find((int) $this->route('id'));
        if (!$this->user) {
            return false;
        }

        if (! hash_equals((string) $this->route('hash'),
            sha1($this->user->getEmailForVerification()))) {
            return false;
        }

        return true;
    }

    /**
     * Fulfill the email verification request.
     *
     * @return User
     */
    public function verify()
    {
        if (! $this->user->hasVerifiedEmail()) {
            $this->user->markEmailAsVerified();

            event(new Verified($this->user()));
        }

        return $this->user;
    }
}
