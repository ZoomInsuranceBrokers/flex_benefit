<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsurancePolicy extends Model
{
    use HasFactory;
    protected $table = 'insurance_policy';

    protected $fillable = [
        'sfdc_id','name','sum_insured','ins_subcategory_id_fk','description','price_tag','points','extra_assured_amount',
        'dependent_structure','is_parent_sublimit','parent_sublimit_amount','insurer_cost','lumpsum_amount',
        'replacement_of_policy_id','replacement_of_policy_sfdc_id','currency_id_fk','is_active','created_by','modified_by',
        'si_factor', 'is_base_plan', 'is_default_selection', 'base_plan_id',
        'base_plan_id_sfdc', 'base_plan_text', 'base_plan_sum_assured_text', 'is_multi_selectable',
        'is_point_value_based', 'show_value_column'
    ];

    /*  'sfdc_id','name','sum_insured','ins_subcategory_id_fk','description','price_tag','points','extra_assured_amount',
        'dependent_structure','is_parent_sublimit','parent_sublimit_amount','insurer_cost','lumpsum_amount','replacement_of_policy_id'
        ,'replacement_of_policy_sfdc_id','currency_id_fk','is_active','created_by','modified_by', 'si_factor',
        'is_base_plan',
        'is_default_selection',
        'base_plan_id',
        'base_plan_id_sfdc',
        'base_plan_text',
        'base_plan_sum_assured_text',
        'is_multi_selectable',
        'is_point_value_based',
        'show_value_column' */

        
    public function getTableName(){
        return $this->table;
    }

    public function currency() {
        return $this->hasOne(Currency::class, 'id', 'currency_id_fk');
    }

    public function subcategory() {
        return $this->hasOne(InsuranceSubCategory::class, 'id', 'ins_subcategory_id_fk')->with('categories');
    }

    public function map_fy_policies() {
        return $this->hasManyThrough(MapFYPolicy::class, 'ins_policy_id_fk', 'id');
    }
    
}
