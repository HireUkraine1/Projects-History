<?php

namespace App\Http\Controllers\Protect;

use App\Http\ConfigParameters\ParametersList;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\VirtualEnvironments\VirtualEnv;
use Illuminate\Http\Request;

class InstanceController extends CommonController
{
    protected $parameters;

    public function __construct(ParametersList $parameters)
    {
        parent::__construct();
        $this->parameters = $parameters;
    }

    public function index()
    {
        return view('pages.design-instances');
    }

    /**
     * Get instances
     */
    public function getInstances()
    {
        $instances = [];
        $message = ['error' => 1, 'Server Error'];
        $provisionedInstances = \DB::table('instance_creating')->orderBy('id', 'DESC')->get();
        if (count($provisionedInstances) > 0) {
            try {
                $i = 0;
                foreach ($provisionedInstances as $instance) {
                    $instances[$i]['instance_name'] = $instance->instance_name;
                    $instances[$i]['id'] = $instance->vmid;
                    $instances[$i]['SSH'] = '';
                    $instances[$i]['HTTP'] = '';
                    $instances[$i]['DATE'] = '';
                    $instances[$i]['version'] = $instance->version;
                    if (strlen($instance->created) != 0) {
                        $datetime = $instance->created;
                        $pos = strpos($datetime, " ");
                        $instances[$i]['DATE'] = substr($datetime, 0, $pos);
                        $instances[$i]['TIME'] = substr($datetime, -$pos + 2);
                    }
                    $diff = abs(strtotime(date("Y-m-d H:i:s")) - strtotime($instance->updated));
                    if (floor($diff / (3600 * 24)) > 1) {
                        $date_res = floor($diff / (3600 * 24));
                        $instances[$i]['updated'] = $date_res . " days";
                    } elseif (floor($diff / 3600) > 1) {
                        $date_res = floor($diff / 3600);
                        $instances[$i]['updated'] = $date_res . " hours";
                    } else {
                        $date_res = floor($diff / 60);
                        $instances[$i]['updated'] = $date_res . " minutes";
                    }
                    if (strlen($instance->ip) != 0) {
                        $devIp = explode(".", $instance->ip);
                        $devIp = end($devIp);
                        $instances[$i]['SSH'] = 'ssh root@' . $this->parameters->hostname . ' -p2' . $devIp;
                        $instances[$i]['HTTP'] = 'http://' . $this->parameters->hostname . ':8' . $devIp;
                    }
                    $status = $instance->instance_status;
                    $logArray = json_decode($instance->log);
                    $instances[$i]['status'] = $status;
                    if (strpos($instance->log, $this->parameters->finish_creating_instance) === false) {
                        $instances[$i]['status_color'] = 'status-processing';
                        $instances[$i]['status_indicator'] = 'ind-processing';
                        $instances[$i]['status'] = 'creating';

                    } else {
                        $instances[$i]['status'] = $status;
                        if ($status == 'stopped') {
                            $instances[$i]['status_color'] = 'status-stopped';
                            $instances[$i]['status_indicator'] = 'ind-fail';
                        } elseif ($status == 'running') {
                            $instances[$i]['status_color'] = 'status-run';
                            $instances[$i]['status_indicator'] = 'ind-run';
                        } else {
                            $instances[$i]['status_color'] = 'status-processing';
                            $instances[$i]['status_indicator'] = 'ind-processing';
                        }
                    }
                    $i++;
                }
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
            $message = ['error' => 0, 'message' => $instances];
        } else {
            $message = ['error' => 1, 'message' => 'Do not have any instance'];
        }

        echo json_encode($message);
    }

    /**
     * Get instance by name
     *
     * @param $name
     * @return mixed
     */
    public function instanceName($name)
    {
        $instance = \DB::table('instance_creating')->where('instance_name', $name)->first();
        if ($instance == null) {
            abort(404);
        }
        $logs = json_decode($instance->log);
        if (is_null($logs)) {
            $logs = [];
        }
        $devIp = $this->devIp($instance->ip);
        $instanceInfo = ['name_instance' => $instance->instance_name,
            'created_by' => 'admin',
            'date' => $instance->created,
            'web' => 'http://' . $instance->ip . ':8' . $devIp,
            'ssh' => 'ssh root@' . $instance->ip . '  -p2' . $devIp,
            'status' => $instance->instance_status,
            'description' => $instance->description,
            'version' => $instance->version,

        ];
        return view('pages.design-instance-name')->with('menu', 'instances')->with('instanceName', $name)->with('logs', $logs)->with('instanceInfo', $instanceInfo);
    }

    /**
     * @param $host
     * @return array|mixed
     */
    public function devIp($host)
    {
        $devIp = explode(".", $host);
        $devIp = end($devIp);
        return $devIp;
    }

    /**
     * Instances control actions
     *
     */
    public function instanceAction(Request $request, VirtualEnv $virtualEnv)
    {
        ignore_user_abort(true);
        set_time_limit(0);
        $action = $request->action;
        $id = $request->id;
        try {
            $virtualEnv->loginInstance();
            if ($action == 'restart') {
                $virtualEnv->restartInstance($id);
            } elseif ($action == 'stop') {
                $virtualEnv->stopInstance($id);
            } elseif ($action == 'start') {
                $virtualEnv->startInstance($id);
            } elseif ($action == 'destroy') {
                $virtualEnv->destroyInstance($id);
            }
            sleep($this->parameters->sleep);
            $status = $virtualEnv->statusInstanceOrDelete($id);
            if ($status['status'] == 'stopped') {
                $indication['status_color'] = 'status-stopped';
                $indication['status_indicator'] = 'ind-fail';
            } elseif ($status['status'] == 'running') {
                $indication['status_color'] = 'status-run';
                $indication['status_indicator'] = 'ind-run';
            } else {
                $indication['status_color'] = 'status-processing';
                $indication['status_indicator'] = 'ind-processing action-in-processing';
            }
            \DB::table('instance_creating')->where('vmid', $id)
                ->update(['updated' => date("Y-m-d H:i:s"), 'instance_status' => $status['status']]);
            $message = ['error' => 0, 'message' => 'Success! Current state of VM-' . $id . ' is ' . $status['status'], 'indication' => $indication];
        } catch (\Exception $e) {
            $message = ['error' => 1, 'message' => $e->getMessage()];
        }
        return $message;
    }


}
