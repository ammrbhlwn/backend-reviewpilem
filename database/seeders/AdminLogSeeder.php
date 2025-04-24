<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks (if needed)
        Schema::disableForeignKeyConstraints();

        // Truncate the table to start fresh (optional)
        DB::table('admin_log')->truncate();

        // Seed the admin_log table
        DB::table('admin_log')->insert([
            [
                'id_admin' => 1,
                'timestamp' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_admin' => 1,
                'timestamp' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_admin' => 2,
                'timestamp' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Re-enable foreign key checks (if disabled earlier)
        Schema::enableForeignKeyConstraints();
    }
}
