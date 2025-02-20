<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        User::create([
            'name' => 'Super Admin',
            'email' => 'super.admin@example.com',
            'password' => bcrypt('12345678'),
            'role' => 'super admin',  
        ]);

        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('12345678'),
            'role' => 'admin',  
        ]);
    
        User::create([
            'name' => 'User',
            'email' => 'user@example.com',
            'password' => bcrypt('12345678'),
            'role' => 'user',  
        ]);

        User::create([
            'name' => 'Sales Person',
            'email' => 'sales@example.com',
            'password' => bcrypt('12345678'),
            'role' => 'sale',  
        ]);
    }
}
