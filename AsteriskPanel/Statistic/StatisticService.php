<?php

namespace App\Statistic;

use Carbon\Carbon;

class StatisticService
{
    /**
     * @var Sqs\Sqs
     */
    private $sqs;

    /**
     * @var Asg\AutoScalingGroup
     */
    private $asg;

    /**
     * @var MetricStatistic\MetricStatistic
     */
    private $metric;

    /**
     * StatisticService constructor.
     * @param Sqs\Sqs $sqs
     * @param Asg\AutoScalingGroup $asg
     * @param MetricStatistic\MetricStatistic $metric
     */
    public function __construct(
        Sqs\Sqs $sqs,
        Asg\AutoScalingGroup $asg,
        MetricStatistic\MetricStatistic $metric
    )
    {
        $this->sqs = $sqs;
        $this->asg = $asg;
        $this->metric = $metric;
    }


    public function getCurrentStatistic()
    {
        $carbon = Carbon::now();
        $date = $carbon->toDateString();
        $spendModeyDay = $carbon->subDays(2)->format("Y-m-d");
        $metric = $this->metric->getSpend();
        $sqs = $this->sqs->getList();
        $asg = $this->asg->getList();

        return compact('sqs', 'asg', 'metric', 'date', 'spendModeyDay');
    }

    public function getObjectCurrentStatistic()
    {
        $currentStatistic = $this->getCurrentStatistic();
        $objectStatistic = new StdStatisticObject($currentStatistic);

        return $objectStatistic;
    }
}