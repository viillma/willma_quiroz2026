<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionsSeeder;
use Illuminate\Console\Command;

class SyncRoles extends Command
{
    protected $signature = 'app:sync-roles';

    protected $description = 'Sync permissions and legacy user roles';

    public function handle(): void
    {
        $this->call(PermissionsSeeder::class, ['--class' => 'Database\\Seeders\\PermissionsSeeder']);

        $bar = $this->output->createProgressBar(User::count());
        $bar->start();

        foreach (User::lazy() as $user) {
            if ($user->role && ! $user->roles()->exists()) {
                $role = Role::where('nombre', $user->role)->first();
                if ($role) {
                    $user->roles()->syncWithoutDetaching([$role->id]);
                }
            }
            $user->ensureRolesHavePermissions();
            $bar->advance();
        }

        $bar->finish();

        $this->newLine();
        $this->info('Roles and permissions synced successfully.');
    }
}
