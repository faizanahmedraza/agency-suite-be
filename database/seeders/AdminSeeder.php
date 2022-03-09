<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $superAdmin = User::create([
            'first_name' => 'Faizan Ahmed',
            'last_name' => 'Raza',
            'username' => 'faizan.raza@saasfa.com',
            'password' => Hash::make('admin123'),
        ]);

        $superAdmin->admin()->save(new Admin());

        $superAdmin->assignRole('Super Admin');
    }
}
