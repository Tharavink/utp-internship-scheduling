<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    use HasFactory;
    protected $fillable = [
        'scheduled', 'internship_id'
    ];

    public function internship()
    {
        return $this->belongsTo('App\Models\Internship', 'internship_id');
    }
}
