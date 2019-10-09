<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Auth;

class Children extends Model
{

    protected $table = 'children';
    /*
     ***************
     *  Functions
     ***************
    */

    /**
     * Добавляет детей к юзеру
     * @param FormRequest $request
     */
    public static function addChildren(FormRequest $request) : void
    {
        $id = Auth::user()->id;

        self::where('user_id', $id)->delete();

        foreach ($request->child_age as $age)
        {
            self::insert(
                [
                    'age'       => (int)$age,
                    'user_id'   => $id
                ]
            );
        }
    }
}
