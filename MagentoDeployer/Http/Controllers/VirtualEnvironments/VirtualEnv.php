<?php

namespace App\Http\Controllers\VirtualEnvironments;

interface VirtualEnv
{

    /**
     * Proxmox login
     */
    public function loginInstance();

    /**
     * get instance status or delete it from DataBase
     */
    public function statusInstanceOrDelete($id);

    /**
     * get instance status
     */
    public function statusInstance($id);

    /**
     * instance restart
     */
    public function restartInstance($id);

    /**
     * instance stop
     */
    public function stopInstance($id);


    /**
     * instance start
     */
    public function startInstance($id);

    /**
     * instance destroy
     */
    public function destroyInstance($id);

    public function listInstances();

    public function get_next_vmid();

    public function cloneInstance($template_id, $params, $newInstance);

    public function taskStatus($upid);

    public function getInstanceConfig($vmid);

    public function postInstanceConfig($vmid, $params);

    public function getStatusTask($upid);

}