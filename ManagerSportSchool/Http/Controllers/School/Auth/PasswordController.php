<?php

namespace App\Http\Controllers\School\Auth;

use App\Http\Controllers\Controller;
use App\Models\School;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\sportport\Str;
use Mail;
use Validator;

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

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function getEmail()
    {
        return view('school.auth.passwords.email');
    }

    public function postEmail(Request $request)
    {
        $school = School::where('email', $request->email)->first();
        if ($school) {
            //send mail
            $token = Str::random(60);

            \DB::table('school_password_resets')->insert(
                ['email' => $request->email, 'token' => $token, 'created_at' => Carbon::now()]
            );

            Mail::send('school.auth.emails.password', ['email' => $request->email, 'token' => $token], function ($message) use ($school) {
                $message->to($school->email, $school->schoolName())->subject('Reset password.');
            });
            return back()->with('status', 'Reset link sent. Please check your mail');
        } else {
            // send mail not found
            return back()->withErrors(['email' => 'Email not found']);
        }
    }

    public function showResetForm(Request $request, $token = null)
    {
        return view('school.auth.passwords.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $school = School::where('email', $request->email)->first();
        $school_reset = \DB::table('school_password_resets')->where('email', $request->email)->where('token', $request->token)->orderBy('created_at', 'desc')->first();

        if (isset($school) && $school_reset->token == $request->token) {
            $school->forceFill([
                'password' => bcrypt($request->password),
                'remember_token' => Str::random(60),
            ])->save();
            \DB::table('school_password_resets')->where('email', $request->email)->where('token', $request->token)->delete();
            return redirect('/school/login')->with('status', 'Password changed');
        } else {
            return back()->withErrors(['email' => 'Check your credentials']);
        }
    }

}
