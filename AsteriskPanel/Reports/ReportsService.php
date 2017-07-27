<?php

namespace App\Reports;


class ReportsService
{
    /**
     * @var ReportStorage
     */
    protected $reportStorage;

    /**
     * @var array
     */
    public $limits;

    /**
     * Reports constructor.
     * @param ReportStorage $reportStorage
     * @param ReportLimits $limits
     */
    public function __construct(ReportStorage $reportStorage, ReportLimits $limits)
    {
        $this->reportStorage = $reportStorage;
        $this->limits = $limits->getList();
    }

    /**
     * Get Reports List
     *
     * @param bool $from
     * @param bool $to
     * @param bool $status
     * @param bool $host
     * @return ReportStorage|mixed
     */
    public function getList($from = false, $to = false, $status = false, $host = false, $lastReport=true)
    {
        if ($to || $from) {
            $from = $from . ' 00:00:00';
            $to = $to . ' 23:59:59';
        }

        $reports = $this->reportStorage->getReportList($from , $to);

        if($lastReport) {
            $reports = $this->reportStorage->filterLastReport($reports);
        }

        if ($host) {
            $reports = $this->reportStorage->filterByHost($reports, $host);
        }

        if ($status) {
            $reports = $this->reportStorage->filterByStatus($reports, $status);
        }
        return $reports;
    }

}