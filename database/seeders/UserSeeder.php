<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminEmail = 'admin@oncochemo.local';
        $user = User::where('email', $adminEmail)->first();

        if ($user) {
            $user->update(['role' => 'admin']);
        } else {
            User::create([
                'name' => 'Admin User',
                'email' => $adminEmail,
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]);
        }
    }
}
