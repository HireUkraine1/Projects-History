<?php

use Aws\Common\Aws;
use Aws\S3\Exception\S3Exception;

class PerformerController extends BaseController
{

    /*
    |--------------------------------------------------------------------------
    | Default Concert Controller
    |--------------------------------------------------------------------------
    |
    | You may wish to use controllers instead of, or in addition to, Closure
    | based routes. That's great! Here is an example controller method to
    | get you started. To route to this controller, just add the route:
    |
    |	Route::get('/', 'ConcertController@showWelcome');
    |
    */
    protected $layout = 'admin.layouts.default';
    protected $conf = '';

    public function __construct()
    {
        $this->conf = App::make('conf');
    }


    public function performer_admin($query = null)
    {
        if ($query):
            if (is_numeric($query)):
                $performer = Performer::find($query)
                    ->with('featured')
                    ->with('concerts')
                    ->with('concerts.performers')
                    ->with('concerts.location')
                    ->with('concerts.venue')->first();
            else: //diz be slug
                $performer = Performer::where('slug', $query)
                    ->with('featured')
                    ->with('concerts')
                    ->with('concerts.performers')
                    ->with('concerts.location')
                    ->with('concerts.venue')->first();
            endif;
            $this->layout->content = View::make('admin.performer.single', array('performer' => $performer, 'feat' => false));
        else:
            $featuredAll = FeaturedPerformer::with('performer')->with('performer.images')->get();
            $this->layout->content = View::make('admin.performer.feature-content', ['featured' => $featuredAll]);
        endif;
        $this->layout->customjs = View::make('admin.performer.cssjs');
        // $this->layout->mainmenu = View::make('admin.mainmenu');
    }

    public function performer_mass_action()
    {
        $ids = Input::get('ps');
        if (!$ids) return Redirect::to('/saleadminpanel/performer');
        $perfs = [];
        foreach ($ids as $id):
            $performer = Performer::find($id);
            $imgs = $performer->get_images_arr();
            $perfs[$id]['name'] = $performer->name;
            $perfs[$id]['slug'] = $performer->slug;
            $perfs[$id]['thumb'] = (isset($imgs['sale']['thumb'])) ? $imgs['sale']['thumb'] : '/images/missing.png';
            $perfs[$id]['main'] = (isset($imgs['sale']['main'])) ? $imgs['sale']['main'] : '/images/missing.png';
            $perfs[$id]['feat'] = $performer->featured()->get()->toArray();
        endforeach;
        //set full width:
        $this->layout->cssjs = View::make('admin.performer.cssjs');
        $this->layout->content = View::make('admin.performer.massaction', array('perfs' => $perfs));
    }

    public function upload_image()
    {
        $this->layout = null;
        $name = Input::get('performer');
        $performer = new Performer;
        $performer = $performer->gesaleySlug($name);
        $size = Input::get('size');
        $file = Input::file('Filedata');
        $ext = $file->getClientOriginalExtension();
        $filename = "{$name}.{$ext}";
        $key = "performer-images/{$size}/$filename";
        $filepath = $file->getRealPath();
        $type = $file->getMimeType();
        if (S3Helper::put($filepath, $key, $type)):
            $fullpath = S3Helper::get_path($key);
            $img = Image::firstOrCreate(['path' => $fullpath]);
            $img->save();
            $performer->images()->detach($img->id);
            $performer->images()->attach($img->id, array('type' => 'sale', 'size' => $size));
            echo $fullpath;
        else:
            echo 0;
        endif;

    }

}