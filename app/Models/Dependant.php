<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dependant extends Model
{
    use HasFactory;
    protected $table = 'dependent';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'external_id',
        'dependent_name',
        'user_id_fk',
        'dependent_code',
        'dob',
        'doe',
        'gender',
        'nominee_percentage',
        'relationship_type',
        'approval_status',
        'is_active',
        'is_life_event',
        'is_deceased',
        'created_by',
        'modified_by'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id_fk','id')->select(['id','external_id as user_ext_id','fname','lname']);
    }
}
