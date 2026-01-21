<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use App\Models\Product;
use Illuminate\Console\Command;

class SeedDemoData extends Command
{
    protected $signature = 'demo:seed';
    protected $description = 'Seed demo data for the application';

    public function handle()
    {
        $this->info('Seeding demo data...');

        // Verify roles exist
        if (Role::count() === 0) {
            $this->error('No roles found. Please run migrations first.');
            return 1;
        }

        // Create admin user
        if (!User::where('email', 'admin@example.com')->exists()) {
            User::factory()->create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => 'password',
                'role_id' => Role::where('name', 'Admin')->first()->id,
            ]);
            $this->info('✓ Admin user created');
        }

        // Create seller
        if (!User::where('email', 'seller@example.com')->exists()) {
            User::factory()->create([
                'name' => 'Seller User',
                'email' => 'seller@example.com',
                'password' => 'password',
                'role_id' => Role::where('name', 'Seller')->first()->id,
            ]);
            $this->info('✓ Seller user created');
        }

        // Create customer
        if (!User::where('email', 'customer@example.com')->exists()) {
            User::factory()->create([
                'name' => 'Customer User',
                'email' => 'customer@example.com',
                'password' => 'password',
                'role_id' => Role::where('name', 'Pelanggan')->first()->id,
            ]);
            $this->info('✓ Customer user created');
        }

        // Create products
        if (Product::count() === 0) {
            Product::create(['name' => 'Laptop', 'description' => 'Good laptop', 'price' => 1299.99, 'stock' => 10]);
            Product::create(['name' => 'Mouse', 'description' => 'Wireless mouse', 'price' => 29.99, 'stock' => 50]);
            Product::create(['name' => 'Keyboard', 'description' => 'RGB keyboard', 'price' => 149.99, 'stock' => 25]);
            Product::create(['name' => 'USB Hub', 'description' => 'Multi-port hub', 'price' => 49.99, 'stock' => 15]);
            $this->info('✓ Products created');
        }

        $this->info('');
        $this->line('Demo data seeded successfully!');
        $this->line('Users: ' . User::count());
        $this->line('Products: ' . Product::count());
        $this->line('');
        $this->line('Demo credentials:');
        $this->line('Admin: admin@example.com / password');
        $this->line('Seller: seller@example.com / password');
        $this->line('Customer: customer@example.com / password');

        return 0;
    }
}
