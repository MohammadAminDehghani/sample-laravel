<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class University extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'country', 'city', 'rank', 'url'
    ];
    protected $seeder = [
        [
            'name'=>'Concordia',
            'city'=>'Montreal',
            'country'=>'Canada',
            'faculties' => [
                'name' => 'Electrical and Computer Engineering (ECE)',
                'university_id' => 'from prent!',
                'url' => 'https://www.concordia.ca/ginacody/electrical-computer-eng.html',
                'professors_url' => 'https://www.concordia.ca/ginacody/electrical-computer-eng/about/faculty-members.html'
            ]
        ],
        'name', 'location', 'country'
    ];

    public function faculties()
    {
        return $this->hasMany(Faculty::class);
    }



}
