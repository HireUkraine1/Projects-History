<?php

namespace App\Support\Sitemap;

use Illuminate\Database\Eloquent\Collection;
use App\Models;

class SitemapPages
{
    private $pages;

    public function __construct(Collection $pages)
    {
        $this->pages = $pages;
    }

    //modify in future
    public function getPages()
    {
        $processedPages = [];

        $this->pages->each(function (Models\Pagesheet $page) use (&$processedPages) {
            $return['url'] = $page->url;
            $return['lastmod'] = $page->updated_at;
            $return['changefreq'] = 'weekly';
            $return['priority'] = $page->sitemappriority;
            $processedPages[] = $return;
        });

        return $processedPages;
    }
}