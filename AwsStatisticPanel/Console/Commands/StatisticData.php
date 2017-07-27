<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Statistic\StatisticService as Statistic;
use App\Statistic\Statistic as StatisticDB;

class StatisticData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statistic:data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get statistic data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(Statistic $statistic)
    {
        $staticObj = $statistic->getObjectCurrentStatistic();
        return StatisticDB::create(['serialize_object'=>serialize($staticObj)]);
    }
}
