<?php

namespace App\Console\Commands;

use App\Http\ConfigParameters\ParametersList;
use App\Http\Controllers\VirtualEnvironments\VirtualEnv;
use Illuminate\Console\Command;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class CheckInstanceStatus extends Command
{
    //variables and commands patterns old
    protected $parameters;
    protected $virtualEnv;
    protected $view_log;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check-instances-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'check instances status';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ParametersList $parameters, VirtualEnv $virtualEnv)
    {
        parent::__construct();
        $this->parameters = $parameters;
        $this->virtualEnv = $virtualEnv;
        $this->view_log = new Logger('Cron Error Log');
        $this->view_log->pushHandler(new StreamHandler(storage_path('logs/cron.log'), Logger::INFO));
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $this->virtualEnv->loginInstance();
            $instanceInDb = \DB::table('instance_creating')->select('vmid', 'instance_status')->get();
            $vmids = [];
            foreach ($instanceInDb as $instance) {
                $vmids[$instance->vmid] = $instance->instance_status;
            }
            if (count($vmids) > 0) {
                $instancesList = $this->virtualEnv->listInstances();
                $instancesEdit = array_filter($instancesList, function ($instance) use ($vmids) {
                    $checkVmid = in_array($instance['vmid'], array_keys($vmids));
                    $checkStatus = false;
                    if ($checkVmid) {
                        if ($instance['status'] != $vmids[$instance['vmid']]) {
                            $checkStatus = true;
                        }
                    }
                    return ($checkStatus);
                });
                foreach ($instancesEdit as $instance) {
                    \DB::table('instance_creating')
                        ->where('vmid', $instance['vmid'])
                        ->update(array('instance_status' => $instance['status'], 'updated' => date("Y-m-d H:i:s")));
                }
            }
        } catch (\Exception $e) {
            $this->view_log->addInfo($e->getMessage() . ' line: ' . $e->getLine());
        }
    }


}
