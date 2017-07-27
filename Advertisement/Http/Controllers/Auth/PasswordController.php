<?php

namespace App\Http\Controllers\Auth;

use App\GetCode;
use App\Http\Controllers\Controller;
use App\Http\Controllers\SMSC_SMPP;
use App\Http\Requests\ResetPassword;
use App\Http\Requests\ResetSms;
use Illuminate\Auth\Passwords\TokenRepositoryInterface;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Password;

class PasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    protected $redirectPath = "Вход";
    protected $subject = "Ссылка для смены пароля на сайте Site.com";
    protected $address;
    protected $name;

    /**
     * Create a new password controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
        $this->address = \App\User::where('id', 1)->first()->email;
        $this->name = 'Site.com';
    }


    /**
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\Http\Response
     */
    public function getEmail()
    {

        return view('common.password.email');
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function postEmail(Request $request)
    {

        if ($request->type == 1) {
            $this->validate($request, ['email' => 'required|email']);

            $response = Password::sendResetLink($request->only('email'), function (Message $message) {

                $message->from($this->address, $this->name)->subject($this->getEmailSubject());
            });

            switch ($response) {
                case Password::RESET_LINK_SENT:
                    return redirect()->back()->with('flash_message', trans($response));

                case Password::INVALID_USER:
                    return redirect()->back()->withErrors(['email' => trans($response)]);
            }
        }
        if ($request->type == 2) {
            $this->validate($request, ['phone' => 'required']);

            $phone = preg_replace("/[^0-9+]/", '', $request->phone);

            $cod = rand(100000, 200000);

            $user = \App\User::where('tel', $phone)->first();

            if ($user) {
                $token = csrf_token();
                $getCode = GetCode::where('token', $token)->first();
                if ($getCode instanceof GetCode) {
                    $getCode->phone = $phone;
                    $getCode->cod = $cod;
                    $getCode->save();
                } else {
                    $getCode = new GetCode;
                    $getCode->phone = $phone;
                    $getCode->cod = $cod;
                    $getCode->token = $request->_token;
                    $getCode->save();
                }
                try {
                    $sms = new SMSC_SMPP();
                    $strToUser = "Code: $cod";
                    $strISO = utf8_decode($strToUser);
                    $title = utf8_decode("Site");
                    $sms->send_sms($getCode->phone, $strISO, $title);
                } catch (\Exception $e) {
                    $getCode->delete();
                    return redirect()->back()->withErrors(['phone' => 'Server Error 500!']);
                }

                return redirect('сброс-пароля-смс')->with('flash_message', 'на ваш номер отправлен секретный код');
            } else {
                return redirect()->back()->with('flash_message', 'Пользователь с данным номером не обнаружен');
            }
        }
    }

    /**
     * send reset SMS
     *
     * @param ResetSms $reset
     * @return mixed
     */
    public function postResetSms(ResetSms $reset)
    {

        $token = csrf_token();
        $getCode = GetCode::where('token', $token)->where('cod', $reset->code)->first();

        if ($getCode) {
            $user = \App\User::where('tel', $getCode->phone)->first();
            if ($user) {
                $user->password = bcrypt($reset->password);
                $user->save();
                return redirect('сброс-пароля-смс')->with('flash_message', 'Пароль успешно изменен');
            } else {
                return redirect('сброс-пароля-смс')->with('flash_message', 'Пользователь не найден');
            }
        } else {
            return redirect('сброс-пароля-смс')->with('flash_message', 'Введите правильный код');
        }
    }

    /**
     * Display the password reset view for the given token.
     *
     * @param  string $token
     * @return \Illuminate\Http\Response
     */
    public function getReset($token = null)
    {
        if (is_null($token)) {
            throw new NotFoundHttpException;
        }

        return view('common.password.reset')->with('token', $token);
    }

    /**
     * @return mixed
     */
    public function getResetSms()
    {
        $code = 1;
        return view('common.password.reset-sms')->with('code', $code);
    }


    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function postReset(ResetPassword $request)
    {
        $credentials = $request->only(
            'email', 'password', 'password_confirmation', 'token'
        );

        $response = Password::reset($credentials, function ($user, $password) {

            $this->resetPassword($user, \Hash::make($password));
        });

        switch ($response) {
            case Password::PASSWORD_RESET:
                return redirect()
                    ->back()
                    ->withFlashMessage('Пароль удачно изменен!');

            default:
                return redirect()->back()
                    ->withInput($request->only('email'))
                    ->withErrors(['email' => trans($response)]);
        }
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword $user
     * @param  string $password
     * @return void
     */
    protected function resetPassword($user, $password)
    {
        $user->password = $password;
        $user->save();
    }


}
