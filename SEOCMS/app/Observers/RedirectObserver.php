<?php

namespace App\Observers;

use App\Models\Redirect;
use App\Support\Redirect\RedirectService;

class RedirectObserver
{
    protected $redirectService;

    public function __construct(RedirectService $redirectService)
    {
        $this->redirectService = $redirectService;
    }

    public function saved(Redirect $redirect)
    {
        $this->redirectService->cache();
    }

    public function deleted(Redirect $redirect)
    {
        $this->redirectService->cache();
    }
}