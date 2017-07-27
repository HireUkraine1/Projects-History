<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Mail;
use App\Model;
use Illuminate\Http\Request;

class ApplicationsController extends CommonController
{
    /**
     * Display a listing of the applications.
     *
     * Status of applications: 0-Consideration, 1-Accept, 2-Rejected
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        if ($this->admin->role_id == 3) {
            abort(403);
        }
        //status 0 - new; 1-  Accept; 2 - Waiting list
        $allAppicatedUsers = Model\Application::where('status', '!=', 1)->get()->toArray();
        $appicatedUsers = [];
        $emptyApplications = '';

        foreach ($allAppicatedUsers as $key => $user) {
            $appicatedUsers[$key]['id'] = (isset($user['id'])) ? $user['id'] : '';
            $appicatedUsers[$key]['first_name'] = (isset($user['first_name'])) ? $user['first_name'] : '';
            $appicatedUsers[$key]['last_name'] = (isset($user['last_name'])) ? $user['last_name'] : '';
            $appicatedUsers[$key]['email'] = (isset($user['email'])) ? $user['email'] : '';
            $appicatedUsers[$key]['phone'] = (isset($user['phone'])) ? $user['phone'] : '';
            $appicatedUsers[$key]['message'] = (isset($user['message'])) ? $user['message'] : '';
            $appicatedUsers[$key]['date'] = (isset($user['created_at'])) ? date('m/d/Y', strtotime($user['created_at'])) : '';
            switch ($user['status']):
                case 0:
                    $statusClass = \Lang::get('status.status_new_class');
                    $statusName = \Lang::get('status.status_new_name');
                    $action = [
                        'Accept' => [
                            'actions_url' => '/applications/action',
                            'actions_data' => '1',
                        ],
                        'Waiting list' => [
                            'actions_url' => '/applications/action',
                            'actions_data' => '2',
                        ],
                    ];
                    break;
                case 1:
                    $statusClass = \Lang::get('status.status_accept_class');
                    $statusName = \Lang::get('status.status_accept_name');
                    $action = [];
                    break;
                case 2:
                    $statusClass = \Lang::get('status.status_waiting_class');
                    $statusName = \Lang::get('status.status_waiting_name');
                    $action = [
                        'Accept' => [
                            'actions_url' => '/applications/action',
                            'actions_data' => '1',
                        ],
                    ];
                    break;
            endswitch;
            $appicatedUsers[$key]['status_class'] = $statusClass;
            $appicatedUsers[$key]['status_name'] = $statusName;
            $appicatedUsers[$key]['action'] = $action;
        }

        if (count($appicatedUsers) == 0) {
            $emptyApplications = \Lang::get('messages.application_not_found');
        }

        $data = [
            'appicatedUsers' => $appicatedUsers,
            'emptyApplications' => $emptyApplications
        ];
        return view('admin.applications.index')->with('data', $data);
    }

    /**
     * Handler of Applications
     *
     * @param Requests\AplicationStatus $aplication
     * @return string
     */
    public function action(Requests\AplicationStatus $aplication)
    {
        if ($this->admin->role_id == 3) {
            abort(403);
        }
        try {
            $error = 1;
            $msg = \Lang::get('messages.error');
            $appicatedUser = Model\Application::where('id', '=', $aplication->id)->first();
            \DB::transaction(function () use ($appicatedUser, $aplication) {

                $password = $this->generatePassword();
                $existMember = Model\User::where('email', '=', $appicatedUser->email)->orWhere('phone', '=', $appicatedUser->phone)->first();

                if ($existMember instanceof Model\User) {
                    throw new \Exception('User exist');
                }
                if ($appicatedUser instanceof Model\Application) {
                    if ($aplication->action_id == 1) {
                        Model\User::create([
                            'first_name' => $appicatedUser->first_name,
                            'last_name' => $appicatedUser->last_name,
                            'email' => $appicatedUser->email,
                            'phone' => $appicatedUser->phone,
                            'password' => $password,
                        ]);
                        \Mail::to($appicatedUser->email)->send(new Mail\AcceptMember([
                            'email' => $appicatedUser->email,
                            'password' => $password,
                            'name' => $appicatedUser->first_name
                        ]));

                    } else {
                        \Mail::to($appicatedUser->email)->send(new Mail\WaitingListMember());
                    }
                    $appicatedUser->status = $aplication->action_id;
                    $appicatedUser->save();
                }
            });
            \DB::commit();
            $error = 0;
            $msg = \Lang::get('messages.success');
        } catch (\Exception $e) {
            \DB::rollback();
            $msg = $e->getMessage();
            \Log::info($e->getFile() . $e->getLine() . $e->getMessage());;
        } finally {
            return json_encode([
                'error' => $error,
                'message' => $msg
            ]);
        }
    }

}
