<?php

namespace App\Support\DatabaseTemplate\Compiler;

use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers;
use App\Support\DatabaseTemplate\TemplateFinder;

class DatabaseTemplateCompiler extends Compilers\BladeCompiler implements Compilers\CompilerInterface
{
    /**
     * @var TemplateFinder
     */
    public $templateFinder;

    /**
     * DatabaseTemplateCompiler constructor.
     *
     * @param TemplateFinder $templateFinder
     * @param Filesystem $files
     * @param $cachePath
     */
    public function __construct(
        TemplateFinder $templateFinder,
        Filesystem $files,
        $cachePath
    ){
        $this->templateFinder = $templateFinder;
        parent::__construct($files, $cachePath);
    }

    /**
     * Compile content
     * @param $path
     */
    public function compile($path = null)
    {
        if ($path) {
            $this->setPath($path);
        }

        if (!is_null($this->cachePath)) {
            $template = $this->templateFinder->find($path);
            $contents = $this->compileString($template->body);
            $this->files->put($this->getCompiledPath($this->getPath()), $contents);
        }
    }

    /**
     * @param string $path
     * @return bool
     */
    public function isExpired($path)
    {
        $compiled = $this->getCompiledPath($path);

        // If the compiled file doesn't exist we will indicate that the view is expired
        // so that it can be re-compiled. Else, we will verify the last modification
        // of the views is less than the modification times of the compiled views.
        if (!$this->files->exists($compiled)) {
            return true;
        }

        return $this->templateFinder->lastModified($path) >=
            $this->files->lastModified($compiled);
    }

}