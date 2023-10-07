<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $csvFile = fopen(base_path("database/seeders/data/departments.csv"), "r");
        $firstline = true;
        while (($data = fgetcsv($csvFile, 2000, ",")) !== FALSE) {
            if (!$firstline) {
                $department = Department::firstOrNew(['name' => $data[1]]);
                if (!$department->exists) {
                    $department->fill(['id' => $data[0]])->save();
                }
            }
            $firstline = false;
        }

        fclose($csvFile);
    }
}
