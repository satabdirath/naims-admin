<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\StageController;


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
Route::post('/leads/store', [LeadController::class, 'store'])->name('leads.store');
Route::post('/stages/store', [StageController::class, 'store'])->name('stages.store');
Route::get('/leads', [LeadController::class, 'fetchLeads'])->name('leads.fetch');
Route::post('/leads/update', [LeadController::class, 'updateLead'])->name('leads.update');
Route::get('/pipeline', [LeadController::class, 'pipelineView'])->name('pipeline.view');
Route::post('/leads/update-stage', [LeadController::class, 'updateStage'])->name('leads.updateStage');
Route::post('/leads/update-assigned', [LeadController::class, 'updateAssigned'])->name('leads.updateAssigned');



});

require __DIR__.'/auth.php';
