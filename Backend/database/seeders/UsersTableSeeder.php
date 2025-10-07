<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles if not exists
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $borrowerRole = Role::firstOrCreate(['name' => 'borrower']);
        $sellerRole = Role::firstOrCreate(['name' => 'seller']);

        // Create users with roles
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole($adminRole);

        $borrower = User::create([
            'name' => 'Borrower User',
            'email' => 'borrower@example.com',
            'password' => Hash::make('password'),
        ]);
        $borrower->assignRole($borrowerRole);

        $seller = User::create([
            'name' => 'Seller User',
            'email' => 'seller@example.com',
            'password' => Hash::make('password'),
        ]);
        $seller->assignRole($sellerRole);
    }
}
