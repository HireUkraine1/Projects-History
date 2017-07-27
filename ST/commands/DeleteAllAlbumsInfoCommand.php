<?php

use Dandelionmood\LastFm\LastFm;
use Guzzle\Http\Client;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class DeleteAllAlbumsInfoCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'miner:delete-albums-info';

    /**
     * The console command description.
     *cd /opt
     *
     * @var string
     */
    protected $description = 'Delete all available albums and tracks data for all performer';

    protected $lfm;

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
        ini_set("memory_limit", "-1");
        DB::disableQueryLog();
        DB::connection('mysql')->disableQueryLog();

        $this->delPerformerAlbum();
    }

    public function delPerformerAlbum($skip = 0, $i = 1)
    {

        if ($i <= 10) {

            $performers = Performer::take(1)->skip($skip)->orderBy("id")->get(array('id', 'mbz_id', 'name'));

            try {
                foreach ($performers as $performer):
                    echo "\n\r $i \n\r";
                    $i++;
                    $albums = $performer->albums;
                    foreach ($albums as $album):
                        $images = $album->images;
                        //del img
                        foreach ($images as $image):
                            $path = $image->path;
                            $pattern = 's3.amazonaws.com/';
                            $s3 = strripos($path, $pattern);

                            if ($s3 === false) {
                                echo "\n\r $path del from db \n\r";
                                //X $img=Image::where('path', $path)-> delete();
                            } else {
                                $replays = array("https://s3.amazonaws.com/site-dev/" => "");
                                $key = strtr($path, $replays);
                                echo "\n\r $key  1-del from s3; 2-del from db \n\r";
                            }

                        endforeach;
                        echo "\n\r $album->title - $album->performer \n\r";
                        //del album
                        $albumId = $album->id;
                        $delAlbum = Album::where('id', $albumId)->first();
                        $delAlbum->delete();

                    endforeach;

                endforeach;

                $skip += 1;
                $this->delperformeralbum($skip, $i);
            } catch (Exception $e) {
                echo "ERR\n\r" . $e->getMessage() . "\n\r" . $e->getTraceAsString();

            }

        }
    }

}