<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class ProfessorDetails extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'professor_details';


    protected $fillable = [

        'professor_id',
        "first_name",
        "last_name",
        "summary",
        "affiliation",
        "gender",
        "email",
        "phone",
        "address",
        "image_url",
        "education",
        "publications",
        "courses",
        "awards_and_honors",
        "external_links",
        "professional_memberships",
        "supervision",
        "news_and_media",
        'research',
        'interests',
        'courses',
        'degrees',
        'books',
        'links',
        'groups',
        'academic_titles',
        'honors',
        'websites',
        'labs',
        'related_news',
        'urls',
    ];
}

