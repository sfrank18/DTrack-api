<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DocumentUrgency;
class DocumentUrgencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $urgencies = [
                [
                'name'=>"Urgent",
                ],
                [
                'name'=>"Non-urgent",
                ],
            ];

        foreach($urgencies as $urgency){
        
            $userRow = DocumentUrgency::firstOrNew(['name' => $urgency['name']]);

            if (!$userRow->exists) {    
                DocumentUrgency::create([
                    'name' => $urgency['name'],
                    'created_by' => null,
                ]);
            }
        }
    }
    
}
