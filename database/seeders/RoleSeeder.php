<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::createOrFirst(['name' => 'Admin'], ['description' => 'Administrator with full access']);
        Role::createOrFirst(['name' => 'Seller'], ['description' => 'Seller who can manage sales']);
        Role::createOrFirst(['name' => 'Pelanggan'], ['description' => 'Customer who can view products']);
    }
}
