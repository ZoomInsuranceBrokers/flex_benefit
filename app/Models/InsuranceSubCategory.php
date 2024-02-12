<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class InsuranceSubCategory extends Model
{
    use HasFactory;
    protected $table = 'insurance_subcategory';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'external_id','ins_category_id_fk','name','fullname',
        'description','details','has_core_multiple','core_multiple',
        'created_by','modified_by', 'is_active'
    ];

    public function categories() 
    {
        return $this->belongsTo(InsuranceCategory::class, 'ins_category_id_fk', 'id');
    }

    /*'sfdc_id','ins_category_id_fk','name','fullname',
    'description','details','has_core_multiple','core_multiple',
    'has_sum_assured','sum_assured','created_by','modified_by' */
}
