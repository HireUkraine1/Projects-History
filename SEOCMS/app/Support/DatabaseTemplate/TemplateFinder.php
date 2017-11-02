<?php

namespace App\Support\DatabaseTemplate;

use App\Models\Template;

class TemplateFinder
{
    /**
     * @param string $name
     * @return \Exception
     */
    public function find(string $name)
    {
         return Template::virtualrootFilter($name)
                ->firstOrFail();
    }


    public function lastModified($path)
    {
        $template = $this->find($path);
        return strtotime($template->updated_at);
    }
}