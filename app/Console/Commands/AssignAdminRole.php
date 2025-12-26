<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Role;

class AssignAdminRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'role:assign-admin {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assigns the admin role to a user by email';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("User with email {$email} not found.");
            return 1;
        }
        
        $role = Role::where('slug', 'admin')->first();
        
        if (!$role) {
            $this->error("Admin role not found. Please run migrations and seeders first.");
            return 1;
        }
        
        $user->assignRole($role);
        
        $this->info("Successfully assigned admin role to {$email}");
        
        return 0;
    }
}
