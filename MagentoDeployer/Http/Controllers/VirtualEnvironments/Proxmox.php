<?php

namespace App\Http\Controllers\VirtualEnvironments;

use App\Http\ConfigParameters\ParametersList;
use App\Http\Controllers\Library\PVE2_API;

class Proxmox implements VirtualEnv
{
    private $proxmox;
    private $parameters;

    public function __construct(PVE2_API $proxmox, ParametersList $parameters)
    {
        $this->proxmox = $proxmox;
        $this->parameters = $parameters;
    }

    /**
     * Proxmox login
     */
    public function loginInstance()
    {
        if (!$this->proxmox->login()) {
            throw new \Exception("Unexpected error occurred during login attempt to Proxmox API");
        }
    }

    /**
     * get instance status or delete it from DataBase
     */
    public function statusInstanceOrDelete($id)
    {
        $status = $this->proxmox->get("/nodes/" . $this->parameters->proxmox_node . "/qemu/" . $id . "/status/current");
        if (!$status) {
            \DB::table('instance_creating')->where('vmid', '=', $id)->delete();
            throw new \Exception($id . "|Unexpected error occurred while getting instance status through Proxmox API");
        }
        return $status;
    }

    /**
     * get instance status
     */
    public function statusInstance($id)
    {
        $status = $this->proxmox->get("/nodes/" . $this->parameters->proxmox_node . "/qemu/" . $id . "/status/current");
        if (!$status) {
            throw new \Exception($id . "|Unexpected error occurred while getting status of instance " . $id);
        }
        return $status;
    }

    /**
     * instance restart
     */
    public function restartInstance($id)
    {
        $this->stopInstance($id);
        $restart = $this->startInstance($id);
        if (!$restart) {
            throw new \Exception($id . "|Unexpected error occurred while restarting the instance " . $id);
        }
        return $restart;
    }

    /**
     * instance stop
     */
    public function stopInstance($id)
    {
        $status = $this->proxmox->post("/nodes/" . $this->parameters->proxmox_node . "/qemu/" . $id . "/status/stop", array());
        if (!$status) {
            throw new \Exception($id . "|Unexpected error occurred while stopping the instance " . $id);
        }
        return $status;
    }

    /**
     * instance start
     */
    public function startInstance($id)
    {
        $result = $this->proxmox->post("/nodes/" . $this->parameters->proxmox_node . "/qemu/" . $id . "/status/start", array());
        if (!$result) {
            throw new \Exception($id . "|Unexpected error occurred while starting the instance " . $id);
        }
        sleep($this->parameters->sleep);
        return $result;
    }

    /**
     * instance destroy
     */
    public function destroyInstance($id)
    {
        $this->stopInstance($id);
        sleep($this->parameters->sleep);
        $result = $this->proxmox->delete("/nodes/" . $this->parameters->proxmox_node . "/qemu/" . $id);
        if (!$result) {
            throw new \Exception($id . "|Unexpected error occurred while destroying the instance " . $id);
        }
        sleep($this->parameters->sleep);
        return $result;
    }

    //Get list of instances
    public function listInstances()
    {
        $instances = $this->proxmox->get("/nodes/" . $this->parameters->proxmox_node . "/qemu");
        if (!$instances) {
            throw new \Exception("Unexpected error occurred while getting list of instances through Proxmox API");
        }
        return $instances;
    }

    public function get_next_vmid()
    {
        $new_vmid = $this->proxmox->get_next_vmid();
        if (!$new_vmid) {
            throw new \Exception("Unexpected error occurred while getting next vmid of instance through Proxmox API");
        }
        return $new_vmid;
    }

    public function cloneInstance($template_id, $params, $newInstance)
    {

        $result = $this->proxmox->post('/nodes/' . $this->parameters->proxmox_node . '/qemu/' . $template_id . '/clone', $params);
        if (!$result) {
            throw new \Exception("Unexpected error occurred while cloning the instance " . $newInstance);
        }
        return $result;
    }

    public function taskStatus($upid)
    {
        $status = $this->proxmox->get('/nodes/' . $this->parameters->proxmox_node . '/tasks/' . $upid . '/status');
        if (!$status) {
            throw new \Exception("Unexpected error occurred while getting task " . $upid . " status");
        }
        return $status;
    }

    public function getInstanceConfig($vmid)
    {
        $config = $this->proxmox->get('/nodes/' . $this->parameters->proxmox_node . '/qemu/' . $vmid . '/config');
        if (!$config) {
            throw new \Exception($vmid . "|Unexpected error occurred while getting config of instance " . $vmid);
        }
        return $config;
    }


    public function postInstanceConfig($vmid, $params)
    {
        $result = $this->proxmox->post('/nodes/' . $this->parameters->proxmox_node . '/qemu/' . $vmid . '/config', $params);
        if (!$result) {
            throw new \Exception($vmid . "|Unexpected error occurred while setting config of instance " . $vmid);
        }
        return $result;
    }

    public function getStatusTask($upid)
    {
        $status = $this->proxmox->get('/nodes/' . $this->parameters->proxmox_node . '/tasks/' . $upid . '/status');
        if (!$status) {
            throw new \Exception("Unexpected error occurred while getting status of task " . $upid);
        }
        return $status;
    }

}