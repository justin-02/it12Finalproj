<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'name' => 'System Administrator',
                'email' => 'admin@agrivet.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ],
            [
                'name' => 'Inventory Manager',
                'email' => 'inventory@agrivet.com',
                'password' => Hash::make('inventory123'),
                'role' => 'inventory',
            ],
            [
                'name' => 'Cashier Staff',
                'email' => 'cashier@agrivet.com',
                'password' => Hash::make('cashier123'),
                'role' => 'cashier',
            ],
            [
                'name' => 'Store Helper',
                'email' => 'helper@agrivet.com',
                'password' => Hash::make('helper123'),
                'role' => 'helper',
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }

        $this->command->info('Demo users created successfully!');
        $this->command->info('Admin: admin@agrivet.com / admin123');
        $this->command->info('Inventory: inventory@agrivet.com / inventory123');
        $this->command->info('Cashier: cashier@agrivet.com / cashier123');
        $this->command->info('Helper: helper@agrivet.com / helper123');
    }
}