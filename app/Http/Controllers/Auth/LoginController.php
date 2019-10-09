<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\AuthRequest;
use App\Services\Errors;
use App\Services\TemporaryLogin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class LoginController
 * @package App\Http\Controllers\Auth
 */
class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = "/";
    /**
     * @var TemporaryLogin
     */
    protected $temporary_login;
    /**
     * @var
     */
    protected $username;


    /**
     * LoginController constructor.
     * @param TemporaryLogin $temporary_login
     */
    public function __construct(TemporaryLogin $temporary_login)
    {
        $this->middleware('guest')->except('logout');
        $this->temporary_login = $temporary_login;
    }

    /**
     * @param AuthRequest $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function authenticate(AuthRequest $request) : Response
    {
        try
        {
            // Если логиниться по постоянному паролю
            if($request->get('login_type') === 'permanent')
            {
                // по какому полю логиниться (почта / телефон)
                $this->username = $request->get('login_by');

                try
                {
                    $this->login($request);
                }catch(ValidationException $th)
                {
                    return response()->json(['status' => 'validation error', 'message' => \Lang::get('auth.failed')]);
                }

                return response()->json(['status' => 'success']);
            }

            // Если логиниться по временному паролю
            if ($request->get('login_type') === 'dynamic')
            {
                return $this->temporary_login->login($request);
            }
        }
        catch (ValidationException $th)
        {
            return response()->json([
                'status'    => 'validation error',
                'message'   => \Lang::get('auth.failed')
            ]);
        }
        catch(\Throwable $th)
        {
            $error = new Errors($th);
            return $error->ajaxError();
        }
    }

    /**
     * Получение временного кода
     * @param AuthRequest $request
     * @return array|bool
     * @throws \Exception
     */
    public function tempCode(AuthRequest $request)
    {
        return $this->temporary_login->getCode($request);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function logout(Request $request) : Response
    {
        try
        {
            $this->guard()->logout();

            return response()->json(['status' => 'success']);
        }catch(\Throwable $th)
        {
            $error = new Errors($th);
            return $error->ajaxError();
        }
    }

    /**
     * Получение шаблона
     * @param Request $request
     * @return View
     */
    public function getView(Request $request) : View
    {
        $template = $request->get('template');

        if($template === 'login')
        {
            return view('auth.modal-login');
        }

        if($template === 'register')
        {
            return view('auth.modal-register');
        }
    }

    /**
     * Для логина по имени или почте
     * @return mixed
     */
    public function username()
    {
        return $this->username;
    }
}
