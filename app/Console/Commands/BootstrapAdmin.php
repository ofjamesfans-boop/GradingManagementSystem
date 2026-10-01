<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class BootstrapAdmin extends Command
{
    protected $signature = 'gradeflow:bootstrap-admin';

    protected $description = 'Create the first administrator from deployment secrets';

    public function handle(): int
    {
        if (User::whereIn('role', ['administrator', 'registrar'])->exists()) {
            $this->info('An administrator already exists. No account changed.');

            return self::SUCCESS;
        }

        $email = getenv('ADMIN_EMAIL') ?: '';
        $password = getenv('ADMIN_PASSWORD') ?: '';

        if (! filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 12) {
            $this->error('Set ADMIN_EMAIL and a unique ADMIN_PASSWORD of at least 12 characters in Render.');

            return self::FAILURE;
        }

        User::create([
            'name' => 'System Administrator',
            'email' => $email,
            'role' => 'administrator',
            'status' => 'active',
            'password' => Hash::make($password),
        ]);

        $this->info('Initial administrator account created.');

        return self::SUCCESS;
    }
}
