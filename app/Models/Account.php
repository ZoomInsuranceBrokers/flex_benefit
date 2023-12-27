<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;    
    protected $table = 'accounts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'external_id',
        'name',
        'address',
        'country_id_fk',
        'mobile_number',
        'enrollment_start_date',
        'enrollment_end_date',
        'created_by',
        'modified_by'
    ];
}
