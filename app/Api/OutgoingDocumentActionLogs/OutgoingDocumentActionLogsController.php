<?php

namespace App\api\OutgoingDocumentActionlogs;

use App\Http\Controllers\CrudController;
use App\Models\OutgoingDocumentActionLog;
use Illuminate\Http\Request;

class OutgoingDocumentActionLogsController extends CrudController
{
    //
    public function __construct()
    {
        $model = new OutgoingDocumentActionLog();
        $queryModel = $this->model;
        $validation = [
            'rules'=>[],
            'message'=>[],
            'attribute'=>[]
        ];
    }

    public function index(Request $request) :object{
        return response()->json();
    }
}
