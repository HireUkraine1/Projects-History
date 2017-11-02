<?php

namespace App\Console\Commands\UserManager;

use App\Models;
use Illuminate\Support\Facades\Validator;

class AdminManager extends Manager
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'manager:admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Admin Manager (Create/Delete)';


    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $action = $this->choice(__('console/users-manager.admin.type_action'), ['create', 'delete'], 0);

        if ($action == 'create') {
            $this->create();
        } elseif ($action == 'delete') {
            $this->delete();
        }
    }

    /**
     * Create admin
     */
    protected function create()
    {
        $data = $this->newAdminData();
        if ($this->validator($data)) {
            Models\Admin::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
            ]);
            return $this->info(__('console/users-manager.admin.created'));
        }
    }

    /**
     * Delete admin
     */
    protected function delete()
    {
        $email = $this->ask(__('console/users-manager.admin.email'));
        $admin = $this->getAdminByEmail($email);
        if ($this->confirmDeleteAdmin($admin)) {
            $admin->delete();
            $this->info(__('console/users-manager.admin.deleted'));
        }
    }

    /**
     * Get data that need for creation admin
     *
     * @return array
     */
    public function newAdminData()
    {
        $name = $this->ask(__('console/users-manager.admin.name'));
        $email = $this->ask(__('console/users-manager.admin.email'));
        $password = $this->secret(__('console/users-manager.admin.password'));
        $password_confirmation = $this->secret(__('console/users-manager.admin.password_confirmation'));
        return compact([
            'name',
            'email',
            'password',
            'password_confirmation'
        ]);
    }

    /**
     * Validate input data
     *
     * @param array $data
     * @return bool
     */
    private function validator(array $data)
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins',
            'password' => 'required|string|min:6|confirmed',
        ]);
        if (count($validator->errors()->all())) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return false;
        }
        return true;
    }

    /**
     * Get admin by email
     *
     * @param $email
     * @return mixed
     */
    private function getAdminByEmail($email)
    {
        return Models\Admin::emailFilter($email)->first();

    }

    /**
     * Confirm that admin should be deleted
     * @param $admin
     * @return bool
     */
    private function confirmDeleteAdmin($admin)
    {
        if (!$admin instanceof Models\Admin) {
            $this->error(__('console/users-manager.admin.not_found'));
            return false;
        }
        return $this->confirm(__('console/users-manager.admin.delete_questions') . ' "' . $admin->email . '"?');
    }
}