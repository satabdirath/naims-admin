<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; 

class Lead extends Model {
    use HasFactory;

    // Define the fillable attributes to protect against mass assignment
    protected $fillable = ['name', 'mobile_number', 'email', 'address', 'status', 'assigned_to', 'stage_id','source', 'note'];


    public function stage()
    {
        return $this->belongsTo(Stage::class, 'stage_id'); // Correct relation
    }

    
}
