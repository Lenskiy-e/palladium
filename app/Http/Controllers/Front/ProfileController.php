<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\BaseController;
use App\Http\Requests\ProfileRequest;
use App\Models\Children;
use App\Models\Hobbies;
use App\Models\MarketingProfile;
use App\Services\Errors;
use App\Services\Order\getOrder;
use Auth;
use App\User;
use App\Models\Profile;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Lang;
use Log;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends BaseController
{


    /**
     * @var Profile
     */
    protected $profile_instance;

    /**
     * @var MarketingProfile
     */
    protected $marketing_profile_instance;

    /**
     * @var Hobbies
     */
    protected $hobbies_instance;

    /**
     * @var getOrder
     */
    protected $get_order;
    /**
     * ProfileController constructor.
     * @param Hobbies $hobbies
     * @param MarketingProfile $marketing_profile
     * @param Profile $profile
     */
    public function __construct(Hobbies $hobbies, MarketingProfile $marketing_profile, Profile $profile, getOrder $get_order)
    {
        parent::__construct();

        $this->profile_instance = $profile;
        $this->marketing_profile_instance = $marketing_profile;
        $this->hobbies_instance = $hobbies;
        $this->get_order = $get_order;
    }
    /**
     * @return Response
     * Показ профайла
     */
    public function index() : Response
    {
        try
        {
            $user = Auth::user();
            if(!$user)
            {
                return redirect('/')->withError("Not login in.");
            }
            $profile = $this->profile_instance->where('user_id', $user->id)->firstOrFail();

            $orders = $user->order()->with('product')->get();

            $favorites = $user->favorites()->with('product')->get();

            $order_data = $this->get_order->getArrayData('products');

            return response()->view('front.account.index', [
                'user'              => $user,
                'profile'           => $profile,
                'orders'            => $orders,
                'favorites'         => $favorites,
                'order_products'    => $order_data
            ]);
        }
        catch (ModelNotFoundException $th)
        {
            // TODO translate
            return redirect('/')->withError('No profile');
        }
        catch(\Throwable $th)
        {
            $error = new Errors($th);
            return $error->staticError();
        }
    }

    /**
     * @param $id
     * @return Response
     * Редактирование профайла
     */
    public function edit($id) : Response
    {

       try
       {
           if(!Auth::check())
           {
               throw new ModelNotFoundException('Not login in');
           }
           $user = Auth::user();
           $profile = $this->profile_instance->where('user_id', $user->id)->firstOrFail();
           $marketing = $user->marketing;
           $hobbies = $this->hobbies_instance::all();
           $active_hobbies = $user->hobbies()->select('id')->pluck('id')->toArray();

           $data = [
               'user'          => $user,
               'profile'       => $profile,
               'marketing'     => $marketing,
               'hobbies'       => $hobbies,
               'active_hobbies' => $active_hobbies
           ];

           return response()->view('front.account.edit', $data);
       }
       catch (ModelNotFoundException $th)
       {
           return redirect('/')->withError(Lang::get('common.profile_not_found'));
       }
       catch(\Throwable $th)
       {
           $error = new Errors($th);
           return $error->staticError();
       }
    }


    /**
     * @param ProfileRequest $request
     * @param $id
     * @return Response
     */
    public function update(ProfileRequest $request, $id) : Response
    {

        try{
            $this->addData($id, $request);
        }catch (\Throwable $th)
        {
            $error = new Errors($th);
            return $error->staticError();
        }

        return redirect('/profile');
    }


    /**
     * @param int $id
     * @param ProfileRequest $request
     * @return Response|null
     * Обновление данных
     */
    protected function addData(int $id, ProfileRequest $request) : ?Response
    {
        $marketing_data = ['auto' => 0, 'mailing' => 0, 'children' => 0];

        if (isset($request->auto))
        {
            $marketing_data['auto'] = 1;
        }

        if (isset($request->mailing))
        {
            $marketing_data['mailing'] = 1;
        }

        if (isset($request->children))
        {
            $marketing_data['children'] = 1;
        }


        try
        {

            $user = Auth::user();

            $user->update($request->only(['name']));

            $user->hobbies()->detach();
            $user->hobbies()->attach($request->hobbies);

            $this->profile_instance->where('user_id', $id)->update($request->only(['last_name', 'patronymic', 'birthday', 'gender']));

            if($request->child_age)
            {
                Children::addChildren($request);
            }


            $this->marketing_profile_instance->where('user_id', $id)->update($marketing_data);
        }catch(\Throwable $th)
        {
            $error = new Errors($th);
            return $error->staticError();
        }


    }
}
