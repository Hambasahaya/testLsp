<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles first
        $this->call(RoleSeeder::class);

        // Get roles
        $adminRole = Role::where('name', 'Admin')->first();
        $sellerRole = Role::where('name', 'Seller')->first();
        $customerRole = Role::where('name', 'Pelanggan')->first();

        // Create admin user
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 'password',
            'role_id' => $adminRole->id,
        ]);

        // Create seller user
        User::factory()->create([
            'name' => 'Seller User',
            'email' => 'seller@example.com',
            'password' => 'password',
            'role_id' => $sellerRole->id,
        ]);

        // Create customer users
        User::factory()->create([
            'name' => 'Customer User',
            'email' => 'customer@example.com',
            'password' => 'password',
            'role_id' => $customerRole->id,
        ]);

        User::factory(2)->create([
            'role_id' => $customerRole->id,
        ]);

        // Create sample products
        Product::create([
            'name' => 'Laptop Dell XPS 13',
            'description' => 'High-performance ultrabook',
            'price' => 1299.99,
            'stock' => 10,
        ]);

        Product::create([
            'name' => 'Wireless Mouse Logitech',
            'description' => 'Comfortable ergonomic mouse',
            'price' => 29.99,
            'stock' => 50,
        ]);

        Product::create([
            'name' => 'Mechanical Keyboard',
            'description' => 'RGB mechanical keyboard',
            'price' => 149.99,
            'stock' => 25,
        ]);

        Product::create([
            'name' => 'USB-C Hub',
            'description' => 'Multiport USB-C hub with multiple connectors',
            'price' => 49.99,
            'stock' => 15,
        ]);
    }
}
