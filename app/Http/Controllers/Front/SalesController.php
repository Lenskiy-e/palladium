<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\BaseController;
use App\Models\Sales;
use App\Services\Errors;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response;

class SalesController extends BaseController
{

    /**
     * @var Sales
     */
    protected $sales_instance;

    /**
     * SalesController constructor.
     * @param Sales $sales
     */
    public function __construct(Sales $sales)
    {
        parent::__construct();
        $this->sales_instance = $sales;
    }

    /**
     * @return Response
     */
    public function all() : Response
    {
        try
        {
            $now = Carbon::today()->toDateString();

            $sales = $this->sales_instance->whereDate('start', '<=', $now)->whereDate('end', '>=', $now)->whereNotNull('baner')->get();

            return response()->view('front.sales.all', ['sales' => $sales]);
        }catch(\Throwable $th)
        {
            $error = new Errors($th);
            return $error->staticError();
        }
    }

    /**
     * @param int $id
     * @return Response
     */
    public function single(int $id) : Response
    {
        try
        {
            $sale = $this->sales_instance->findOrFail($id);

            if(Carbon::parse($sale->start) > Carbon::today())
            {
                return redirect('/')->withError('Акция еще не началась');
            }

            if(Carbon::parse($sale->end) < Carbon::today())
            {
                return redirect('/')->withError('Акция закончилась');
            }

            return response()->view('front.sales.single', ['sale' => $sale]);
        }
        catch (ModelNotFoundException $error)
        {
            return redirect('/')->withError('Акция не существует');
        }
        catch(\Throwable $th)
        {
            $error = new Errors($th);
            return $error->staticError();
        }
    }
}
