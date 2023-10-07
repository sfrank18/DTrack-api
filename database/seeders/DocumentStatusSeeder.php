<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DocumentStatus;

class DocumentStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $statuses = [
                [
                'name'=>"New Document",
                ],
                [
                'name'=>"Routing",
                ],
                [
                'name'=>"Completed",
                ],
            ];

        foreach($statuses as $status){
        
            $userRow = DocumentStatus::firstOrNew(['name' => $status['name']]);

            if (!$userRow->exists) {    
                DocumentStatus::create([
                    'name' => $status['name'],
                    'created_by' => null,
                ]);
            }
        }
    }
}
