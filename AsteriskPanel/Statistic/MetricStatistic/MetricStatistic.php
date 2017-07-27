<?php

namespace App\Statistic\MetricStatistic;

use App\Statistic\ClientAbstract;

class MetricStatistic extends ClientAbstract
{
    protected function setService()
    {
        $this->service = 'cloudwatch';
    }

    public function getList()
    {
        $todayMetricStatistics = $this->client->getMetricStatistics([
            'Namespace' => 'AWS/Billing',
            'MetricName' => 'EstimatedCharges',
            'Dimensions' => [
                [
                    'Name'  => 'Currency',
                    'Value' => 'USD',
                ]
            ],
            'StartTime' => strtotime('midnight') - 60 * 60 * 24,
            'EndTime' => strtotime('midnight') - 1,
            'Period' => 60,
            'Statistics' => ['Maximum'],
        ]);

        $yesterdayMetricStatistics = $this->client->getMetricStatistics([
            'Namespace' => 'AWS/Billing',
            'MetricName' => 'EstimatedCharges',
            'Dimensions' => [
                [
                    'Name'  => 'Currency',
                    'Value' => 'USD',
                ]
            ],
            'StartTime' => strtotime('midnight') - 60 * 60 * 24 * 2,
            'EndTime' => strtotime('midnight') - 60 * 60 * 24 - 1,
            'Period' => 60,
            'Statistics' => ['Maximum'],
        ]);

        $today = $this->convert($todayMetricStatistics);
        $yesterday = $this->convert($yesterdayMetricStatistics);
        $spend = end($today)['Maximum'] - end($yesterday)['Maximum'];

        return compact('today', 'yesterday', 'spend');
    }


    public function getSpend(){
        $data=$this->getList();
        return $data['spend'];
    }
    private function convert($datapoints)
    {
        return collect($datapoints->get('Datapoints'))
            ->sortBy('Timestamp')
            ->toArray()
            ;
    }


}