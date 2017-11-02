<?php

namespace App\Support\EntityWorker;

class EntityWorker
{
    use Traits\Create, Traits\Update, Traits\Delete;

    /**
     * Entity Model
     */
    private $model;

    /**
     * Current model m-2-m methods
     */
    private $pivot = [];

    /**
     * EntityWorker constructor.
     *
     * @param $model
     * @param $pivot
     */
    public function __construct( $model, $pivot )
    {
    	$this->model = $model;
    	$this->pivot = $pivot;
    }

}