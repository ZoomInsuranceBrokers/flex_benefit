<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'externl_id','fname','mname','lname','employee_id','grade','hire_date','email','points_used', 'points_available',
        'address','country_id_fk','mobile_number','salutation','title','suffix','gender','nominee_percentage','is_active',
        'created_by','modified_by', 'grade_id_fk'
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

    public function grade()
    {
        return $this->hasOne(Grade::class, 'id', 'grade_id_fk')->with('categoryMapping');
    }

    public function dependent()
    {
        return $this->hasMany(Dependent::class, 'user_id_fk', 'id');
    }

}
