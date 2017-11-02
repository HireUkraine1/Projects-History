<?php

namespace App\Console\Commands\UserManager;

use Illuminate\Console\Command;

abstract class Manager extends Command
{
    /**
     * Manager constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Create User
     *
     */
    abstract protected function create();

    /**
     * Delete User
     */
    abstract protected function delete();

}
