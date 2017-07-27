<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SettingRequest;
use App\Http\Requests\UpdateSettingRequest;
use App\Http\Controllers\Controller;
use App\Setting;
use Yajra\Datatables\Datatables;

class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.pages.settings_index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view('admin.pages.settings_create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(SettingRequest $request)
    {
        $setting = Setting::create($request->all());
        $url = \LaravelLocalization::getLocalizedURL(null, ADMINPANEL . '/settings');
        return redirect()->intended($url);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $settings = Setting::where('setting_key', '=', $id)->first();

        return view('admin.pages.settings_edit', [
            'settings' => $settings
        ]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSettingRequest $request)
    {

        $url = \LaravelLocalization::getLocalizedURL(null, ADMINPANEL . '/settings');
        $setting = Setting::where('setting_key', '=', $request['setting_key_old'])->first();
        $setting->setting_value = $request['setting_value'];
        $setting->group = $request['group'];
        if ($request['setting_key_old'] != $request['setting_key']) {
            $settingCheck = Setting::where('setting_key', '=', $request['setting_key'])->first();
            if ($settingCheck instanceof Setting) {
                return redirect()->back()->withErrors(['The Setting key has already been taken.']);
            }
            $setting->setting_key = $request['setting_key'];
        }
        $setting->save();
        return redirect()->intended($url);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $setting = Setting::where('setting_key', '=', $id)->first();
        if ($setting) {
            Setting::where('setting_key', '=', $id)->delete();
        }
        $url = \LaravelLocalization::getLocalizedURL(null, ADMINPANEL . '/settings');

        return redirect()->intended($url);
    }

    public function anyData()
    {
        $settings = Setting::select(['setting_key', 'setting_value', 'group']);
        return Datatables::of($settings)->addColumn('action', function ($settings) {
            return '<a href="' . \LaravelLocalization::getLocalizedURL(null, ADMINPANEL . '/settings/' . $settings->setting_key . '/edit/') . '" class="btn btn-xs btn-primary">
                        <i class="glyphicon glyphicon-edit"></i> ' . trans('admin.edit') . '
                    </a> &nbsp;
                    <a href="#" data-id="' . $settings->setting_key . '" class="btn btn-xs btn-danger delete-setting">
                        <i class="glyphicon glyphicon-remove"></i> ' . trans('admin.delete') . '
                    </a>
                    ';
        })->make(true);
    }
}
