<?php

namespace App\Http\Controllers\Worker;

use App\GetCode;
use App\Http\Controllers\AcResizeImage;
use App\Http\Controllers\Controller;
use App\Http\Controllers\SMSC_SMPP;
use App\Http\Requests\RegisterUserStep1;
use App\Http\Requests\RegisterUserStep2;
use App\Http\Requests\RegisterWorkerFree;
use App\Tag;
use App\User;
use App\Worker;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Image;
use Sentinel;


class RegistrationController extends Controller
{
    /**
     * First stap of registration
     *
     * @return mixed
     */
    public function userRegistration1()
    {
        return view('common.new-user-1');
    }

    /**
     * Handle of first step form
     *
     * @param RegisterUserStep1 $registerUser
     * @return mixed
     */
    public function userRegistrationPost1(RegisterUserStep1 $registerUser)
    {
        $phone = preg_replace("/[^0-9+]/", '', $registerUser->phone);
        $checkUser = User::where('tel', $phone)->first();
        if ($checkUser instanceof User) {
            return redirect()->back()->withErrors(['phone' => 'Пользователь с таким номером уже существует']);
        } else {
            $cod = rand(100000, 200000);
            $token = csrf_token();
            $getCode = $this->createSmsCode($token, $cod, $registerUser['_token'], $phone);
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
            return redirect('регистрация/шаг-2');
        }
    }

    /***
     * Create sms
     *
     * @param $token
     * @param $cod
     * @param $userToken
     * @param $phone
     * @return GetCode
     */
    protected function createSmsCode($token, $cod, $userToken, $phone)
    {
        $getCode = GetCode::where('token', $token)->first();
        if ($getCode instanceof GetCode) {
            $getCode->phone = $phone;
            $getCode->cod = $cod;
            $getCode->save();
        } else {
            $getCode = new GetCode;
            $getCode->phone = $phone;
            $getCode->cod = $cod;
            $getCode->token = $userToken;
            $getCode->save();
        }
        return $getCode;
    }

    /**
     * Second step
     *
     * @return mixed
     */
    public function userRegistration2()
    {
        $token = csrf_token();
        $code = GetCode::where('token', $token)->first();
        if ($code) {
            return view('common.new-user-2')->with('code', $code->cod);
        } else {
            return redirect('регистрация/шаг-1');
        }
    }

    /**
     * handle second step
     *
     * @param RegisterUserStep2 $registerUser
     * @return mixed
     */
    public function userRegistrationPost2(RegisterUserStep2 $registerUser)
    {
        $token = csrf_token();
        $getCode = GetCode::where('token', $token)->where('cod', $registerUser->codGet)->first();
        if ($getCode) {
            $createUser = $this->cteateUser($registerUser, $getCode);
            if ($createUser['error']) {
                return redirect()->back()->withErrors(['common' => $createUser['msg']]);
            } else {
                return redirect('кабинет');
            }
        } else {
            return redirect()->back()->withErrors(['codGet' => 'Введите правильный код']);
        }
    }

    /**
     * Create user
     *
     * @param $registerUser
     * @param $getCode
     * @return array
     */
    protected function cteateUser($registerUser, $getCode)
    {
        try {
            DB::transaction(function () use ($registerUser, $getCode) {
                $password = $registerUser->password;
                $email = $registerUser->email;
                $workerUser = Sentinel::registerAndActivate([
                    'tel' => $getCode['phone'],
                    'email' => $email,
                    'password' => $password
                ]);
                $workerRole = Sentinel::findRoleByName('Worker');
                $workerRole->users()->attach($workerUser);
                Sentinel::login($workerUser);
            });
            $msg = ['error' => 0];
        } catch (\Exception $e) {
            dd($e->getMessage());
            $msg = ['error' => 1, 'msg' => $e->getMessage()];
        }
        return $msg;
    }

    /**
     * Create profile
     *
     * @return mixed
     */
    public function createProfile()
    {
        $user = Sentinel::check();
        if (!$user->worker) {
            $popularCities = $this->popularCities();
            $categories = $this->getAllCat();
            return view('common.create-profile')->with('popularCities', $popularCities)
                ->with('categories', $categories);
        } else {
            return redirect('кабинет');
        }
    }



    /**
     * Handle Profile form
     * @param RegisterWorkerFree $newWorker
     * @return mixed
     */
    public function createProfilePost(RegisterWorkerFree $newWorker)
    {
        $user = Sentinel::check();
        if ($user) {
            try {
                DB::transaction(function () use ($user, $newWorker) {
                    $oldWorker = Worker::where('user_id', $user->id)->first();
                    if ($oldWorker instanceof Worker) {
                        return redirect('/создание-кабинета');
                    } else {
                        $worker = $this->createWorker($user->id, $newWorker);
                        $worker->cities()->attach([$newWorker['pcity']]);
                        $this->createWorkerSubCategories($newWorker['spec'], $worker);
                        $this->createWorkerProffesion($newWorker['prof'], $worker->id);
                    }
                });
                return redirect('исполнитель/' . $user->worker->id);
            } catch (\Exception $e) {
                \DB::rollback();
                return redirect('/создание-кабинета');
            }
        } else {
            return redirect('/создание-кабинета');
        }
    }

    /**
     * Create Worker
     *
     * @param $id
     * @param $newWorker
     * @return Worker
     */
    protected function createWorker($id, $newWorker)
    {
        $worker = new Worker;
        $worker->user_id = $id;
        $worker->first_name = $newWorker['fname'];
        $worker->description = $newWorker['oinfo'];
        $worker->show = 1;
        if ($newWorker['avatar']) {
            $path = public_path('avatars/' . $id);
            if (!File::exists($path)) {
                File::makeDirectory($path, 0775, true);
            } else {
                File::cleanDirectory($path);
            }
            $imgNew = Image::make($newWorker->file('avatar'))->save($path . '/temp.jpg');
            $img = new AcResizeImage($path . '/temp.jpg');
            if ($imgNew->height() != 120 || $imgNew->width() != 120) {
                $img->resize(120, 120);
            }
            $img->save($path . '/', 'avatar', 'jpg', true, 100);
            $worker->avatar_path = '/avatars/' . $id . '/avatar.jpg';
            File::delete($path . '/temp.jpg');
            $worker->avatar_path = '/avatars/' . $id . '/avatar.jpg';
        } else {
            $worker->avatar_path = '/avatars/common_avatar.jpg';
        }
        $worker->save();
        return $worker;
    }



}
