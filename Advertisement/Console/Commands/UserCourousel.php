<?php

namespace App\Console\Commands;

use App\Worker;
use Illuminate\Console\Command;

class UserCourousel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user-courousel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Перемешиваем пользователей для изменении позиции в выдачи';

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
    public function handle()
    {
        $this->userCourousel();
    }

    /**
     * Mix user's position
     */
    public function userCourousel()
    {
        try {
            $workers = Worker::all();
            $count = $workers->count();
            $arrayWorkerId = range(1, $count);
            shuffle($arrayWorkerId);
            echo count($arrayWorkerId);
            $i = 0;
            foreach ($workers as $worker):
                $worker->position = $arrayWorkerId[$i];
                $worker->save();
                $i++;
            endforeach;
            \Mail::raw('Обновление списка пользователей прошло удачно', function ($message) {
                $message->from('Site@site.com', 'Site');

                $message->to('email@site.com')->subject('Обновление списка пользователей прошло удачно');;
            });
        } catch (\Exception $e) {

            \Mail::raw('Обновление списка пользователей прошло неудачно!', function ($message) {
                $message->from('Site@site.com', 'Site');

                $message->to('email@site.com')->subject('Обновление списка пользователей прошло неудачно!');;
            });
        }
    }

}
