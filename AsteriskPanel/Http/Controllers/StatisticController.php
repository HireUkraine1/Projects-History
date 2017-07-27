<?php

namespace App\Http\Controllers;

use App\Statistic;

class StatisticController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $statistics = Statistic\Statistic::paginate(15);

        return view('statistic', ['statistics' => $statistics]);
    }

    /**
     * Show statistic for selected day
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        $statistics = Statistic\Statistic::findOrFail($id);

        return view('show-day-statistic', ['data' => unserialize($statistics->serialize_object)]);
    }

    /**
     * Get statistic by AJAX
     *
     * @param Statistic $statistic
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getAjaxStatistic(Statistic\StatisticService $statistic)
    {
        $data = $statistic->getObjectCurrentStatistic();

        return view('tables.statistic', ['data' => $data]);
    }
}

