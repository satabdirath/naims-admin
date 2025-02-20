<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\Stage;
use App\Models\User;


class LeadController extends Controller {

    public function index() {
        $stages = Stage::with('leads')->get(); 
        $leads = Lead::all();
        $users = User::where('role', '!=', 'admin')->get(); // Fetch all users except super admin
    
        return view('sales', compact('stages', 'leads', 'users'));
    }
    


    public function store(Request $request) {
        $lead = Lead::create($request->all());
        return response()->json(['success' => true, 'lead' => $lead]);
    }

    public function fetchLeads()
{
    $leads = Lead::with('stage')
        ->latest() // Order by created_at descending
        ->get()
        ->map(function ($lead) {
            return [
                'id' => $lead->id,
                'name' => $lead->name,
                'created_at' => $lead->created_at,
                'stage_id' => optional($lead->stage)->id, // Send ID instead of name
                'stage_name' => optional($lead->stage)->name, // Send stage name separately
                'source' => $lead->source,
                'assigned_to' => $lead->assigned_to,
                'remember' => $lead->remember,
            ];
        });

    $stages = Stage::all(['id', 'name']); // Fetch all pipeline stages
    $users = User::all(['id', 'name']); // Fetch all users

    return response()->json([
        'leads' => $leads,
        'stages' => $stages,
        'users' => $users
    ]);
}

    

    public function pipelineView()
{
    $stages = Stage::with('leads')->get();
    return view('crm.pipeline', compact('stages'));
}


public function updateStage(Request $request)
{
    $lead = Lead::find($request->lead_id);
    if ($lead) {
        $lead->stage_id = $request->stage_id;
        $lead->save();
        return response()->json(['success' => true, 'message' => 'Stage updated successfully']);
    }
    return response()->json(['success' => false, 'message' => 'Lead not found'], 404);
}

public function updateAssigned(Request $request)
{
    $lead = Lead::find($request->lead_id);
    if ($lead) {
        $lead->assigned_to = $request->assigned_to;
        $lead->save();
        return response()->json(['success' => true, 'message' => 'Assigned user updated successfully']);
    }
    return response()->json(['success' => false, 'message' => 'Lead not found'], 404);
}

}
