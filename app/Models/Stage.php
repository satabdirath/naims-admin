<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Stage extends Model {
    use HasFactory;
    protected $fillable = ['name'];

    public function leads()
    {
        return $this->hasMany(Lead::class, 'stage_id');
    }
}
