<?php

namespace App\Providers;

use App\Http\Controllers\VirtualEnvironments\Proxmox;
use App\Http\Controllers\VirtualEnvironments\VirtualEnv;
use Illuminate\Support\ServiceProvider;

class VirtualEnvProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(VirtualEnv::class, Proxmox::class);
    }
}
