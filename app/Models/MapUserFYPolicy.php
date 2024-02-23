<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MapUserFYPolicy extends Model
{
    use HasFactory;
    protected $table = 'map_user_fypolicy';
    protected $fillable = [
        'external_id','map_name','map_description','fypolicy_id_fk','user_id_fk','points_used','selected_dependent',
        'encoded_summary','is_active','created_by','modified_by'
    ];

    public function getTableName(){
        return $this->table;
    }

    public function fyPolicy()
    {
        return $this->hasOne(MapFYPolicy::class, 'id', 'fypolicy_id_fk')->with('policy');
    }

    public function user()
    {
        //return $this->hasOne(User::class, 'id', 'user_id_fk')->with('dependant');
        return $this->belongsTo(User::class, 'user_id_fk','id')->with('dependant');
    }
    public function userCompact()
    {
        return $this->belongsTo(User::class, 'user_id_fk','id')->select(['id','fname','lname']);
    }
}
