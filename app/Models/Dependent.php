<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dependent extends Model
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
        'gender',
        'nominee_percentage',
        'relationship_type',
        'approval_status',
        'is_active',
        'is_deceased',
        'created_by',
        'modified_by'
    ];

    // public function contact(): BelongsTo
    // {
    //     return $this->belongsTo(Contact::class, 'contact_id_fk');
    // }
}
