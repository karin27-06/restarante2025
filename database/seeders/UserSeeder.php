<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //get the roles
        $adminRole = Role::findById(1);
        $personalRole = Role::findById(2);


        // get the permissions 
        $permissions = Permission::all()->pluck('name')->toArray();

         $admin_1 = User::create([
            'name' => 'Pablo Isaac Lupu Garcia',
            'email' => 'pablolupu2020@gmail.com',
            'username' => 'pablolupu',
            'password' => Hash::make('12345678'),
            'status' => 1,
        ]);
       
         $admin_2 = User::create([
            'name' => 'Karin Hair',
            'email' => 'kayisanta5@gmail.com',
            'username' => 'kchozo27',
            'password' => Hash::make('12345678'),
            'status' => 1,
        ]);

  
        $adminRole->syncPermissions($permissions);
        $admin_1->assignRole($adminRole);
        $admin_2->assignRole($adminRole);
  
    }
}
