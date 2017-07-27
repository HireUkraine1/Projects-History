<?php

namespace App\Statistic\Asg;

use App\Statistic\ClientAbstract;

class AutoScalingGroup extends ClientAbstract
{
    protected $returnService;

    protected function setService()
    {
        $this->service = 'autoscaling';
    }

    /**
     * @return array
     */
    public function getList()
    {
        $list = $this->client->describeAutoScalingGroups([]);
        return $this->checkInstances($list->get('AutoScalingGroups'));
    }

    /**
     * @param $list
     * @return array
     */
    private function checkInstances($list)
    {
        $instancesExist = function($item) {
            if (count($item['Instances'])) {
                return true;
            }
            return false;
        };

        $instancesCount = function($item) {
            $item['InstancesCount'] = count($item['Instances']);
            return $item;
        };

        return collect($list)
            ->filter($instancesExist)
            ->map($instancesCount)
            ->toArray()
            ;
    }
}