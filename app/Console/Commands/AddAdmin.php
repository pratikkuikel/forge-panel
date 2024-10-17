<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Role;
use Illuminate\Console\Command;

class AddAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-admin {email} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds admin user to the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (User::count() == 0) {
            User::create([
                'name' => 'Super Admin',
                'email' => $this->argument('email'),
                'role' => Role::ADMIN->value,
                'password' => $this->argument('password'),
                'email_verified_at' => now(),
            ]);

            $this->info('Admin user created!');
        }
    }
}
