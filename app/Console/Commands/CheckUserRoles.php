<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CheckUserRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:check-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and display user roles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::all();
        
        $this->info('Current users and their roles:');
        $this->info('================================');
        
        foreach ($users as $user) {
            $role = $user->role ?? 'customer';
            $this->line("ID: {$user->id} | Name: {$user->name} | Email: {$user->email} | Role: {$role}");
        }
        
        // Ask if user wants to update roles
        if ($this->confirm('Do you want to set specific users as admin or staff?')) {
            $userId = $this->ask('Enter user ID to update');
            $newRole = $this->choice('Select new role', ['admin', 'staff', 'customer'], 0);
            
            $user = User::find($userId);
            if ($user) {
                $user->role = $newRole;
                $user->save();
                $this->success("User {$user->name} has been set as {$newRole}");
            } else {
                $this->error('User not found');
            }
        }
        
        return 0;
    }
}
