<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AssignUserRole extends Command
{
    protected $signature = 'user:assign-role {userId} {role}';
    protected $description = 'Assign a role to a specific user';

    /**
     * @return int
     */
    public function handle(): int
    {
        $userId = $this->argument('userId');
        $role = $this->argument('role');

        $user = User::find($userId);

        if (!$user) {
            $this->error("User with ID {$userId} not found.");
            return 1;
        }

        if (!Role::where('name', $role)->exists()) {
            $this->error("Role {$role} does not exist.");
            return 1;
        }

        $user->assignRole($role);
        $this->info("Role {$role} assigned to user with ID {$userId}.");

        return 0;
    }
}
