#!/bin/bash
cd "$(dirname "$0")"
php artisan tinker << 'PHPEOF'
use App\Models\Role, App\Models\User, App\Models\Product;

// Verify roles exist
echo "Roles: " . Role::count() . "\n";

// Create admin user
User::factory()->create([
    'name' => 'Admin User',
    'email' => 'admin@example.com',
    'password' => 'password',
    'role_id' => Role::where('name', 'Admin')->first()->id,
]);
echo "Admin created\n";

// Create seller
User::factory()->create([
    'name' => 'Seller User',
    'email' => 'seller@example.com',
    'password' => 'password',
    'role_id' => Role::where('name', 'Seller')->first()->id,
]);
echo "Seller created\n";

// Create customer
User::factory()->create([
    'name' => 'Customer User',
    'email' => 'customer@example.com',
    'password' => 'password',
    'role_id' => Role::where('name', 'Pelanggan')->first()->id,
]);
echo "Customer created\n";

// Create products
Product::create(['name' => 'Laptop', 'description' => 'Good laptop', 'price' => 1299.99, 'stock' => 10]);
Product::create(['name' => 'Mouse', 'description' => 'Wireless mouse', 'price' => 29.99, 'stock' => 50]);
Product::create(['name' => 'Keyboard', 'description' => 'RGB keyboard', 'price' => 149.99, 'stock' => 25]);
Product::create(['name' => 'USB Hub', 'description' => 'Multi-port hub', 'price' => 49.99, 'stock' => 15]);

echo "All data seeded!\n";
echo "Users: " . User::count() . ", Products: " . Product::count() . "\n";
PHPEOF
