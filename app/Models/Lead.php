<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; 

class Lead extends Model {
    use HasFactory;


    protected $fillable = ['name', 'mobile_number', 'email', 'address', 'status', 'assigned_to', 'stage_id','source', 'note','task', 'property_name' , 'pincode' , 'home_type' , 'budget', 'call_log', 'mail_log', 'whatsapp_log','meeting_log' , 'phone'];


    public function stage()
    {
        return $this->belongsTo(Stage::class, 'stage_id'); 
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to'); 
    }

    
}
