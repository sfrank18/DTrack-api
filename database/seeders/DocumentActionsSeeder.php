<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DocumentActions;

class DocumentActionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $actions = [
            [
            'name'=>"Mark as complete",
            ],
            [
            'name'=>"Add comment",
            ],
            [
            'name'=>"Add attachment",
            ],
        ];

        foreach($actions as $action){
        
            $userRow = DocumentActions::firstOrNew(['name' => $action['name']]);

            if (!$userRow->exists) {    
                DocumentActions::create([
                    'name' => $action['name'],
                ]);
            }
        }
    }
}
