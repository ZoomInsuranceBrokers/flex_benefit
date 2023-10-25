<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InsuranceCategory extends Model
{
    use HasFactory;
    protected $table = 'insurance_category';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sfdc_id',
        'name',
        'contact_id_fk',
        'tagline',
        'sequence',
        'is_active',
        'created_by',
        'modified_by'
    ];
    /**
     * Get the comments for the blog post.
     */
    public function subcategories()
    {
        return $this->hasMany(InsuranceSubCategory::class,'ins_category_id_fk', 'id');
    }


}
