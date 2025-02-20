<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stage;

class StageController extends Controller {
    public function store(Request $request) {
        $stage = Stage::create($request->all());
        return response()->json(['success' => true, 'stage' => $stage]);
    }
}
