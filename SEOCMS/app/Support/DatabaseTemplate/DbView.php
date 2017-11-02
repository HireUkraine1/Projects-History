<?php

namespace App\Support\DatabaseTemplate;

use App\Support\DatabaseTemplate\Compiler\DatabaseTemplateCompilerEngine;
use Illuminate\View\View as LaraView;

class DbView extends LaraView
{
    /**
     * DbView constructor.
     * @param DatabaseTemplateFactory $factory
     * @param DatabaseTemplateCompilerEngine $engine
     * @param string $view
     * @param string $path
     * @param array $data
     */
    public function __construct(
        DatabaseTemplateFactory $factory,
        DatabaseTemplateCompilerEngine $engine,
        $view,
        $path,
        $data = []
    ){
        parent::__construct($factory, $engine, $view, $path, $data);
    }

}
