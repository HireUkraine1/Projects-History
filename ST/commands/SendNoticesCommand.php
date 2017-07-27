<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class SendNoticesCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'miner:send-notices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Email users.';

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
    public function fire()
    {

        $this->tourTracker();
    }

    public function tourTracker()
    {
        $activeFans = Fan::where('status', 1)->where('dont_bother', 0)->get();
        $now = time(); // or your date as well

        foreach ($activeFans as $fan):
            try {

                echo "\n\r Fan: $fan->name ";
                $currentNotifications = NotificationSettings::where('fan_id', $fan->id)->where('type', 'email')->with('performer')->with('performer.images')->get();
                $trackedPerformers = [];
                foreach ($currentNotifications as $cn):
                    $trackedPerformers[$cn->performer_id]['performer'] = $cn->performer;
                    $trackedPerformers[$cn->performer_id]['notifications'][] = $cn->days;
                endforeach;
                $upcomingConcerts = FanHelper::fetchUpcomingConcerts($fan->id);

                $notify = [];
                foreach ($upcomingConcerts as $up):
                    $trackingDays = $trackedPerformers[$up->id]['notifications'];

                    foreach ($up->upcoming_concerts as $uc):
                        $cdate = strtotime($uc->date);
                        $datediff = abs($cdate - $now);

                        $daysTill = floor($datediff / (60 * 60 * 24));
                        echo "\n\r $fan->id tracks $up->name - $up->id is in  $daysTill ";
                        if (in_array($daysTill, $trackingDays)): //has number of days tracked
                            $notify[$uc->id]['days'] = $daysTill;
                            $notify[$uc->id]['performer'] = $up;
                            $notify[$uc->id]['concert'] = $uc;
                        endif;
                    endforeach;
                endforeach;

                usort($notify, function ($a, $b) {
                    return $a['days'] - $b['days'];
                });
                if (count($notify)):
                    $this->_sendTracking($fan, $notify);
                    echo count($notify);
                endif;
            } catch (Exception $e) {
                echo "\n\r" . $e->getMessage();
                echo $e->getTraceAsString();

            }
        endforeach;
    }

    private function _sendTracking($fan, $notices)
    {
        echo "\n\r email sent to $fan->name ";
        Mail::send('emails.frontend.fans.upcoming-concerts', array('fan' => $fan, 'notices' => $notices), function ($message) use ($fan) {
            $message->to($fan->email, $fan->name)->subject('TourTracker: Concerts Near You!');
        });
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(// array('example', InputArgument::REQUIRED, 'An example argument.'),
        );
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(// array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
        );
    }

}
