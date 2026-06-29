<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SetupMobileApi extends Command
{
    protected $signature   = 'vsuite:setup-mobile-api';
    protected $description = 'Run pending API migrations and enable mobile API access for all active users';

    public function handle(): int
    {
        // 1. Run all pending migrations
        $this->info('Running pending migrations...');
        $this->call('migrate', ['--force' => true]);

        // 2. Enable can_use_api for all active users
        if (Schema::hasColumn('users', 'can_use_api')) {
            $updated = DB::table('users')
                ->where('is_active', 1)
                ->update(['can_use_api' => 1]);
            $this->info("Enabled API access for {$updated} active user(s).");
        } else {
            $this->warn('can_use_api column not found — migration may have failed.');
        }

        // 3. Show users who now have API access
        $this->info('Users with API access enabled:');
        $users = DB::table('users')
            ->where('is_active', 1)
            ->where('can_use_api', 1)
            ->select('id', 'name', 'email', 'role')
            ->get();

        $this->table(['ID', 'Name', 'Email', 'Role'],
            $users->map(fn($u) => [$u->id, $u->name, $u->email, $u->role])->toArray()
        );

        $this->info('Done. Use any of the above emails with the existing password to log in from the mobile app.');

        return 0;
    }
}
