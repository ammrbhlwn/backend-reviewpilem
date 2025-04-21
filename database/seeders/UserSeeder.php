<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks (if needed)
        Schema::disableForeignKeyConstraints();

        // Truncate the table to start fresh (optional)
        DB::table('users')->truncate();

        // Seed the users table
        DB::table('users')->insert([
            [
                'nama' => 'Admin User',
                'username' => 'adminadmin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'User User',
                'username' => 'useruser',
                'email' => 'user@example.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Re-enable foreign key checks (if disabled earlier)
        Schema::enableForeignKeyConstraints();
    }
}
