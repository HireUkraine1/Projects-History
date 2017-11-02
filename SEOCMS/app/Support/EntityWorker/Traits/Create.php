<?php

namespace  App\Support\EntityWorker\Traits;

trait Create
{
    public function createEntity($data)
    {
        $syncData = array_only($data, $this->pivot);
        $thisModel = $this->model;
        $item = $thisModel::create(array_except($data, $this->pivot));
        $this->syncPivot($item, $syncData);
        return $item;
    }

    private function syncPivot($item, $syncData)
    {
        foreach ($syncData as $pivot_method => $value) {
            if(empty($value)){
                $item->{$pivot_method}()->detach();
            } else {
                $item->{$pivot_method}()->sync($value);
            }
        }
    }
}
