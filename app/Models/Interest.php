<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interest extends Model
{
    use HasFactory;

    protected $fillable = [
        'professor_id', 'interest'
    ];

    public function professor()
    {
        return $this->belongsTo(Professor::class);
    }
}
