<?php

namespace App\Support\Sitemap;

use Illuminate\Support\Facades\Response;

/**
 * Class XmlResponse
 * @package XmlResponse
 */
class XmlResponse
{
    /**
     * @var string
     */
    private $domen;

    /**
     * @var array
     */
    private $header;

    /**
     * @var int
     */
    private $status;



    /**
     * XmlResponse constructor.
     * @param $domen
     */
    public function __construct(string $domen)
    {
        $this->domen = $domen;
        $this->header = $this->header();
        $this->status = 200;

    }

    /**
     * @param SitemapPages $pages
     * @return mixed
     */
    public function sitemap(SitemapPages $pages)
    {
        $xml = new \SimpleXMLElement($this->template());

        foreach ($pages->getPages() as $page) {
            $url = $xml->addChild("url");
            $url->addChild("loc", $this->domen . $page['url']);
            $url->addChild("lastmod", $page['lastmod']);
            $url->addChild("changefreq", $page['changefreq']);
            $url->addChild("priority", $page['priority']);
        }

        return Response::make($xml->asXML(), $this->status, $this->header);
    }


    /**
     * @return array
     */
    private function header()
    {
        return [
            'Content-Type' => 'text/xml'
        ];
    }

    /**
     * @return string
     */
    private function template()
    {
        return  "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".
                "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\"></urlset>";
    }
}

