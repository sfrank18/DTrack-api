<?php

namespace App\Api\Select;

use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SelectController extends Controller
{
    protected function dataSource($source) {
        $sources = [
            'divisions' => 'Division',
            'departments' => 'Department',
            'designations' => 'Designation',
            'urgencies' => 'DocumentUrgency',
            'outgoing-categories' => 'OutgoingDocumentCategory',
            'incoming-documents' => 'OutgoingDocumentCategory',
            'users' => 'User',
        ];

        return $sources[$source];
    }

    public function makeSource($source) {
        $dataSource = $this->dataSource($source);

        if (!$dataSource) {
            return response()
            ->json([
                'message' => "Source not found"
            ], 422);
        }

        $model = App::make("App\\Models\\" . $dataSource);

        return response()->json($model->select("name as label","id as value")->get(),200);
    }
}
