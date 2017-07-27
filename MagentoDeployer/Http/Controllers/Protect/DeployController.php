<?php

namespace App\Http\Controllers\Protect;

use App\Http\ConfigParameters\ParametersList;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\VirtualEnvironments\VirtualEnv;
use GrahamCampbell\GitHub\Facades\GitHub;
use Illuminate\Http\Request;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class DeployController extends CommonController
{
    protected $parameters;
    protected $virtualEnv;
    protected $view_log;

    public function __construct(ParametersList $parameters, VirtualEnv $virtualEnv)
    {
        parent::__construct();
        $this->parameters = $parameters;
        $this->virtualEnv = $virtualEnv;
        $this->view_log = new Logger('Deploy Controller "Exceptions"');
        $this->view_log->pushHandler(new StreamHandler(storage_path('logs/deploy_controller.log'), Logger::INFO));
    }

    /**
     * Start methods loading Deploy page
     *
     * @return mixed
     */
    public function index()
    {
        return view('pages.design-deploy');
    }

    /**
     * Get branches list from GitHub repository
     */
    public function getBranchesList(Request $request)
    {
        //Get list of branches from GitHub repo $this->reponame of $this->gituser user
        try {
            $version = $request->version;
            $gitBranches = GitHub::connection($version)->gitData()->references()->branches($this->parameters->git[$version]['gituser'], $this->parameters->git[$version]['gitreponame']);
            $branches = array_map(function ($branch) {
                $branch = $branch['ref'];
                $branch = preg_replace('/refs\/heads\//', "", $branch);
                return $branch;
            }, $gitBranches);
            $message = ['error' => 0, 'message' => ['branches' => $branches, 'def_branch' => $this->parameters->git[$version]['def_branch']]];
        } catch (\Exception $e) {
            $message = ['error' => 1, 'message' => $e->getMessage() . "Unable to get list of branches of " . $this->parameters->git[$version]['gituser'] . "/" . $this->parameters->git[$version]['gitreponame']];
        }
        echo json_encode($message);
    }

    /**
     * Get instances list using Proxmox API
     *
     * @throws Exception
     */
    public function getInstancesList()
    {
        try {
            $this->virtualEnv->loginInstance();
            $envInstances = $this->virtualEnv->listInstances();
            $instances = array_map(function ($instance) {
                return $instance['name'];
            }, $envInstances);
            $message = ['error' => 0, 'message' => $instances];
        } catch (\Exception $e) {
            $message = ['error' => 1, 'message' => $e->getMessage()];
            $this->view_log->addInfo($e->getMessage() . ' line: ' . $e->getLine());
        }
        echo json_encode($message);
    }

    /**
     * Get commits list for a given branch
     */
    public function getCommitsList(Request $request)
    {
        $currBranch = $request->branch;
        $version = $request->version;

        //Check existence of given branch
        try {
            if ($this->branchIsExists($currBranch, $version)) {
                $commitsGit = GitHub::connection($version)->repo()->commits()->all($this->parameters->git[$version]['gituser'], $this->parameters->git[$version]['gitreponame'], ['sha' => $currBranch]);
                $commits = array_map(function ($commit) {
                    $sha = substr($commit['sha'], 0, 7);
                    $commit = ['sha' => $sha, 'message' => $commit['commit']['message']];
                    return $commit;
                }, $commitsGit);
                $message = ['error' => 0, 'message' => $commits];
            } else {
                $message = ['error' => 1, 'message' => "Source branch " . $this->parameters->git[$version]['gituser'] . "/" . $this->parameters->git[$version]['gitreponame'] . "/" . $currBranch . " doesn't exist"];
            }
        } catch (\Exception $e) {
            $message = ['error' => 1, 'message' => "Unable to get list of commits of " . $this->parameters->git[$version]['gituser'] . "/" . $this->parameters->git[$version]['gitreponame'] . "/" . $currBranch];
            $this->view_log->addInfo($e->getMessage() . ' line: ' . $e->getLine());
        }
        echo json_encode($message);
    }

    /**
     * Check existence of given branch
     *
     * @param $currBranch
     * @return bool
     */
    protected function branchIsExists($currBranch, $version)
    {
        $message = false;
        try {
            $branches = GitHub::connection($version)->gitData()->references()->branches($this->parameters->git[$version]['gituser'], $this->parameters->git[$version]['gitreponame']);
            foreach ($branches as $branch) {
                $branch = $branch['ref'];
                $branch = preg_replace('/refs\/heads\//', "", $branch);
                if ($branch == $currBranch) {
                    $message = true;
                    break;
                }
            }
        } catch (\Exception $e) {
            $this->view_log->addInfo($e->getMessage() . ' line: ' . $e->getLine());
            $message = false;
        }
        return $message;
    }

    /**
     * Create a new branch in GitHub repository
     */
    public function createBranch(Request $request)
    {
        $newBranch = $request->newBranch; //branch name
        $currBranch = $request->currBranch; //sha
        $version = $request->version;
        if (empty($newBranch)) {
            $message = ['error' => 1, 'message' => 'Branch name cannot be empty. Please enter a valid name.'];
        } elseif (!$this->branchIsExists($currBranch, $version)) {
            //Check existence of $currBranch
            $message = ['error' => 1, 'message' => "Source branch " . $this->parameters->git[$version]['gituser'] . "/" . $this->parameters->git[$version]['gitreponame'] . "/" . $currBranch . " doesn't exist"];
        } elseif ($this->branchIsExists($newBranch, $version)) {
            //Check existence of $newBranch
            $message = ['error' => 1, 'message' => "New branch " . $this->parameters->git[$version]['gituser'] . "/" . $this->parameters->git[$version]['gitreponame'] . "/" . $currBranch . " is already exists"];
        } else {
            //Get list of latest commits
            //It is the way to find the HEAD commit, which SHA corresponds with the branch SHA
            try {
                $commits = GitHub::connection($version)->repo()->commits()->all($this->parameters->git[$version]['gituser'], $this->parameters->git[$version]['gitreponame'], ['sha' => $currBranch]);
                $currSHA = $commits[0]['sha'];
                $params = [
                    'ref' => 'refs/heads/' . $newBranch,
                    'sha' => $currSHA
                ];
                GitHub::connection($version)->gitData()->references()->create($this->parameters->git[$version]['gituser'], $this->parameters->git[$version]['gitreponame'], $params);
                $message = ['error' => 0];
            } catch (\Exception $e) {
                $message = ['error' => 1, 'message' => $e->getMessage()];
                $this->view_log->addInfo($e->getMessage() . ' line: ' . $e->getLine());
            }
        }
        echo json_encode($message);
    }

    /**
     * Create Instance after clicking Create (sending POST)
     */
    public function createInstance(Request $request)
    {
        ignore_user_abort(true);
        set_time_limit(0);
        try {
            $newInstance = $request->newInstance; //name of new instance
            $newInstanceDescription = $request->newInstanceDescription; //Description of new instance
            $version = $request->version;//git repo
            $newBranch = $request->newBranch; // git branch
            $branchName = $request->branchName;// git commit sha
            $branchMagento2 = $request->branchMagento2; // git branch of repo branch Magento2(custom code, custom path)
            $branchCommitMagento2 = $request->branchCommitMagento2; // git commit ssh of repo branch Magento2(custom code, custom path)

            //Clear information about the previously created instance with the same name from database
            \DB::table('instance_creating')->where('instance_name', '=', $newInstance)->delete();
            \DB::table('instance_creating')->insertGetId([
                'branch_custom_code' => $branchMagento2['customCode'],
                'sha_custom_code' => $branchCommitMagento2['customCode'],
                'branch_custom_patch' => $branchMagento2['customPatch'],
                'sha_custom_patch' => $branchCommitMagento2['customPatch'],
                'created' => date("Y-m-d H:i:s"),
                'instance_name' => $newInstance,
            ]);
            $this->dbAppendLog($newInstance, ['log' => 'Instance creation started']);
            if (empty($newInstance)) {
                $message = ['error' => 1, 'message' => 'Instance name cannot be empty. Please enter a valid name.'];
            } else {
                $vmid = $this->cloneInstance($newInstance, $newInstanceDescription, $newBranch, $branchName, $version);
                $running = $this->instanceIsRunning($vmid);
                if (!$running) {
                    $this->changeMAC($vmid);
                }
                $this->dbAppendLog($vmid, ['log' => 'Starting instance']);
                $message = $this->startInstance($vmid);
                if (!$message['error']) {
                    $message = $this->instanceIsRunning($vmid);
                    if (!$message) {
                        $this->dbAppendLog($vmid, ['log' => 'Unexpected error occurred when starting the instance ' . $vmid]);
                    } else {
                        $this->dbAppendLog($vmid, ['log' => 'Instance started']);
                        $this->dbAppendLog($vmid, ['log' => 'Waiting for git clone operation to complete']);
                    }
                } else {
                    $this->dbAppendLog($newInstance, ['log' => $message['message']]);
                }
            }

        } catch (\Exception $e) {
            $this->view_log->addInfo($e->getMessage() . ' line: ' . $e->getLine());

            $log = new Logger('Exception Logs');
            $log->pushHandler(new StreamHandler(storage_path('logs/create_instance.log'), Logger::INFO));
            $log->addInfo($e->getMessage() . ' line: ' . $e->getLine());

            $message = explode('|', $e->getMessage());
            if (isset($message[1])) {
                $vmid = $message[0];
                $message = $message[1];
                $this->dbAppendLog($vmid, ['log' => $message]);
            }
        }
    }

    protected function dbAppendLog($instance, $message)
    {
        if (is_numeric($instance)) {
            $target = 'vmid';
        } else {
            $target = 'instance_name';
        }
        $db = \DB::table('instance_creating')->where($target, $instance)->first();
        if (!is_null($db)) {
            $log = json_decode($db->log);
            $log[] = $message['log'];
            $log = json_encode($log);
            \DB::table('instance_creating')->where($target, $instance)->update(['log' => $log]);
        }
    }

    /**
     * Perform cloning an instance from template
     *
     * @param $newInstance
     * @param $newInstanceDescription
     * @return array
     * @throws Exception
     */
    protected function cloneInstance($newInstance, $newInstanceDescription, $newBranch, $branchName, $version)
    {
        $template_id = 0;
        $template_name = 'template-';
        $magento = 'Magento 1.x';
        if ($version == 'magento_2') {
            $template_name = 'templateM2-';
            $magento = 'Magento 2.x';
        }

        try {
            $this->dbAppendLog($newInstance, ['log' => 'Cloning instance from template started']);
            $this->virtualEnv->loginInstance();
            $instances = $this->virtualEnv->listInstances();
            $templateDate = '0000-00-00'; // var for comparing date
            foreach ($instances as $instance) {
                if (substr_count($instance['name'], $template_name) > 0) {
                    $templateDateCalc = str_replace($template_name, '', $instance['name']);
                    if (strtotime($templateDate) < strtotime($templateDateCalc)) {
                        $templateDate = $templateDateCalc;
                        $template_id = $instance['vmid'];
                    }
                }
            }
            $new_vmid = $this->virtualEnv->get_next_vmid();
            $params = [
                'newid' => $new_vmid,
                'name' => $newInstance,
                'description' => $newInstanceDescription,
                'storage' => $this->parameters->proxmox_storage,
                'format' => $this->parameters->proxmox_continer,
                'full' => $this->parameters->proxmox_clone_isfull,
                'target' => $this->parameters->proxmox_node
            ];

            \DB::table('instance_creating')->where('vmid', '=', $new_vmid)->delete();
            \DB::table('instance_creating')->where('instance_name', $newInstance)
                ->update(['branch_name' => $branchName,
                    'commit_hash' => $newBranch,
                    'vmid' => $new_vmid,
                    'version' => $magento,
                    'description' => $newInstanceDescription,
                    'updated' => date("Y-m-d H:i:s")]);
            $result = $this->virtualEnv->cloneInstance($template_id, $params, $newInstance);
            $this->waitForTask($result);
        } catch (\Exception $e) {
            $this->view_log->addInfo($e->getMessage() . ' line: ' . $e->getLine());
            throw $e;
        }
        return $new_vmid;
    }

    /**
     * Wait for Proxmox tasks to be completed
     *
     * @param $upid
     * @return array
     * @throws Exception
     */
    protected function waitForTask($upid)
    {
        $count = 0;
        try {
            $this->virtualEnv->loginInstance();
            do {
                sleep($this->parameters->sleep);
                $status = $this->virtualEnv->getStatusTask($upid);
                $count++;
            } while ($status['status'] != 'stopped' && $count < $this->parameters->timeout);
            $message = ['error' => 0];
        } catch (\Exception $e) {
            $this->view_log->addInfo($e->getMessage() . ' line: ' . $e->getLine());
            $message = ['error' => 1, 'message' => "Waiting timeout for a task " . $upid . " to complete (" . $this->parameters->sleep * $this->parameters->timeout . " seconds) has been exceeded"];
        }
        return $message;
    }

    /**
     * Check state of instance
     *
     * @param $vmid
     * @return array
     * @throws Exception
     */
    protected function instanceIsRunning($vmid)
    {
        try {
            $this->virtualEnv->loginInstance();
            $status = $this->virtualEnv->statusInstance($vmid);
            if ($status['status'] == 'running') {
                $running = true;
            } else {
                $running = false;
            }
        } catch (\Exception $e) {
            $this->view_log->addInfo($e->getMessage() . ' line: ' . $e->getLine());
            throw $e;
        }
        return $running;
    }

    /**
     * Change MAC for instance with given $vmid.
     *
     * @param $vmid
     * @return array
     * @throws Exception
     */

    protected function changeMAC($vmid)
    {
        $this->dbAppendLog($vmid, ['log' => 'Assigning IP address started']);
        try {
            $this->virtualEnv->loginInstance();
            $message = $this->nextMAC();
            $nextMAC = $message['message']['nextMAC'];
            $nextIP = $message['message']['nextIP'];
            $params = [
                'net0' => 'rtl8139=' . $nextMAC . ',bridge=' . $this->parameters->bridge,
            ];
            $result = $this->virtualEnv->postInstanceConfig($vmid, $params);
            $this->waitForTask($result);

            \DB::table('instance_creating')->where('vmid', $vmid)
                ->update(['ip' => $nextIP]);
            $this->dbAppendLog($vmid, ['log' => "The following IP address $nextIP was assigned successfully"]);

        } catch (\Exception $e) {
            $this->view_log->addInfo($e->getMessage() . ' line: ' . $e->getLine());
            throw $e;
        }
        return $message;
    }

    /**
     * This function allows a given IP address to be assigned to a particular instance.
     * It creates an array of MAC addresses and corresponding IP addresses, checks MAC addresses of Proxmox instances
     * and returns the first free MAC that hasn't been assigned to a particular instance yet.
     * Same mapping rules for MAC and IP addresses are specified in dhcpd.conf of the hypervisor.
     * In such a way, it is easy to find out what IP address is assigned to a particular instance.
     *
     * The array of MAC addresses starts from 36:64:34:30:66:0A.
     * The array of IP addresses starts from 192.168.206.150.
     *
     * @return array
     * @throws Exception
     */
    protected function nextMAC()
    {
        $mac_ip = [];
        $allMAC = [];
        $nextMAC = '';
        //Pool of MAC addresses starts from this MAC
        $mac = $this->parameters->mac;
        //Pool of IP addresses starts from this IP
        $ip = $this->parameters->ip;
        //Total number of MAC and IP addresses
        $total = $this->parameters->total;

        for ($i = 0; $i < $total; $i++) {
            $mac_ip[$this->parameters->mac_ip . dechex($mac)] = $this->parameters->host_ip . $ip;
            $ip++;
            $mac++;
        }
        try {
            $this->virtualEnv->loginInstance();
            $instances = $this->virtualEnv->listInstances();
            foreach ($instances as $instance) {
                $config = $this->virtualEnv->getInstanceConfig($instance['vmid']);
                if (isset($config["net0"])) {
                    $macN = strtolower($this->getMAC($config["net0"]));
                    $allMAC[] = $macN;
                }
            }

            foreach ($mac_ip as $mac => $ip) {
                $mac = strtolower($mac);
                if (!in_array($mac, $allMAC)) {
                    $nextMAC = $mac;
                    break;
                }
            }
            if (empty($nextMAC)) {
                throw new \Exception("Don't have free ip");
            } else {
                $nextIP = $mac_ip[$nextMAC];
                $message = ['message' => ['nextMAC' => $nextMAC, 'nextIP' => $nextIP]];
            }

            return $message;

        } catch (\Exception $e) {
            $this->view_log->addInfo($e->getMessage() . ' line: ' . $e->getLine());
            throw $e;
        }

    }

    /**
     * Get MAC
     * @param $str
     * @return mixed
     */
    protected function getMAC($str)
    {
        $pattern = '#[\w]{1,2}\:[\w]{1,2}\:[\w]{1,2}\:[\w]{1,2}\:[\w]{1,2}\:[\w]{1,2}#';
        $b = preg_match($pattern, $str, $rows);
        return $rows[0];
    }

    /**
     * Turn instance on
     *
     * @param $vmid
     * @return array
     * @throws Exception
     */

    protected function startInstance($vmid)
    {
        try {
            $this->virtualEnv->loginInstance();
            $start = $this->virtualEnv->startInstance($vmid);
            $result = $this->waitForTask($start);
        } catch (\Exception $e) {
            $this->view_log->addInfo($e->getMessage() . ' line: ' . $e->getLine());
            throw $e;
        }
        return $result;
    }

    /**
     * Check Instance for existence
     *
     * @throws Exception
     */
    public function instanceIsExists(Request $request)
    {
        if (\Request::ajax()) {
            $newInstance = $request->newInstance;
            $instances = [];
            $message = ['error' => 0];
            if (empty($newInstance)) {
                $message = ['error' => 1, 'message' => 'Instance name cannot be empty. Please enter a valid name.'];
            } else {
                try {
                    $this->virtualEnv->loginInstance();
                    $instances = $this->virtualEnv->listInstances();
                    foreach ($instances as $instance) {
                        if ($instance['name'] == $newInstance) {
                            $message = ['error' => 1, 'message' => "Instance with the '$newInstance' name is already exists"];
                            break;
                        }
                    }
                } catch (\Exception $e) {
                    $message = ['error' => 1, 'message' => $e->getMessage()];
                }
            }
            echo json_encode($message);
        }
    }

}
