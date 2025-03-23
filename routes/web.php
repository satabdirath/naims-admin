<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\StageController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ChatController;
use Illuminate\Http\Request;
use App\Models\Lead;
use App\Http\Controllers\MailboxController;



Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
Route::get('/sales', [LeadController::class, 'index'])->name('sales.index');
Route::get('/contact', [LeadController::class, 'contact_index'])->name('contact.index');
Route::post('/leads/store', [LeadController::class, 'store'])->name('leads.store');
Route::post('/stages/store', [StageController::class, 'store'])->name('stages.store');
Route::get('/leads', [LeadController::class, 'fetchLeads'])->name('leads.fetch');
Route::post('/leads/update', [LeadController::class, 'updateLead'])->name('leads.update');
Route::get('/pipeline', [LeadController::class, 'pipelineView'])->name('pipeline.view');
Route::post('/leads/update-stage', [LeadController::class, 'updateStage'])->name('leads.updateStage');
Route::post('/leads/update-assigned', [LeadController::class, 'updateAssigned'])->name('leads.updateAssigned');


Route::post('/update-lead-stage', function (Request $request) {
    $lead = Lead::find($request->lead_id);
    if ($lead) {
        $lead->stage_id = $request->stage_id;
        $lead->save();
        return response()->json(['success' => true, 'message' => 'Lead stage updated']);
    }
    return response()->json(['success' => false, 'message' => 'Lead not found'], 404);
});

Route::get('/search', function (Request $request) {
    $query = $request->input('query');

    if (!$query || strlen($query) < 2) {
        return response()->json(['results' => []]);
    }

   
    $results = Lead::where('name', 'LIKE', "%$query%")
               ->orWhere('mobile_number', 'LIKE', "%$query%")
               ->orWhere('email', 'LIKE', "%$query%")
               ->get(['name', 'mobile_number', 'email']); // Fetch multiple fields

    return response()->json(['results' => $results]);
})->name('search');

Route::get('/leads/filter-by-stage', [LeadController::class, 'filterByStage'])->name('leads.filterByStage');

Route::get('/leads/filter/assigned', [LeadController::class, 'filterByAssigned'])->name('leads.filterByAssigned');
Route::get('/leads/filter/date', [LeadController::class, 'filterByDate'])->name('leads.filterByDate');
Route::post('/leads/bulk-delete', [LeadController::class, 'bulkDelete'])->name('leads.bulkDelete');
Route::post('/leads/save-details', [LeadController::class, 'saveDetails'])->name('leads.saveDetails');
Route::get('/leads/getDetails', [LeadController::class, 'getDetails'])->name('leads.getDetails');


Route::get('/onboarding', [OnboardingController::class, 'index'])->name('onboarding.index');



Route::get('/notifications', [NotificationController::class, 'fetchNotifications'])->name('notifications.fetch');
Route::post('/notifications/clear', [NotificationController::class, 'clearNotifications'])->name('notifications.clear');



Route::get('/leads/filterBySource', [LeadController::class, 'filterBySource'])->name('leads.filterBySource');


Route::post('/leads/bulkDelete', [LeadController::class, 'bulkDelete'])->name('leads.bulkDelete');
Route::post('/leads/bulkUpdateStage', [LeadController::class, 'bulkUpdateStage'])->name('leads.bulkUpdateStage');
Route::post('/leads/bulkAssign', [LeadController::class, 'bulkAssign'])->name('leads.bulkAssign');

Route::get('/leads/filterByActivity', [LeadController::class, 'filterByActivity'])->name('leads.filterByActivity');

Route::get('/leads/{id}/details', [LeadController::class, 'getLeadDetails']);

});


Route::get('/chats', [ChatController::class, 'index'])->name('chats.index');
Route::post('/send-mail', [ChatController::class, 'sendMail'])->name('send.mail');



Route::get('/emails/inbox', [MailboxController::class, 'getEmails']);
Route::get('/emails/drafts', fn() => (new MailboxController)->getEmails('Drafts')); // Adjust as needed
Route::get('/emails/templates', fn() => (new MailboxController)->getEmails('Templates'));

Route::get('/listFolders', [MailboxController::class, 'listFolders']);

require __DIR__.'/auth.php';
