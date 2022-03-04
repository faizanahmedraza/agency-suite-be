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

        $admin = User::create([
            'first_name' => 'Mehmood',
            'last_name' => 'Ismail',
            'username' => 'mehmood.ismail@saasfa.com',
            'password' => Hash::make('123456'),
        ]);

        $admin->admin()->save(new Admin());

        $finance = User::create([
            'first_name' => 'Imran',
            'last_name' => 'Mumtaz',
            'username' => 'imran.mumtaz@saasfa.com',
            'password' => Hash::make('123456'),
        ]);

        $finance->admin()->save(new Admin());

        $hr = User::create([
            'first_name' => 'Fahad',
            'last_name' => 'khan',
            'username' => 'fahad.khan@saasfa.com',
            'password' => Hash::make('123456'),
        ]);

        $hr->admin()->save(new Admin());

        $accounts = User::create([
            'first_name' => 'Taimour',
            'last_name' => 'Hadi',
            'username' => 'taimour.hadi@saasfa.com',
            'password' => Hash::make('123456'),
        ]);

        $accounts->admin()->save(new Admin());

        $superAdmin->assignRole('Super Admin');
        $admin->assignRole('Admin');
        $finance->assignRole('Finance');
        $hr->assignRole('HR');
        $accounts->assignRole('Accounts');
    }
}
