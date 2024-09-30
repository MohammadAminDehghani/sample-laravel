<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;


use Tymon\JWTAuth\Contracts\JWTSubject;


//class User extends Authenticatable
class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'admin',
        'remember_token',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Automatically hash the password when saving.
     *
     * @param string $password
     */
    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Hash::make($password);
    }

    /**
     * Compare the user's password.
     *
     * @param string $password
     * @return bool
     */
    public function comparePassword($password)
    {
        return Hash::check($password, $this->password);
    }

    /**
     * Set remember token manually.
     *
     * @param string $token
     */
    public function setRememberToken($token)
    {
        $this->attributes['remember_token'] = $token;
    }

    /**
     * Check if the user is a VIP.
     *
     * @return bool
     */
    public function isVip()
    {
        return false; // Update this as needed
    }


    /**
     * Get the identifier that will be stored in the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey(); // Typically returns the user's ID
    }

    /**
     * Return a key-value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return []; // Add custom claims here if needed
    }
}
