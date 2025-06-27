<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Product;
use App\Models\Role;
use App\Models\Tank;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        // Roles
        Role::create(['name' => 'super_admin', 'display_name' => 'Super Admin']);
        Role::create(['name' => 'ceo', 'display_name' => 'CEO']);
        Role::create(['name' => 'client', 'display_name' => 'Client']);

        // Products
        $products = [
            ['name' => 'Water', 'density' => 1.0],
            ['name' => 'EDC', 'density' => 1.24],
            ['name' => 'MEG', 'density' => 1.11],
            ['name' => 'N.Paraffin', 'density' => 0.75],
            ['name' => 'Ethanol', 'density' => 0.7892],
            ['name' => 'BS150', 'density' => 0.944],
        ];
        foreach ($products as $product) {
            Product::create($product);
        }


        // Tanks
        $tanks = [
            ['number' => 'T#1', 'cubic_meter_capacity' => 3000, 'status' => 'Available'],
            ['number' => 'T#2', 'cubic_meter_capacity' => 2500, 'status' => 'Available'],
            ['number' => 'T#3', 'cubic_meter_capacity' => 700, 'status' => 'Available'],
            ['number' => 'T#4', 'cubic_meter_capacity' => 2500, 'status' => 'Available'],
            ['number' => 'T#5', 'cubic_meter_capacity' => 2500, 'status' => 'Available'],
            ['number' => 'T#6', 'cubic_meter_capacity' => 700, 'status' => 'Available'],
            ['number' => 'T#7', 'cubic_meter_capacity' => 2500, 'status' => 'Available'],
            ['number' => 'T#8', 'cubic_meter_capacity' => 1800, 'status' => 'Available'],
            ['number' => 'T#9', 'cubic_meter_capacity' => 2500, 'status' => 'Available'],
            ['number' => 'T#10', 'cubic_meter_capacity' => 1800, 'status' => 'Available'],
        ];
        foreach ($tanks as $tank) {
            Tank::create($tank);
        }

        // Users
        $users = [
            [
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'email' => 'super_admin@ecoc.com',
                'password' => Hash::make('password@1234'),
                'role_id' => '1',
                'status' => 'active',
            ],
            [
                'first_name' => 'CEO',
                'last_name' => 'Test',
                'email' => 'ceo@ecoc.com',
                'password' => Hash::make('password@1234'),
                'role_id' => '2',
                'status' => 'active',
            ],
            [
                'first_name' => 'Client',
                'last_name' => 'Test',
                'email' => 'client@ecoc.com',
                'password' => Hash::make('password@1234'),
                'role_id' => '3',
                'status' => 'active',
            ],
        ];
        foreach ($users as $user) {
            User::create($user);
        }
    }
}
