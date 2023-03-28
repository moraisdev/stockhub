<?php

use Illuminate\Database\Seeder;
use App\Models\Admins;
use App\Models\Shops;
use App\Models\Suppliers;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Admins::create([
            'name' => 'Admin',
            'email' => 'admin@gruposhopmix.com',
            'password' => bcrypt('Admin@123')
        ]);

        Shops::create([
            'name' => 'Lojista Teste',
            'email' => 'lojista@gruposhopmix.com',
            'status' => 'active',
            'login_status' => 'authorized',
            'terms_agreed' => 1,
            'document'=> '11.111.111/1111-11',
            'password' => bcrypt('Admin@123'),
            'hash' => Str::random(30),
            'private_hash' => Str::random(30)
        ]);

        Suppliers::create([
            'name' => 123123,
            'legal_name' => 'Fornecedor Teste',
            'email' => 'fornecedor@gruposhopmix.com',
            'password' => bcrypt('Admin@123'),
            'status' => 'active',
            'login_status' => 'authorized',
            'terms_agreed' => 1,
            'hash' => Str::random(30),
            'private_hash' => Str::random(30)
        ]);
    }
}
