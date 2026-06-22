<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin; // Import model Admin

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::create([
            'username' => 'admin',
            'password_hash' => Hash::make('password'), // Ganti 'password' dengan password yang kuat
            'email' => 'admin@example.com',
            'nama_lengkap' => 'Administrator Utama',
        ]);

        // Atau jika Anda ingin menggunakan DB facade langsung:
        // DB::table('admin')->insert([
        //     'username' => 'admin',
        //     'password_hash' => Hash::make('password'),
        //     'email' => 'admin@example.com',
        //     'nama_lengkap' => 'Administrator Utama',
        //     'created_at' => now(),
        //     'updated_at' => now(),
        // ]);
    }
}