<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MapFYPolicy extends Model
{
    use HasFactory;
    protected $table = 'map_financial_year_policy';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sfdc_id','map_name','map_description', 'fy_id_fk', 'ins_policy_id_fk','is_active','created_by','modified_by'
    ];

    public function financialYears()
    {
        return $this->hasOne(FinancialYear::class, 'id', 'fy_id_fk');
    }

    public function policy()
    {
        return $this->hasOne(InsurancePolicy::class, 'id', 'ins_policy_id_fk')->with('currency');
    }

    
}
