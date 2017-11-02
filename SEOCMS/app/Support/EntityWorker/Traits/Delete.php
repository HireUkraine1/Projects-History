<?php

namespace  App\Support\EntityWorker\Traits;

trait Delete
{
    public function deleteEntity($id){
        $thisModel = $this->model;
        return $thisModel::destroy($id);
    }
}
