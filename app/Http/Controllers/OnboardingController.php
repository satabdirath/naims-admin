<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\Stage;
use App\Models\User;


class OnboardingController extends Controller {

    public function index() {
        $stages = Stage::with('leads')->get(); 
        $leads = Lead::all();
        $users = User::where('role', '!=', 'admin')->get(); // Fetch all users except super admin
         $sources = Lead::select('source')->distinct()->pluck('source');
    
        return view('onboarding', compact('stages', 'leads', 'users','sources'));


    }
    


  

}
