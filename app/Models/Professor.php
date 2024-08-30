<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Professor extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name', 'last_name', 'affiliation', 'gender', 'email', 'phone', 'url', 'url_response',  'address', 'department_id'
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function faculty()
    {
        return $this->hasOneThrough(Faculty::class, Department::class);
    }

    public function university()
    {
        return $this->hasOneThrough(University::class, Department::class);
    }

    public function interests()
    {
        return $this->hasMany(Interest::class);
    }
}
