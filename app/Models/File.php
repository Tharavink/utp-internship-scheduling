<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'public_id', 'type', 'uploaded_by_id'
    ];

    public function uploaded_by()
    {
        return $this->belongsTo('App\Models\User', 'uploaded_by_id');
    }
}
