<?php

namespace App\Reports;

use Elasticsearch\ClientBuilder;
use function GuzzleHttp\Promise\inspect_all;

class ReportStorage
{

    /**
     * @var int
     */
    private $size = 1000;

    /**
     * @var int
     */
    private $max = 1000;

    /**
     * @var string
     */
    private $index = 'alex';

    /**
     * @var string
     */
    private $dateFrom = '-1 day';

    /**
     * @var string
     */
    private $dateFormat = 'Y-m-d\TH:m:s.v\Z';

    /**
     * @var array
     */
    private $colums = [
        'dashboard',
        'content',
        'product',
        'insight'
    ];

    /**
     * @var \Elasticsearch\Client
     */
    private $client;

    /**
     * @var ReportLimits
     */
    private $reportLimits;

    /**
     * ReportStorage constructor.
     * @param ReportLimits $reportLimits
     */
    public function __construct(ReportLimits $reportLimits)
    {
        $this->client = $this->setClient();
        $this->reportLimits = $reportLimits;
    }

    /**
     * @return \Elasticsearch\Client|mixed
     */
    private function setClient()
    {
        if (!$this->client) {
            $this->client = ClientBuilder::create()
                ->setHosts(config('elastic'))
                ->build();
        }
        return $this->client;
    }

    /**
     * Get Report List
     *
     * @param $from
     * @param $to
     * @return ReportStorage
     */
    public function getReportList($from, $to)
    {
        $query = [
            'bool' => [
                'filter' => [
                    'range' => [
                        'date' => [
                            'gte' => $this->getDateFromFormat($from),
                            'lte' => $this->getDateToFormat($to),
                        ]
                    ]
                ]
            ]
        ];
        $sort = [
            'date' => [
                'order' => 'desc'
            ]
        ];
        $params = [
            'index' => $this->index,
            'size' => $this->size,
            'body' => [
                'query' => $query,
                'sort' => $sort,
            ],
        ];
        $result = $this->client->search($params);
        return $this->convert($result);
    }

    /**
     * Filter Reports by host name
     * @param $reports
     * @param $host
     * @return mixed
     */
    public function filterByHost($reports, $host)
    {
        return $reports->filter(function ($value, $key) use ($host) {
            return $key == $host;
        });
    }

    /**
     * Filter Reports by status
     *
     * @param $reports
     * @param $status
     * @return mixed
     */
    public function filterByStatus($reports, $status)
    {
        $range = $this->getLimitRange($status);
        if ($range['from'] == 0 && $range['to'] == $this->max && $range['warning'] == true) {
            return $reports;
        }
        $statusFilter = function ($report) use ($range) {
            $item = $report->map(function ($item) use ($range) {
                $this->pageActive = 0;
                if ($range['from'] == 0 && $range['to'] == $this->max && $range['warning'] == false) {
                    $limit = $this->reportLimits->getList();
                    foreach ($this->colums as $page) {
                        if ($item[$page] > $limit['success'] && $item[$page] < $limit['warning']) {
                            $this->pageActive++;
                            $item[$page] = false;
                        }
                    }
                    if ($this->pageActive == 4) {
                        return false;
                    }
                    return $item;
                }
                foreach ($this->colums as $page) {
                    if ($item[$page] < $range['from'] || $item[$page] > $range['to']) {
                        $this->pageActive++;
                        $item[$page] = false;
                    }
                }
                if ($this->pageActive == 4) {
                    return false;
                }
                return $item;
            });
            return $item;
        };
        $empty = function ($items) {
            return $items->filter(function ($item) {
                if ($item) {
                    return true;
                }
                return false;
            });
        };

        $filter = function ($item) {
            if ($item->count()) {
                return true;
            }
            return false;
        };

        return $reports
            ->map($statusFilter)
            ->map($empty)
            ->filter($filter);
    }

    public function filterLastReport($reports){
        $lastReport = [];
        $reports->each(function ($hostreports, $host) use(&$lastReport){
            $lastReport[$host] = [$hostreports->first()];
        });

        return collect($lastReport);
    }

    /**
     * get Limit Range
     *
     * @param $status
     * @return array
     */
    private function getLimitRange($status)
    {
        $normal = $warning = $critical = false;
        $from = $to = 0;
        $limit = $this->reportLimits->getList();
        if (in_array(0, $status)) {
            $normal = true;
            $from = 0;
            $to = $limit['success'];
        }
        if (in_array(1, $status)) {
            $warning = true;
            $from = $normal ? 0 : $limit['success'];
            $to = $limit['warning'];
        }
        if (in_array(2, $status)) {
            $from = $limit['warning'];
            if ($warning) {
                $from = $limit['success'];
            }
            if ($normal) {
                $from = 0;
            }
            $to = $this->max;
        }
        return compact('from', 'to', 'warning');
    }

    /**
     * convert result from Elastic Search to collection
     * @param $data
     * @return static
     */
    private function convert($data)
    {
        $toArray = function ($item) {
            return $item['_source'];
        };

        return collect($data['hits']['hits'])
            ->map($toArray)
            ->groupBy('host');
    }

    /**
     * @param $date
     * @return false|string
     */
    private function getDateFromFormat($date)
    {
        if (!$date) {
            return date($this->dateFormat, strtotime($this->dateFrom));
        }
        return date($this->dateFormat, strtotime($date));
    }

    /**
     * @param $date
     * @return false|string
     */
    private function getDateToFormat($date)
    {
        if (!$date) {
            return date($this->dateFormat);
        }
        return date($this->dateFormat, strtotime($date));
    }


}