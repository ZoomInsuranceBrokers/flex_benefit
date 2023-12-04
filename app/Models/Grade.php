<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;
    protected $table = 'grade';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'external_id',
        'grade_name',
        'is_active',
        'created_by',
        'modified_by'
    ];
    /**
     * Get the comments for the blog post.
     */
    public function categoryMapping()
    {
        return $this->hasMany(MapGradeCategory::class,'grade_id_fk', 'id')->with('category');
    }
}
