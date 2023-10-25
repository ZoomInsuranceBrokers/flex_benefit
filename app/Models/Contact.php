<?php

namespace App\Models;

//use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\Contact as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
 use UserTrait;
 use RemindableTrait;

class Contact extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $table = 'contacts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sfdc_id','fname','mname','lname','employee_id','grade','hire_date','email',
        'address','country_id_fk','mobile_number','salutation','title',
        'suffix','gender','nominee_percentage','is_active',
        'password','created_by','modified_by'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
