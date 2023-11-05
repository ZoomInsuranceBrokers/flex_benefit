<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MapUserFYPolicy extends Model
{
    use HasFactory;
    protected $table = 'map_user_fypolicy';
    protected $fillable = [
        'external_id','map_name','map_description','fypolicy_id_fk','user_id_fk','points_used',
        'encoded_summary','is_active','created_by','modified_by'
    ];

    public function fyPolicy()
    {
        return $this->hasOne(MapFYPolicy::class, 'id', 'fypolicy_id_fk')->with('policy');
    }
}
