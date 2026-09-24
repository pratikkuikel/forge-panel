<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Role;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class AddAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-admin {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds admin user to the database';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (User::exists()) {
            $this->error('An application user already exists.');

            return self::FAILURE;
        }

        $credentials = [
            'email' => $this->argument('email'),
            'password' => $this->secret('Password (minimum 12 characters)'),
        ];

        $validator = Validator::make($credentials, [
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', Password::min(12)->letters()->mixedCase()->numbers()->symbols()],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        User::create([
            'name' => 'Super Admin',
            'email' => $credentials['email'],
            'role' => Role::ADMIN->value,
            'password' => $credentials['password'],
            'email_verified_at' => now(),
        ]);

        $this->info('Admin user created!');

        return self::SUCCESS;
    }
}
