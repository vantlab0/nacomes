<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\VerifyEmailCodeMail;
use App\Models\AuditLog;
use App\Models\Setting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Display the settings configuration interface.
     */
    public function index(): View
    {
        $settings = Setting::allAsMap();

        return view('admin.settings.index', [
            'settings' => $settings,
        ]);
    }

    /**
     * Update website branding and SMTP settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'registration_open' => ['nullable', 'boolean'],
            'site_logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:3072'],
            'site_icon' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp,ico', 'max:1024'],
            'remove_site_logo' => ['nullable', 'boolean'],
            'remove_site_icon' => ['nullable', 'boolean'],
            'mail_host' => ['nullable', 'string', 'max:255'],
            'mail_port' => ['nullable', 'integer', 'between:1,65535'],
            'mail_username' => ['nullable', 'string', 'max:255'],
            'mail_password' => ['nullable', 'string', 'max:255'],
            'mail_encryption' => ['nullable', 'string', 'in:tls,ssl,none'],
            'mail_from_address' => ['nullable', 'email', 'max:255'],
            'mail_from_name' => ['nullable', 'string', 'max:255'],
        ]);

        // 1. Handle Website Logo
        if ($request->hasFile('site_logo')) {
            $oldLogo = Setting::get('site_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }
            $logoPath = $request->file('site_logo')->store('settings', 'public');
            Setting::set('site_logo', $logoPath);
        } elseif ($request->boolean('remove_site_logo')) {
            $oldLogo = Setting::get('site_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }
            Setting::set('site_logo', null);
        }

        // 2. Handle Website Icon / Favicon
        if ($request->hasFile('site_icon')) {
            $oldIcon = Setting::get('site_icon');
            if ($oldIcon && Storage::disk('public')->exists($oldIcon)) {
                Storage::disk('public')->delete($oldIcon);
            }
            $iconPath = $request->file('site_icon')->store('settings', 'public');
            Setting::set('site_icon', $iconPath);
        } elseif ($request->boolean('remove_site_icon')) {
            $oldIcon = Setting::get('site_icon');
            if ($oldIcon && Storage::disk('public')->exists($oldIcon)) {
                Storage::disk('public')->delete($oldIcon);
            }
            Setting::set('site_icon', null);
        }

        // 3. Handle SMTP Email Settings
        $smtpFields = [
            'mail_host',
            'mail_port',
            'mail_username',
            'mail_encryption',
            'mail_from_address',
            'mail_from_name',
        ];

        foreach ($smtpFields as $field) {
            Setting::set($field, $request->input($field) ?: null);
        }

        // Only update password if provided
        if ($request->filled('mail_password')) {
            Setting::set('mail_password', $request->input('mail_password'));
        } elseif ($request->has('clear_mail_password') && $request->boolean('clear_mail_password')) {
            Setting::set('mail_password', null);
        }

        // 4. Handle Student Registration Toggle
        $oldRegistrationOpen = Setting::registrationIsOpen();
        $newRegistrationOpen = $request->boolean('registration_open');
        Setting::setRegistrationOpen($newRegistrationOpen);

        if ($oldRegistrationOpen !== $newRegistrationOpen) {
            AuditLog::record(
                $newRegistrationOpen ? 'registration_opened' : 'registration_closed',
                $newRegistrationOpen
                    ? 'Opened student registration.'
                    : 'Closed student registration.',
                $request->user()?->id,
                $request->ip()
            );
        }

        AuditLog::record(
            'update_settings',
            'Updated portal branding and SMTP email configuration.',
            $request->user()?->id,
            $request->ip()
        );

        return back()->with('status', 'Portal settings have been updated successfully.');
    }

    /**
     * Quick toggle student registration status.
     */
    public function toggleRegistration(Request $request): RedirectResponse
    {
        $currentlyOpen = Setting::registrationIsOpen();
        Setting::setRegistrationOpen(! $currentlyOpen);

        AuditLog::record(
            $currentlyOpen ? 'registration_closed' : 'registration_opened',
            $currentlyOpen
                ? 'Closed student registration.'
                : 'Opened student registration.',
            $request->user()?->id,
            $request->ip()
        );

        $message = $currentlyOpen
            ? 'Student registration has been closed.'
            : 'Student registration has been opened.';

        return back()->with('status', $message);
    }

    /**
     * Send a test verification code email to verify SMTP configuration.
     */
    public function testEmail(Request $request): RedirectResponse
    {
        $request->validate([
            'test_recipient' => ['required', 'email'],
        ]);

        try {
            // Apply current dynamic settings
            static::applyMailSettings();

            $recipient = $request->input('test_recipient');
            $user = $request->user();

            Mail::to($recipient)->send(new VerifyEmailCodeMail($user, '123456'));

            return back()->with('status', "Test email sent successfully to {$recipient} using active mail settings.");
        } catch (\Throwable $e) {
            return back()->withErrors([
                'test_email' => 'Failed to send test email: '.$e->getMessage(),
            ]);
        }
    }

    /**
     * Dynamically apply mail settings from the database into Laravel config.
     */
    public static function applyMailSettings(): void
    {
        $host = Setting::get('mail_host');
        if (! $host) {
            return;
        }

        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp.host', $host);
        Config::set('mail.mailers.smtp.port', (int) Setting::get('mail_port', 587));
        Config::set('mail.mailers.smtp.username', Setting::get('mail_username'));
        if ($password = Setting::get('mail_password')) {
            Config::set('mail.mailers.smtp.password', $password);
        }
        $encryption = Setting::get('mail_encryption', 'tls');
        Config::set('mail.mailers.smtp.encryption', $encryption === 'none' ? null : $encryption);

        if ($fromAddress = Setting::get('mail_from_address')) {
            Config::set('mail.from.address', $fromAddress);
        }
        if ($fromName = Setting::get('mail_from_name')) {
            Config::set('mail.from.name', $fromName);
        }
    }
}
