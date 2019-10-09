<?php


namespace App\Services;


use App\Models\TemporaryPassword;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Lang;
use Symfony\Component\HttpFoundation\Response;

class TemporaryLogin
{
    /**
     * @var TemporaryPassword
     */
    protected $temp_password;
    /**
     * @var
     */
    protected $user;
    /**
     * @var Request
     */
    protected $request;

    /**
     * TemporaryLogin constructor.
     * @param TemporaryPassword $temp_password
     * @param Request $request
     */
    public function __construct(TemporaryPassword $temp_password, Request $request)
    {
        $this->temp_password = $temp_password;
        $this->request = $request;
    }

    /**
     * Логин по временному паролю
     * @param Request $request
     * @return Response
     */
    public function login(Request $request) : Response
    {

        try{
            $login_by = $request->get('login_by');

            $user = $this->temp_password->select('user_id')
                ->where($request->only($login_by, 'token'))
                ->firstOrFail();

            Auth::loginUsingId($user->user_id);

            $user->delete();

            return response()->json(['status' => 'success']);
        }catch (ModelNotFoundException $th)
        {
            return response()->json(['errors' => ['token' => [Lang::get('auth.failed_temporary')] ] ],422);
        }
    }


    /**
     * Получение временного кода
     * для логина
     * @param Request $request
     * @return array|bool
     * @throws \Exception
     */
    public function getCode(Request $request)
    {
        $fillable_types = ['email', 'phone'];

        $type = $request->get('login_by');

        if (in_array($type, $fillable_types)) {
            try
            {
                $user_model = new User();
                $this->user = $user_model->select('id')->where($type, $request->get($type))->first();

                $temp_password_obj = $this->temp_password->find($this->user->id);

                switch ($type) {
                    case 'phone':
                        return $this->phone($temp_password_obj);
                        break;
                    case 'email':
                        return $this->email($temp_password_obj);
                        break;
                    default:
                        return false;
                }

            }catch (ModelNotFoundException $th)
            {
                return [
                    'status'  => 'not found',
                    'message' => 'user not found'
                ];
            }
        }else
        {
            throw new \Exception('Method not found', 1);
        }
    }

    /**
     * Получение временного кода на телефон
     * Доделать, когда будет инфа о клиенте
     * @param $temp_password_obj
     * @return array
     */
    protected function phone($temp_password_obj)
    {
        $attempts = 1;

        if($temp_password_obj)
        {

            $diffInMinutes = $temp_password_obj->created_at->diffInMinutes(Carbon::now());
            $attempts = $temp_password_obj->attempts + 1;

            if($diffInMinutes < 0)
            {
                $result = [
                    'status'  => 'timeout',
                    'message' => 'Change password after ' . (2 - $diffInMinutes) . ' minutes'
                ];

                return $result;
            }
            elseif($attempts > 10 && $temp_password_obj->created_at->diffInDays(Carbon::now()) < 1)
            {
                $result = [
                    'status'  => 'max count',
                    'message' => 'Tries limit is out, login by email'
                ];
                return $result;
            }
            else
            {
                $temp_password_obj->delete();
            }
        }
        return $this->createCode('phone', $attempts > 10 ? 1 : $attempts);
    }


    /**
     * Получение кода на почту
     * @param $temp_password_obj
     * @return array
     */
    protected function email($temp_password_obj)
    {
        $attempts = 1;
        if($temp_password_obj){
            $attempts = $temp_password_obj->attempts + 1;
            $temp_password_obj->delete();
        }
        return $this->createCode('email', $attempts);
    }

    /**
     * Записывает код в БД
     * @param $type
     * @param int $attempts
     * @return void
     * @throws \Exception
     */
    protected function createCode($type, int $attempts = 1) : void
    {

        $token = random_int(10000000,99999999);
        $login_contact = $this->request->get($type);

        $data = [
            'user_id'       => $this->user->id,
            $type           => $login_contact,
            'token'         => $token,
            'attempts'      => $attempts,
            'created_at'    => Carbon::now()
        ];
        $code = $this->temp_password->create($data);
        $result = [
            'status'  => 'created',
            'message' => $code->token
        ];

        $sender = Sender::generate($type);
        $sender->send($login_contact, $token, Lang::get('auth.login-by-' . $type));
    }
}
