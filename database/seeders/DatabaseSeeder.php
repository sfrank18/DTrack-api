<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call(UserRolesAndPermissionSeeder::class);
        $this->call(DivisionSeeder::class);
        $this->call(DepartmentSeeder::class);
        $this->call(DesignationSeeder::class);
        $this->call(DocumentUrgencySeeder::class);
        $this->call(DocumentStatusSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(DocumentActionsSeeder::class);

    }
}
