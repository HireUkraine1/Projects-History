<?php

namespace App\Support\DatabaseTemplate;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\View\Factory as FactoryContract;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Factory as FactoryView;
use Illuminate\View\FileViewFinder;
use App\Support\DatabaseTemplate\Compiler\DatabaseTemplateCompilerEngine;
use App\Support\DatabaseTemplate\Compiler\DatabaseTemplateCompiler;

class DatabaseTemplateFactory extends FactoryView implements FactoryContract
{
    /**
     * @var \Illuminate\Foundation\Application|mixed
     */
    private $app, $filesystem, $templateFinder;

    /**
     * DatabaseTemplateFactory constructor.
     *
     * @param EngineResolver $engines
     * @param Dispatcher $events
     * @param Filesystem $filesystem
     */
    public function __construct(
        EngineResolver $engines,
        Dispatcher $events,
        Filesystem $filesystem
    ){
        $this->app = app();
        $this->filesystem = $filesystem;
        $this->templateFinder = new TemplateFinder;
        $finder = new FileViewFinder($this->filesystem, []);
        $engines->register('blade', function () {
            return new DatabaseTemplateCompilerEngine(
                new DatabaseTemplateCompiler(
                    $this->templateFinder,
                    $this->filesystem,
                    $this->app['config']['view.compiled']
                )
            );
        });
        parent::__construct($engines, $finder, $events);
    }

    /**
     * Get the evaluated view contents for the given view.
     *
     * @param  string  $view
     * @param  array   $data
     * @param  array   $mergeData
     * @return \Illuminate\Contracts\View\View
     */
    public function make($view, $data = [], $mergeData = [])
    {

        $path = $view;
        // Next, we will create the view instance and call the view creator for the view
        // which can set any data, etc. Then we will return the view instance back to
        // the caller for rendering or performing other view manipulations on this.
        $data = array_merge($mergeData, $this->parseData($data));

        return tap($this->viewInstance($view, $path, $data), function ($view) {
            $this->callCreator($view);
        });
    }

    /**
     * Create a new view instance from the given arguments.
     *
     * @param  string $view
     * @param  string $path
     * @param  array $data
     * @return \Illuminate\Contracts\View\View
     */
    protected function viewInstance($view, $path, $data)
    {
        return new DbView($this, $this->getEngineFromPath($path), $view, $path, $data);
    }

    /**
     * Get the extension used by the view file.
     *
     * @param  string $path
     * @return string
     */
    protected function getExtension($path)
    {
        $path .= '.blade.php';
        $extensions = array_keys($this->extensions);
        return Arr::first($extensions, function ($value) use ($path) {
            return Str::endsWith($path, '.' . $value);
        });
    }

}