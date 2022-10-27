<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Internship extends Model
{
    use HasFactory;
    protected $fillable = [
        'title', 'description', 'student_id', 'supervisor_id', 'assigned_by_id', 'term_id', 'scheduled', 'host_email', 'student_contact',
        'host_address', 'is_completed', 'intern_term'
    ];

    public function term()
    {
        return $this->belongsTo('App\Models\Term', 'term_id')->withTrashed();
    }

    public function supervisor()
    {
        return $this->belongsTo('App\Models\User', 'supervisor_id')->withTrashed();
    }

    public function student()
    {
        return $this->belongsTo('App\Models\User', 'student_id');
    }

    public function assigned_by()
    {
        return $this->belongsTo('App\Models\User', 'assigned_by_id');
    }

    public function requests()
    {
        return $this->hasMany('App\Models\RescheduleRequest');
    }
}
