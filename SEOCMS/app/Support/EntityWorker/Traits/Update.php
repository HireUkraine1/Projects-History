<?php

namespace  App\Support\EntityWorker\Traits;

trait Update
{
    public function updateEntity($id, $data){
     	$syncData = array_only($data, $this->pivot);
    	$item =  $this->model::where('id', '=', $id)->first();
    	foreach(array_except($data, $this->pivot) as $var => $value){
    		$item->{$var} = $value;
    	}
    	$item->save();
    	$this->syncPivot($item, $syncData);
    	return $item;
    }

}
