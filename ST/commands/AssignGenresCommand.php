<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class AssignGenresCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'miner:assign-genres';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign genres to concerts.';

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
        $this->assignGenres();
    }

    public function assignGenres()
    {
        $events = Concert::with('performers')->get();
        foreach ($events as $e):
            foreach ($e->performers as $p):
                $genre = Genre::find($p->genre_id);
                $e->genres()->detach($genre->id);
                $e->genres()->attach($genre);
            endforeach;
        endforeach;

    }

}
