<?php namespace App\Http\Helper;

use App\Model;

class Service
{
    /**
     * servers list
     *
     * @return array
     */
    public function allServices()
    {
        $services = Model\Service::with('type')->get();
        $servicesArray = [];
        foreach ($services as $key => $serviceInfo) {
            $servicesArray[$key]['id'] = $serviceInfo->id;
            $servicesArray[$key]['name'] = $serviceInfo->name;
            $servicesArray[$key]['description'] = $serviceInfo->descriptions;
            $servicesArray[$key]['price'] = $serviceInfo->price;
            $servicesArray[$key]['type'] = $serviceInfo['type']->name;
            $servicesArray[$key]['status'] = $serviceInfo->status ? 'Enable' : 'Disable';
            $servicesArray[$key]['status_class'] = $serviceInfo->status ? 'success' : 'danger';
        }
        return $servicesArray;
    }


}
