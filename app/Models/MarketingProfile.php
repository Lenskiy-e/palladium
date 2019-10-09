<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketingProfile extends Model
{

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'marketing_profiles';
    protected $primaryKey = 'user_id';
    protected $fillable = [
        'user_id', 'auto', 'children',
        'discount', 'discount_balance',
        'discount_spent', 'mailing'
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

}
