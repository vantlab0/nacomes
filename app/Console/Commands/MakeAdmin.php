<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class MakeAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:admin
                            {--name= : The full name of the administrator}
                            {--email= : The unique administrator email address}
                            {--matric= : The administrator staff or matric ID}
                            {--password= : The secure password for the administrator}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Provision or promote a secure administrative account for NACOMES election management';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = $this->option('name') ?: $this->ask('Administrator Full Name', 'Electoral Commissioner');
        $email = $this->option('email') ?: $this->ask('Administrator Email Address');
        $matric = $this->option('matric') ?: $this->ask('Administrator Staff / Matric ID', 'ADMIN/ELECT/01');
        $password = $this->option('password') ?: $this->secret('Administrator Password');

        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'matric' => $matric,
            'password' => $password,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'matric' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return Command::FAILURE;
        }

        $normalizedMatric = User::normalizeMatricNo($matric);
        $normalizedEmail = strtolower(trim((string) $email));

        $user = User::where('email', $normalizedEmail)
            ->orWhere('matric_no', $normalizedMatric)
            ->first();

        if ($user) {
            $user->name = $name;
            $user->email = $normalizedEmail;
            $user->matric_no = $normalizedMatric;
            $user->password = Hash::make($password);
            $user->is_admin = true;
            $user->is_suspended = false;
            $user->email_verified_at ??= now();
            $user->terms_accepted_at ??= now();
            $user->save();

            AuditLog::record('admin_updated', "Promoted or updated existing user ({$user->email}) to administrator", $user->id, '127.0.0.1');

            $this->info("Successfully promoted existing user [{$user->email}] to Administrator.");

            return Command::SUCCESS;
        }

        $user = User::create([
            'name' => $name,
            'email' => $normalizedEmail,
            'matric_no' => $normalizedMatric,
            'password' => $password,
            'terms_accepted_at' => now(),
        ]);

        $user->is_admin = true;
        $user->email_verified_at = now();
        $user->save();

        AuditLog::record('admin_created', "Provisioned new administrative account for ({$user->email})", $user->id, '127.0.0.1');

        $this->info("Successfully provisioned new Administrator [{$user->email} | ID: {$user->matric_no}].");

        return Command::SUCCESS;
    }
}
