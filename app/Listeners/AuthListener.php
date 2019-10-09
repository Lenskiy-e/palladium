<?php

namespace App\Listeners;

use App\Models\Order;
use App\Services\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Redis;
use Auth;

class AuthListener
{

    /**
     * @var int | null
     */
    private $order_id;

    /**
     * @var array
     */
    private $favorites;

    /**
     * AuthListener constructor.
     * @param Order $order
     * @param Product $product
     */
    public function __construct(Order $order, Product $product)
    {
        try
        {
            $this->order_id = $order->getOrder()->id;
        }catch(ModelNotFoundException $th)
        {
            $this->order_id = null;
        }
        $this->favorites = $product->getFavorites();
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        if($event->user)
        {
            if ($this->order_id)
            {
                Order::find($this->order_id)->update(['user_id' => $event->user->id]);
            }

            if($this->favorites)
            {
                $event->user->favorites()->detach();
                $event->user->favorites()->attach($this->favorites);
            }
        }
        Redis::set('order_id', $this->order_id);
        Redis::set('favorites', json_encode($this->favorites));
    }

}
