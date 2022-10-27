<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Auth;

class Term extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name'
    ];
    protected $appends = ['enrolled'];

    public function internships()
    {
        return $this->hasMany('App\Models\Internship');
    }

    public function getEnrolledAttribute()
    {
        return $this->internships()->where('student_id', Auth::id())->exists();
    }
}
