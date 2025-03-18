<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Lead;
use App\Models\Stage;
use App\Models\User;


class LeadController extends Controller {

    public function index() {
        $stages = Stage::with('leads')->get(); 
        $leads = Lead::all();
        $users = User::where('role', '!=', 'admin')->get(); // Fetch all users except super admin
        
        // Fetch lost leads where stage_id is 0
        $lostLeads = Lead::where('stage_id', 0)->get();
        
        $sources = Lead::select('source')->distinct()->pluck('source');
    
        return view('sales', compact('stages', 'leads', 'users', 'lostLeads', 'sources'));
    }
    
    

    public function contact_index() {
        $stages = Stage::with('leads')->get(); 
        $leads = Lead::all();
        $users = User::where('role', '!=', 'admin')->get(); // Fetch all users except super admin
        $lostLeads = Lead::where('stage_id', 'lost')->get();

    
        return view('contact', compact('stages', 'leads', 'users','lostLeads'));
    }
    

public function store(Request $request) {
    try {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'mobile_number' => 'required|string|max:15|unique:leads,mobile_number',
            'email' => 'nullable|email|max:255|unique:leads,email',
            'address' => 'nullable|string|max:255',
        ]);

        $lead = Lead::create($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Lead added successfully!',
            'lead' => $lead
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
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
                'stage_id' => optional($lead->stage)->id ?? 0, // If no stage, mark as lost (0)
                'stage_name' => optional($lead->stage)->name ?? 'Lost', // If no stage, label as 'Lost'
                'source' => $lead->source,
                'assigned_to' => $lead->assigned_to,
                'remember' => $lead->task,
            ];
        });

    // Fetch only stages with IDs between 1 and 6
    $stages = Stage::whereBetween('id', [1, 6])->get(['id', 'name']);

    $users = User::all(['id', 'name']); // Fetch all users

    // Fetch unique sources from leads, ignoring NULL values
    $sources = Lead::whereNotNull('source')->distinct()->pluck('source');

    // Separate lost leads (stage_id = 0)
    $lostLeads = $leads->where('stage_id', 0)->values(); // Get only lost leads
    $activeLeads = $leads->where('stage_id', '!=', 0)->values(); // Get active leads

    return response()->json([
        'leads' => $activeLeads, // Return only active leads in main list
        'lostLeads' => $lostLeads, // Return lost leads separately
        'stages' => $stages,
        'users' => $users,
        'sources' => $sources
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
        // Always update the stage_id
        $lead->stage_id = (int) $request->stage_id;

        // If moved to Lost stage, update only the status
        if ($request->status == "Lost") {
            $lead->status = "Lost";
        }

        $lead->save();

        return response()->json(['success' => true, 'message' => 'Lead updated successfully']);
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



public function filterByStage(Request $request)
{
    $stages = $request->input('stages', []);

    if (empty($stages)) {
        // Return all stages (1 to 6) and all leads with relationships
        $filteredStages = Stage::whereBetween('id', [1, 6])->with('leads')->get();
        $leads = Lead::whereBetween('stage_id', [1, 6])->with(['stage', 'assignedTo'])->get();
    } else {
        // Apply filtering and eager-load stage & assignedTo
        $filteredStages = Stage::whereIn('id', $stages)
            ->whereBetween('id', [1, 6])
            ->with('leads')
            ->get();
        
        $leads = Lead::whereIn('stage_id', $stages)
            ->whereBetween('stage_id', [1, 6])
            ->with(['stage', 'assignedTo']) // Ensure relationships are loaded
            ->get();
    }

    return response()->json([
        'stages' => $filteredStages,
        'leads' => $leads
    ]);
}

public function filterBySource(Request $request)
{
    $sources = $request->input('sources', []);

    if (empty($sources)) {
        // Fetch all leads & stages
        $leads = Lead::with(['stage', 'assignedTo'])->get();
        $stages = Stage::whereBetween('id', [1, 6])->with('leads')->get();
    } else {
        // Filter leads by selected sources
        $leads = Lead::whereIn('source', $sources)
            ->with(['stage', 'assignedTo'])
            ->get();

        // Fetch only stages that have leads from selected sources
        $stages = Stage::whereHas('leads', function ($query) use ($sources) {
                $query->whereIn('source', $sources);
            })
            ->whereBetween('id', [1, 6])
            ->with('leads')
            ->get();
    }

    return response()->json([
        'leads' => $leads,
        'stages' => $stages
    ]);
}


public function filterByAssigned(Request $request)
{
    $assignedTo = $request->input('assigned_to', []);

    if (empty($assignedTo)) {
        // If no users selected, return all leads and stages (1 to 6)
        $leads = Lead::whereBetween('stage_id', [1, 6])
            ->with(['stage', 'assignedTo'])
            ->get();

        $stages = Stage::whereBetween('id', [1, 6])
            ->with('leads')
            ->get();
    } else {
        // Filter leads by assigned users, within stages 1 to 6
        $leads = Lead::whereIn('assigned_to', $assignedTo)
            ->whereBetween('stage_id', [1, 6])
            ->with(['stage', 'assignedTo'])
            ->get();

        // Get stages with assigned leads
        $stages = Stage::whereBetween('id', [1, 6])
            ->with(['leads' => function ($query) use ($assignedTo) {
                $query->whereIn('assigned_to', $assignedTo);
            }])->get();
    }

    return response()->json([
        'leads' => $leads,
        'stages' => $stages
    ]);
}


public function filterByDate(Request $request)
{
    $date = $request->input('date');

    if (!$date) {
        return response()->json(['error' => 'Invalid date provided'], 400);
    }

    try {
        $formattedDate = Carbon::parse($date)->format('Y-m-d');
    } catch (\Exception $e) {
        return response()->json(['error' => 'Invalid date format'], 400);
    }

    // Get leads created on the selected date, within stage 1 to 6
    $leads = Lead::whereDate('created_at', $formattedDate)
        ->whereBetween('stage_id', [1, 6]) // Ensure leads belong to stages 1 to 6
        ->with('stage', 'assignedTo')
        ->get();

    // Get stages 1 to 6 and their leads filtered by date
    $stages = Stage::whereBetween('id', [1, 6])
        ->with(['leads' => function ($query) use ($formattedDate) {
            $query->whereDate('created_at', $formattedDate);
        }])
        ->get();

    return response()->json([
        'leads' => $leads,
        'stages' => $stages
    ]);
}


public function bulkDelete(Request $request)
{
    $leadIds = $request->input('leads', []);

    if (empty($leadIds)) {
        return response()->json(['error' => 'No leads selected'], 400);
    }

    Lead::whereIn('id', $leadIds)->delete();

    return response()->json(['message' => 'Selected leads deleted successfully']);
}

public function bulkUpdateStage(Request $request)
{
    Lead::whereIn('id', $request->leads)->update(['stage_id' => $request->stage_id]);
    return response()->json(['message' => 'Selected leads have been updated to the new stage.']);
}

public function bulkAssign(Request $request)
{
    Lead::whereIn('id', $request->leads)->update(['assigned_to' => $request->user_id]);
    return response()->json(['message' => 'Selected leads have been assigned successfully.']);
}


public function saveDetails(Request $request)
{
    $request->validate([
        'lead_id' => 'required|exists:leads,id',
        'note' => 'nullable|string',
        'task' => 'nullable|string',
    ]);

    $lead = Lead::findOrFail($request->lead_id);
    $lead->note = $request->note;
    $lead->task = $request->task;
    $lead->save();

    return response()->json(['message' => 'Lead details saved successfully!']);
}


public function getDetails(Request $request)
{
    $lead = Lead::find($request->lead_id);

    if (!$lead) {
        return response()->json(['error' => 'Lead not found'], 404);
    }

    return response()->json([
        'note' => $lead->note,  // Assuming you have these columns in leads table
        'task' => $lead->task
    ]);
}






public function filterByActivity(Request $request)
{
    $activity = $request->input('activity');

    // ✅ Query leads within stages 1 to 6
    $query = Lead::whereBetween('stage_id', [1, 6]);

    switch ($activity) {
        case 'today':
            $query->whereDate('created_at', Carbon::today());
            break;
        case 'this_week':
            $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
            break;
        case 'last_week':
            $query->whereBetween('created_at', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()]);
            break;
        case 'this_month':
            $query->whereMonth('created_at', Carbon::now()->month)
                  ->whereYear('created_at', Carbon::now()->year);
            break;
        case 'last_month':
            $query->whereMonth('created_at', Carbon::now()->subMonth()->month)
                  ->whereYear('created_at', Carbon::now()->subMonth()->year);
            break;
    }

    // ✅ Fetch filtered leads with relations
    $leads = $query->with(['stage', 'assignedTo'])->get();

    // ✅ Fetch only the stages 1 to 6 and include only filtered leads
    $stages = Stage::whereBetween('id', [1, 6])
        ->with(['leads' => function ($query) use ($activity) {
            // ✅ Apply the same activity filter to leads inside stages
            switch ($activity) {
                case 'today':
                    $query->whereDate('created_at', Carbon::today());
                    break;
                case 'this_week':
                    $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    break;
                case 'last_week':
                    $query->whereBetween('created_at', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()]);
                    break;
                case 'this_month':
                    $query->whereMonth('created_at', Carbon::now()->month)
                          ->whereYear('created_at', Carbon::now()->year);
                    break;
                case 'last_month':
                    $query->whereMonth('created_at', Carbon::now()->subMonth()->month)
                          ->whereYear('created_at', Carbon::now()->subMonth()->year);
                    break;
            }
            $query->whereBetween('stage_id', [1, 6]); // ✅ Ensure only stages 1-6 are included
        }])->get();

    return response()->json([
        'leads' => $leads,
        'stages' => $stages
    ]);
}



public function getLeadDetails($id)
{
    $lead = Lead::with('stage')->find($id);

    if (!$lead) {
        return response()->json(['success' => false, 'message' => 'Lead not found'], 404);
    }

    return response()->json([
        'success' => true,
        'lead' => $lead
    ]);
}


}
