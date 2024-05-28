<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            "name" => "Librarian",
            "loginID" => "admin",
            "password" => Hash::make("secret"),
            "role_id" => 2,
        ]);
        User::create([
            "name" => "Orifov Orifjon Umidovich",
            "loginID" => "student",
            "password" => Hash::make("secret"),
            "role_id" => 1,
        ]);
    }
}
