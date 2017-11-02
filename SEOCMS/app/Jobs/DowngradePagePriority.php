<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DowngradePagePriority implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $pages;

    public function __construct($pages)
    {
        $this->pages = $pages;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->pages->each(function ($page) {
            if ($page->sitemappriority > 0.5) {
                $page->sitemappriority = $page->sitemappriority - 0.1;
                $page->save();
            };
        });
    }

}
