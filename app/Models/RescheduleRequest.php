<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RescheduleRequest extends Model
{
    use HasFactory;
    protected $fillable = [
        'requested', 'reason', 'internship_id', 'requested_by_id'
    ];

    public function internship()
    {
        return $this->belongsTo('App\Models\Internship', 'internship_id');
    }

    public function requestor()
    {
        return $this->belongsTo('App\Models\User', 'requested_by_id');
    }
}
