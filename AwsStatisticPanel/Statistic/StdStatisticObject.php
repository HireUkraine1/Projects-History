<?php

namespace App\Statistic;


class StdStatisticObject
{
    /**
     * @var array
     */
    private $_sqs;

    /**
     * @var array
     */
    private $_asg;

    /**
     * @var integer
     */
    private $_metric;

    /**
     * @var string
     */
    private $_date;

    /**
     * @var string
     */
    private $_spendModeyDay;

    /**
     * @var array
     */
    private $statisticConvertProperty = [
        'asg', 'sqs'
    ];

    /**
     * @var array
     */
    private $sqs_columns = [
        'Name',
        'Messages Available',
        'Messages in Flights'
    ];

    /**
     * @var array
     */
    private $sqs_attributes = [
        '_key_',
        'ApproximateNumberOfMessages',
        'ApproximateNumberOfMessagesNotVisible'
    ];

    /**
     * @var array
     */
    private $asg_columns = [
        'Name',
        'Instances',
        'Desired'
    ];

    /**
     * @var array
     */
    private $asg_attributes = [
        'AutoScalingGroupName',
        'InstancesCount',
        'DesiredCapacity'
    ];

    /**
     * StdStatisticObject constructor.
     * @param array $properties
     */
    public function __construct(Array $properties = array())
    {
        foreach ($properties as $key => $value) {
            if (in_array($key, $this->statisticConvertProperty)) {
                $this->{'_' . $key} = $this->convertProperty($key, $value);
            } else {
                $this->{'_' . $key} = $value;
            }
        }
    }

    /**
     * @param $name
     * @param $values
     * @return array
     */
    private function convertProperty($name, $values)
    {
        $columns = $this->{$name . '_columns'};
        $data = [];
        foreach ($values as $key=>$value){
            $row=[];
            foreach ($this->{$name . '_attributes'} as $attributeValue) {
                if($attributeValue == '_key_') {
                    $row[] = $key;
                    continue;
                }
                $row[] = $values[$key][$attributeValue];
            }

            $data[] = array_combine($columns, $row);
        }
        return $data;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->{'_' . $name};
    }


}
