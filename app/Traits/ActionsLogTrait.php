<?php

namespace App\Traits;
use Auth;
use App\Models\ActionLog;
use Log;
/**
 *
 */
trait ActionsLogTrait
{

    public static function boot(){
        parent::boot();

        /*
         * записываем в лог изменения
         */
        self::saving(function($model){
            if(isset($model->attributes[$model->primaryKey])){

                $old = $model->original;
                $old_data = array();

                if($model->isDirty()){

                    foreach($model->getDirty() as $key => $value){
                        $old_data[$key] = $old[$key];
                    }

                    $data = [
                        "user" => Auth::user()->id,
                        "model" => get_class($model),
                        "model_id" => $model->attributes[$model->primaryKey],
                        "new" => json_encode($model->getDirty()),
                        "old" => json_encode($old_data)
                    ];

                    ActionLog::create($data);
                }
           }
        });
    }

    /**
     * Получение лога
     * @param $id
     * @param null $from
     * @param null $to
     * @return mixed
     */
    public function getLog($id, $from = null, $to = null){
        $data = ActionLog::where('model_id',$id)->where('model',get_class($this))->get();
        if($from){
            $data = $data->where('created_at','>=',$from);
        }

        if($to){
            $data = $data->where('created_at','<=',$to . ' 23:59:59');
        }
        return $data;
    }
}
