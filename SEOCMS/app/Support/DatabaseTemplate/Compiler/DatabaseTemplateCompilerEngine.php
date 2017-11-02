<?php

namespace App\Support\DatabaseTemplate\Compiler;

use Illuminate\View\Engines\CompilerEngine;

class DatabaseTemplateCompilerEngine extends CompilerEngine
{
    /**
     * The Blade compiler instance.
     *
     * @var \Illuminate\View\Compilers\CompilerInterface
     */
    protected $compiler;

    /**
     * DatabaseTemplateCompilerEngine constructor.
     *
     * @param DatabaseTemplateCompiler $compiler
     */
    public function __construct(DatabaseTemplateCompiler $compiler)
    {
        $this->compiler = $compiler;
    }

    public function get($path, array $data = [])
    {
        $this->lastCompiled[] = $path;

        // If this given view has expired, which means it has simply been edited since
        // it was last compiled, we will re-compile the views so we can evaluate a
        // fresh copy of the view. We'll pass the compiler the path of the view.
        if ($this->compiler->isExpired($path)) {
            $this->compiler->compile($path);
        }

        $compiled = $this->compiler->getCompiledPath($path);

        // Once we have the path to the compiled file, we will evaluate the paths with
        // typical PHP just like any other templates. We also keep a stack of views
        // which have been rendered for right exception messages to be generated.
        $results = $this->evaluatePath($compiled, $data);
        array_pop($this->lastCompiled);

        $results = $this->deleteСyclicLink($results);

        return $results;
    }

    private function deleteСyclicLink($viewString)
    {
        $url = str_ireplace(config('settings.current_domain'), "", \Request::url());
        $urlPattern = str_replace('/', "\/", $url);
        $pattern = '/<a .*?href=("|\')' . $urlPattern . '.*?<\/a>/';
        preg_match_all($pattern, $viewString, $matches);
        $results = $viewString;
        if (count($matches[0])) {
            $cyclicLink = $matches[0];
            $replacedCyclicLink = [];
            foreach ($cyclicLink as $link) {
                $link = str_ireplace('<a ', '<span ', $link);
                $link = str_ireplace('</a>', '</span>', $link);
                $link = str_ireplace(' href="' . $url . '"', "", $link);
                $link = str_ireplace(' href=\'' . $url . '\'', "", $link);
                $replacedCyclicLink[] = $link;
            }
            $results = str_ireplace($cyclicLink, $replacedCyclicLink, $viewString);
        }

        return $results;
    }

}