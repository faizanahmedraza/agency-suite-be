<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'first_name' => 'Faizan Ahmed',
            'last_name' => 'Raza',
            'username' => 'faizan.raza@saasfa.com',
            'password' => Hash::make('admin123'),
        ]);

        $user->assignRole('Super Admin');
    }
}
