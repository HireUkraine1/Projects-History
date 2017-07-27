<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Mail;
use App\Model;

class SettingsController extends CommonController
{

    /**
     * Display a listing of the setting.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if ($this->admin->role_id !== 1) {
            abort(403);
        }
        $data = [
            'fee' => Model\Setting::where('slug', 'fee')->first()->value,
            'email' => Model\Setting::where('slug', 'email')->first()->value,
            'users' => Model\ExistUser::all()
        ];

        return view('admin.settings.index')->with('data', $data);
    }

    /**
     * Update setting
     * @param Requests\AdminSettings $request
     * @return mixed
     */
    public function update(Requests\AdminSettings $request)
    {
        if ($this->admin->role_id !== 1) {
            abort(403);
        }
        try {
            $fee = Model\Setting::where('slug', 'fee')->first();
            $email = Model\Setting::where('slug', 'email')->first();
            $fee->value = number_format((float)$request->fee, 2, '.', '');
            $fee->save();
            $email->value = $request->email;
            $email->save();
            \Session::flash('message', ['alert-success' => \Lang::get('messages.success')]);
        } catch (\Exception $e) {
            \Session::flash('message', ['alert-danger' => $e->getMessage()]);
        } finally {
            return redirect('/admin/settings');
        }
    }

}
