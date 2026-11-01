<?php

namespace App\Models;

use App\Mail\VerifyEmailCodeMail;
use App\Support\MatricNumber;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;

#[Fillable(['name', 'matric_no', 'email', 'password', 'terms_accepted_at', 'is_admin', 'is_suspended', 'suspended_at', 'verification_code', 'verification_code_expires_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Normalize a student matriculation number.
     */
    public static function normalizeMatricNo(?string $value): string
    {
        return MatricNumber::normalize($value);
    }

    /**
     * Interact with the user's matriculation number.
     *
     * @return Attribute<string, string>
     */
    protected function matricNo(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value): string => (string) $value,
            set: fn (?string $value): string => static::normalizeMatricNo($value),
        );
    }

    /**
     * Determine whether the user is an administrator.
     */
    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    /**
     * Determine whether the user's account is currently suspended.
     */
    public function isSuspended(): bool
    {
        return (bool) $this->is_suspended;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'verification_code_expires_at' => 'datetime',
            'terms_accepted_at' => 'datetime',
            'suspended_at' => 'datetime',
            'is_admin' => 'boolean',
            'is_suspended' => 'boolean',
            'password' => 'hashed',
        ];
    }

    /**
     * Generate a fresh 6-digit verification code and save it to the database.
     */
    public function generateVerificationCode(): string
    {
        $code = sprintf('%06d', random_int(100000, 999999));

        $this->forceFill([
            'verification_code' => $code,
            'verification_code_expires_at' => now()->addMinutes(30),
        ])->save();

        return $code;
    }

    /**
     * Send email verification with 6-digit code.
     */
    public function sendEmailVerificationCode(): void
    {
        if (empty($this->email)) {
            return;
        }

        $code = $this->generateVerificationCode();

        Mail::to($this->email)->send(new VerifyEmailCodeMail($this, $code));
    }

    /**
     * Override default Laravel notification to use 6-digit code instead of signed link.
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->sendEmailVerificationCode();
    }
}
