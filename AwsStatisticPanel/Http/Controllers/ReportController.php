<?php

namespace App\Http\Controllers;

use App\Reports\ReportsService;
use App\Http\Requests\ReportFilterRequest;

class ReportController extends Controller
{
    /**
     * @var Reports
     */
    private $reports;

    /**
     * ReportController constructor.
     *
     * @param Reports $reports
     */
    public function __construct(ReportsService $reports)
    {
        $this->reports = $reports;
    }

    /**
     * Reports list
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $reports = $this->reports->getList();
        $limits = $this->reports->limits;

        return view('report', compact('reports','limits'));
    }

    /**
     * Filtering all reports by request
     *
     * @param ReportFilterRequest $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function filter(ReportFilterRequest $request)
    {
        $status = $request->has('status')
            ? $request->status
            : false;

        $dateFrom = $request->has('date_from')
            ? $request->date_from
            : false;

        $dateTo = $request->has('date_to')
            ? $request->date_to
            : false;

        $host = $request->has('host')
            ? $request->host
            : false;

        $reports = $this->reports->getList($dateFrom, $dateTo, $status, $host, false);
        $limits =$this->reports->limits;

        return view('tables.report_list', compact(
            'reports',
            'limits'
        ));
    }
}