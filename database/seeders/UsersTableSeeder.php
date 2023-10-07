<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        // $user = User::firstOrNew([
        //     'email'=>'admin@dtrack.com',
        // ]);

        // if(!$user->exists){
        //     $user->fill([
        //         'name'=>"Admin",
        //         'password'=>bcrypt('adminpass'),
        //     ])->save();
        // }

        $users = [
                [
                'name'=>"Super Admin",
                'email'=>"adminsuper@dtrack.com",
                'password'=>bcrypt("superadmin"),
                'role' => 'super-admin'
                ],
                [
                'name'=>"Admin",
                'email'=>"admin@dtrack.com",
                'password'=>bcrypt("adminpass"),
                'role' => 'admin'
                ],
            ];

        // User::upsert($users,['email','name'],['name','email','password']);

        foreach($users as $user){
            
            $userRow = User::firstOrNew(['email' => $user['email']]);
            if (!$userRow->exists) {    
                User::create([
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'password' => $user['password']
                ])->assignRole($user['role']);
            }
        };
    }
}
