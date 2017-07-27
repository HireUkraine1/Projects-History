<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GetDBCity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get-db-city';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Скачиваем новую БД c ip и регионами';

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
        $this->getCity();
    }

    /**
     * Get city
     */
    function getCity()
    {
        try {
            $geoLite2 = public_path('GeoLite2-City.mmdb.gz');
            $geoLite2mmdb = public_path('GeoLite2-City.mmdb');

            if (file_exists($geoLite2)) {
                unlink($geoLite2);
            }

            $curl = curl_init('http://geolite.maxmind.com/download/geoip/database/GeoLite2-City.mmdb.gz');
            $fp = fopen($geoLite2, 'w');
            curl_setopt($curl, CURLOPT_FILE, $fp);
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_exec($curl);
            curl_close($curl);
            fclose($fp);

            if ($geoLite2) {
                if (file_exists($geoLite2mmdb)) {
                    unlink($geoLite2mmdb);
                }
                echo exec('gzip -d ' . $geoLite2mmdb);
                echo exec('chmod 755 ' . $geoLite2mmdb);
            };

            if (file_exists($geoLite2mmdb)) {
                \Mail::raw('Обновление базы ip прошло успешно!', function ($message) {
                    $message->from('Site@site.com', 'Site');

                    $message->to('email@site.com')->subject('Обновление базы ip прошло успешно!');;
                });
            } else {
                \Mail::raw('Обновление базы ip прошло неудачно!', function ($message) {
                    $message->from('Site@site.com', 'Site');

                    $message->to('email@site.com')->subject('Обновление базы ip прошло неудачно!');;
                });
            }
        } catch (\Exception $e) {
            \Mail::raw('Обновление базы ip прошло неудачно!', function ($message) {
                $message->from('Site@site.com', 'Site');

                $message->to('email@site.com')->subject('Обновление базы ip прошло неудачно!');;
            });

        }
    }
}
