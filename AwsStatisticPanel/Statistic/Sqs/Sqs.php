<?php

namespace App\Statistic\Sqs;

use App\Statistic\ClientAbstract;

class Sqs extends ClientAbstract
{
    protected function setService()
    {
        $this->service = 'sqs';
    }

    /**
     * @return array
     */
    public function getList()
    {
        $listQueuesAttr = [];
        foreach (config('aws-sqs.list') as $name) {
            $listQueuesAttr[$name] = $this->client->getQueueAttributes([
                'AttributeNames' => ['ApproximateNumberOfMessages', 'ApproximateNumberOfMessagesNotVisible', 'QueueArn'],
                'QueueUrl' => $this->getFullQueueUrl($name),
            ]);
        }
        return $this->convertList($listQueuesAttr);
    }

    /**
     * @param $queue
     * @return string
     */
    private function getFullQueueUrl($queue)
    {
        return config('aws-sqs.url') . $queue;
    }

    /**
     * @param $list
     * @return array
     */
    private function convertList($list)
    {
        $map = function($item) {
            return $item->get('Attributes');
        };
        return collect($list)
            ->map($map)
            ->toArray()
            ;
    }
}