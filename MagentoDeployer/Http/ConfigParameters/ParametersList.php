<?php

namespace App\Http\ConfigParameters;

Class ParametersList
{
    /**
     *  PROXMOX PART OF CONFIG START
     */
    protected $hostname = 'XX.XX.XX.XX';
    protected $username = 'root';
    protected $realm = 'pam';
    protected $password = 'XXXXXXXXXXXX';
    protected $port = XXXX;

    protected $proxmox_node = 'proxmox';
    protected $proxmox_storage = 'XXXX';
    protected $proxmox_continer = 'XXXXX';
    protected $proxmox_clone_isfull = False;
    protected $bridge = 'XXXXX';

    /**
     *  PROXMOX PART OF CONFIG FINISH
     */
    protected $git = [
        'magento_1' => [
            'gittoken' => 'XXXXXXXXXXXXXXXXXXXXXXXx',
            'gitmethod' => 'XXXXX',
            'gituser' => 'XXXXX',
            'gitreponame' => 'XXXXXXXXXX',
            'def_branch' => 'XXXXXXXXX',
        ],
        'magento_2' => [
            'gittoken' => 'XXXXXXXXXXX',
            'gitmethod' => 'XXXXXXXXX',
            'gituser' => 'XXXXXXXXXXX',
            'gitreponame' => 'XXXXXXX',
            'def_branch' => 'XXXXXXX',
        ],

    ];

    protected $sleep = 5;
    protected $timeout = 5;

    /**
     *  INSTANCE PART OF CONFIG START
     */
    protected $mac = 16; //Pool of MAC addresses starts from this MAC
    protected $ip = 150;   //Pool of IP addresses starts from this IP
    protected $total = 20; //Total number of MAC and IP addresses
    protected $mac_ip = 'XX:XX:XX:XX:XX:';
    protected $host_ip = 'XX.XX.XX.';
    protected $finish_creating_instance = 'Waiting for git clone operation to complete';


    public function __get($name)
    {
        return $this->$name;
    }

}