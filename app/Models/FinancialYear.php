<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialYear extends Model
{
    use HasFactory;
    protected $table = 'financial_years';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'external_id','name','start_date','end_date','last_enrollment_date','future_fy_year_fk','prev_fy_year_fk','is_active','created_by','modified_by'
    ];

    //'sfdc_id','name','start_date','end_date','future_fy_year_fk','prev_fy_year_fk','is_active','created_by','modified_by'
}
