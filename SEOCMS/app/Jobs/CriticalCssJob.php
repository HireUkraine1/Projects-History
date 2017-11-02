<?php

namespace App\Jobs;

use App\Models\Pagesheet;
use App\Models\PageCompile;
use Illuminate\Bus\Queueable;
use App\Models\PageCompileStatus;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldQueue;

class CriticalCssJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $page;

    protected $dimensions;

    protected $pageCompile;

    /**
     * Create a new job instance.
     *
     * @param Pagesheet $page
     * @param $dimensions
     * @param PageCompile $pageCompile
     */
    public function __construct(Pagesheet $page, $dimensions, PageCompile $pageCompile)
    {
        $this->page        = $page;
        $this->dimensions  = $dimensions;
        $this->pageCompile = $pageCompile;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $html = dbview($this->page->template->virtualroot, [
                'h1'          => $this->page->h1,
                'title'       => $this->page->title,
                'keywords'    => $this->page->keywords,
                'description' => $this->page->description,
                'criticalcss' => $this->page->criticalcss,
                'url'         => $this->page->url,
            ])->render();

            $fileName = $this->page->template->name;

            \Storage::put("critical/{$fileName}.html", $html);

            $storageHtml = storage_path("app/critical/{$fileName}.html");
            $storageCss  = storage_path("app/critical/{$fileName}.css");
            $dimensions  = $this->dimensions;
            $base        = public_path();

            $command = "critical {$storageHtml} --dimensions: {$dimensions} > {$storageCss} --base {$base}";

            exec($command, $output, $code);

            $this->page->criticalcss = \Storage::get("critical/{$fileName}.css");

            $this->page->save();
        } catch (\Exception $exception) {
            $this->updateCompileLog(PageCompileStatus::ERROR, $exception->getMessage());
        }

        if ($code != 0) {
            $this->updateCompileLog(PageCompileStatus::ERROR);
        } else {
            $this->updateCompileLog(PageCompileStatus::SUCCESS);
        }
    }

    private function updateCompileLog($status, $message = null)
    {
        $this->pageCompile->status = $status;

        if ($message) {
            $this->pageCompile->error = $message;
        }

        $this->pageCompile->save();
    }
}
