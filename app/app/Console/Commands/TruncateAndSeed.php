<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TruncateAndSeed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:truncate-seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Truncates all tables and runs the seeders';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Disable FK constraint
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate tables
        DB::table('factions')->truncate();
        DB::table('equipments')->truncate();
        DB::table('characters')->truncate();
        DB::table('users')->truncate();

        // Enable FK constraint
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Seed
        $this->call('db:seed');

        $this->info('Truncated tables and seeders run successfully.');
        return 0;
    }
}
