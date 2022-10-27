<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternshipFile extends Model
{
    use HasFactory;
    protected $fillable = [
        'file_id', 'internship_id', 'uploaded_by_id'
    ];

    public function internship()
    {
        return $this->belongsTo('App\Models\Internship', 'internship_id');
    }

    public function uploaded_by()
    {
        return $this->belongsTo('App\Models\User', 'uploaded_by_id');
    }
}
