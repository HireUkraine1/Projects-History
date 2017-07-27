<?php

class SettingsController extends BaseController
{
    protected $layout = 'admin.layouts.default';
    protected $conf = '';

    public function settings_dashboard()
    {
        if ($_POST):
            $newSettings = Input::get();
            foreach ($newSettings as $string_id => $value):
                $settingsRow = SiteSetting::where('string_id', $string_id)->first();
                $settingsRow->string_id = $string_id;
                $settingsRow->value = $value;
                $settingsRow->save();
            endforeach;
            Session::flash('message', "Saved OK!");
            return Redirect::to('/saleadminpanel/settings');
        endif;
        $settings = SiteSetting::whereNotIn('label', ['twitter', 'google', 'facebook'])->get();
        $this->layout->customjs = View::make('admin.settings.cssjs');
        $this->layout->content = View::make('admin.settings.settings', ['settings' => $settings]);
        $this->layout->sidebar = View::make('admin.settings.sidebar');
        $this->layout->mainmenu = View::make('admin.mainmenu');
    }

    public function settings_redirects()
    {
        $this->layout->customjs = View::make('admin.settings.cssjs');
        $redirects = DB::table('active_redirects')->paginate(30);
        $this->layout->content = View::make('admin.settings.redirects', array('redirects' => $redirects));
    }

    public function settings_buffer()
    {
        if ($_POST):
            $newSettings = Input::get();
            foreach ($newSettings as $string_id => $value):
                $settingsRow = SiteSetting::where('string_id', $string_id)->first();
                if (is_object($settingsRow)):
                    $settingsRow->value = $value;
                    $settingsRow->save();
                endif;
            endforeach;
            Session::flash('message', "Saved OK!");
            return Redirect::to('saleadminpanel/settings/buffer');
        endif;
        $settings = SiteSetting::wherelabel('twitter')->orWhere('label', 'LIKE', "google")->orWhere('label', 'LIKE', "facebook")->get();
        $this->layout->customjs = View::make('admin.settings.cssjs');
        $this->layout->content = View::make('admin.settings.settings_buffer', ['settings' => $settings]);
        $this->layout->sidebar = View::make('admin.settings.sidebar');
        $this->layout->mainmenu = View::make('admin.mainmenu');
    }

    public function settings_users()
    {
        $users = AdminUser::with('permission.description')->get();
        $permissions = Permission::get();
        $this->layout->customjs = View::make('admin.settings.cssjs');
        $this->layout->content = View::make('admin.settings.admins-roles', ['users' => $users, 'permissions' => $permissions]);
        $this->layout->sidebar = View::make('admin.settings.sidebar');
        $this->layout->mainmenu = View::make('admin.mainmenu');
    }

    public function post_settings_users()
    {
        if (Input::has('createAdmin')):
            $formField = Input::only('username', 'display_name', 'password');
            $psw = Hash::make($formField['password']);
            $admin = new AdminUser;
            $admin->role = 1;
            $admin->username = $formField['username'];
            $admin->display_name = $formField['display_name'];
            $admin->status = 1;
            $admin->password = $psw;
            $admin->save();
            if (Input::has('permissionId')):
                foreach (Input::get('permissionId') as $value):
                    $adminPermissions = new AdminPermission;
                    $adminPermissions->permission_id = $value;
                    $adminPermissions->admin_id = $admin->id;
                    $adminPermissions->save();
                endforeach;
            endif;
            return Redirect::to('saleadminpanel/settings/users');
        endif;

        if (Input::has('changeAdmin')):
            $admin = AdminUser::whereBetween('id', array(8, 9))->delete();
            $admin = AdminUser::whereid(Input::get('adminId'))->first();
            $admin->username = Input::get('username');
            $admin->display_name = Input::get('display_name');
            if (Input::has('password')):
                $psw = Hash::make(Input::get('password'));
                $admin->password = $psw;
            endif;
            $admin->save();
            $formPermissions = (Input::get('permissionId')) ? Input::get('permissionId') : array();
            $allPermissions = Permission::get();
            foreach ($allPermissions as $idPermission):
                if (!in_array($idPermission->id, $formPermissions)):
                    $adminPermission = AdminPermission::where('admin_id', 'LIKE', $admin->id)->delete();
                endif;
            endforeach;

            if (Input::has('permissionId')):
                foreach ($formPermissions as $value):
                    $adminPermission = AdminPermission::firstOrCreate(array('admin_id' => Input::get('adminId'), 'permission_id' => $value));
                    $adminPermission->save();
                endforeach;
            endif;
            return Redirect::to('saleadminpanel/settings/users');
        endif;
    }

    public function settings_permissions()
    {
        $permissions = Permission::get();
        $this->layout->customjs = View::make('admin.settings.cssjs');
        $this->layout->content = View::make('admin.settings.permission', ['permissions' => $permissions]);
        $this->layout->sidebar = View::make('admin.settings.sidebar');
        $this->layout->mainmenu = View::make('admin.mainmenu');
    }

    public function post_settings_permissions()
    {
        if (Input::has('addPermission')):
            $permission = new Permission;
            $permission->description = Input::get('descriptionPermission');
            $permission->save();
            return Redirect::to('/saleadminpanel/settings/permissions');
        endif;
    }
}