<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Professor extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name', 'last_name', 'email', 'phone', 'room_number', 'faculty_id'
    ];

    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    public function interests()
    {
        return $this->hasMany(Interest::class);
    }
}
