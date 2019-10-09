<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Carbon\Carbon;

class TemporaryPassword extends Model
{
    protected $table = 'temporary_passwords';

    protected $fillable = ['user_id','token', 'phone', 'email' ,'attempts' ,'created_at'];

    protected $dates = ['created_at'];

    protected $primaryKey = "user_id";

    public $timestamps = false;
}
