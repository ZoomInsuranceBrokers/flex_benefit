<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MapGradeCategory extends Model
{
    use HasFactory;

    protected $table = 'map_grade_category';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'external_id',
        'grade_id_fk',
        'category_id_fk',
        'amount',
        'is_active'
    ];
    /**
     * Get the comments for the blog post.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'grade_id_fk');
    }
    /**
     * Get the comments for the blog post.
     */
    public function category()
    {
        return $this->hasOne(InsuranceCategory::class,'id', 'category_id_fk')->with('subcategories');
    }
}
