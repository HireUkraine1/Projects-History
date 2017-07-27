<?php

namespace App\Http\Controllers\Worker;

use App\Http\Controllers\Controller;
use App\Http\Requests\WorkerSignIn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Sentinel;

class WorkerSession extends Controller
{
    /**
     * Sign in
     * @return string
     */
    public function signInPost()
    {
        try {
            $input = Input::all();

            if (!$input['login'] || !$input['password']) {
                return json_encode(['error' => 1, 'msg' => 'Поле пароль и логин обязательны']);
            } else {
                $authenticateUser = $this->typeLogin($input);
                try {
                    if ($authenticateUser) {
                        $role = $authenticateUser->roles()
                            ->get(['name'])
                            ->map(function ($role) {
                                return $role->name;
                            })
                            ->toArray();
                        $worker = in_array('Worker', $role);
                        if (!$worker) {
                            Sentinel::logout($authenticateUser);
                            return json_encode(['error' => 1, 'msg' => 'Вход в админ панель <a href="/админ-панель/войти">войти</a>']);
                        }
                        return json_encode(['error' => 0]);
                    }
                    return json_encode(['error' => 1, 'msg' => 'Неправильный Логин или Пароль!']);

                } catch (\Cartalyst\Sentinel\Checkpoints\NotActivatedException $e) {
                    return json_encode(['error' => 1, 'msg' => 'Пользователь не активирован или заблокирован.']);
                } catch (\Cartalyst\Sentinel\Checkpoints\ThrottlingException $e) {
                    return json_encode(['error' => 1, 'msg' => $e->getMessage()]);
                }
            }
        } catch (\Exception $e) {
            return json_encode(['error' => 1, 'msg' => $e->getMessage()]);
        }
    }

    /**
     * User's type
     *
     * @param $input
     * @return mixed
     */
    protected function typeLogin($input)
    {
        if (filter_var($input['login'], FILTER_VALIDATE_EMAIL)) {
            $authenticateUser = Sentinel::authenticate([
                'email' => $input['login'],
                'password' => $input['password'],
            ]);
        } else {
            $phone = preg_replace("/[^0-9+]/", '', $input['login']);
            $authenticateUser = Sentinel::authenticate([
                'login' => $phone,
                'password' => $input['password'],
            ]);
        }
        return $authenticateUser;
    }

    /**
     * Log out
     *
     * @return mixed
     */
    public function goOut()
    {
        Sentinel::logout();
        return redirect()->intended('/');
    }
}
