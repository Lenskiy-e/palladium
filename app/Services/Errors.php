<?php


namespace App\Services;

use Log;
use Symfony\Component\HttpFoundation\Response;

class Errors
{

    /**
     * @var \Throwable
     */
    protected $error;

    /**
     * Errors constructor.
     * @param \Throwable $error
     */
    public function __construct(\Throwable $error)
    {
        $this->error = $error;
        $log = $this->writeLog();
        if(!$log)
        {
            abort(500);
        }
    }

    /**
     * @return Response
     * Для ответа
     */
    public function staticError() : Response
    {
        abort(500);
    }

    /**
     * @return Response
     * Для ответа на аякс запросы
     */
    public function ajaxError() : Response
    {
        return response()->json([
            'status'    => 'error',
            'message'   => $this->error->getMessage()
        ]);
    }

    /**
     * @return bool
     */
    protected function writeLog() : bool
    {
        $error = Log::error($this->error->getMessage() . ' LINE ' .$this->error->getLine());
        $debug = Log::debug($this->error->getTraceAsString());

        return !(!$error || !$debug);
    }
}
