<?php

use Aws\Common\Aws;
use Aws\S3\Exception\S3Exception;
use Illuminate\Support\Facades\Validator;

class AnnouncementController extends BaseController
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

    public function news_admin($article = null)
    {
        $get = Input::all();
        $sort = (Input::has('sort')) ? Input::get('sort') : 'publish_date';
        $order = (Input::has('order')) ? Input::get('order') : 'desc';
        if (Input::has('category')):
            $catId = $get['category'];
            $announcements = Announcement::orderBy($sort, $order)->whereHas('categories', function ($q) use ($catId) {
                $q->where('categories.id', $catId);
            })->with('performer')->paginate(20);
        else:
            $announcements = Announcement::orderBy('publish_date', 'DESC')->with('categories')->with('performer')->paginate(20);
        endif;
        $cats = Category::orderBy('category', 'ASC')->with('announcements')->get();

        $this->layout->customjs = View::make('admin.news.cssjs');
        $this->layout->content = View::make('admin.news.all', array('announcements' => $announcements, 'categories' => $cats));
        // $this->layout->sidebar = View::make('admin.news.sidebar', array());
        $this->layout->mainmenu = View::make('admin.mainmenu');
    }

    public function remove_announcement($id = null)
    {
        $post = Announcement::find($id);
        if ($post):
            if (Input::has('confirm')):
                $message = "<p><strong>" . $post->title . "</strong> DELETED!</p>";
                if (Input::get('redirect')):
                    $preslug = $this->conf['annuncements_index_url'];
                    UrlHelper::createRedirect($preslug . $post->slug, '/404', 301);
                    $message .= "a 404 redirect created for <strong> $post->slug</strong>";
                endif;
                $post->categories()->detach(); //remove all cats
                S3Helper::delete($post->image_key);
                $post->delete(); //delete
                Session::flash('message', $message);
                return Redirect::to('/saleadminpanel/news');
            endif;
            $this->layout->customjs = View::make('admin.news.cssjs');
            $this->layout->content = View::make('admin.news.remove', array('post' => $post));
            $this->layout->sidebar = View::make('admin.news.announcement-side', array());
            $this->layout->mainmenu = View::make('admin.mainmenu');
        else:
            return Redirect::to('/saleadminpanel/news');
        endif;
    }

    public function edit_announcement($id = null)
    {
        if (Input::has('aid')):
            $redirect = '';
            $aid = Input::get('aid');
            $form = Input::get();
            $day = date("Y-m-d", strtotime($form['publish-date']));
            $hour = date("H:i", strtotime($form['publish-time']));
            $time = $day . ' ' . $hour;
            $user = Auth::user();
            $post = Announcement::where('id', $aid)->with('performer')->first();
            if ($form['a-type'] == 'link'):
                $post->text = '';
                $post->is_page = false;
                $post->performer_id = $form['pid'];
                $slug = $form['slug'];
                $post->slug = $slug;
            else:
                $wasPage = $post->is_page;
                $post->text = $form['text'];
                $post->is_page = true;
                $post->performer_id = null;
                $oldSlug = $post->slug;

                $slug = $this->conf['annuncements_index_url'] . $form['slug'];
                if ($wasPage && ($oldSlug != $slug)):
                    $redirect = "<p>Created slug redirect from <strong>{$oldSlug}</strong> to <strong>{$slug}</strong>";
                    UrlHelper::createRedirect($oldSlug, $slug, 301);
                endif;

            endif;
            $post->status = ($form['draft'] == 0) ? 0 : 1;
            // $post->admin_id			= $user->id;
            $post->excerpt = $form['excerpt'];
            $post->title = $form['title'];
            $post->note = $form['note'];
            $post->publish_date = $time;

            if (Input::hasFile('image') || isset($form['remove-image'])): //either we remove or we replace
                if ($file = Input::file('image')): //there is a new file
                    if ($post->image_key):
                        S3Helper::delete($post->image_key);
                    endif;

                    $ext = $file->getClientOriginalExtension();
                    $filename = StringHelper::create_slug($form['slug']) . "-" . rand(1, 100) . ".{$ext}";
                    $key = "news-images/$filename";
                    $filepath = $file->getRealPath();
                    $type = $file->getMimeType();
                    S3Helper::put($filepath, $key, $type);
                    $post->image_key = $key;
                    $post->image_path = S3Helper::get_path($key);
                else: // there is no replace, check if remove
                    if ($form['remove-image']):
                        S3Helper::delete($form['remove-image']);
                        $post->image_key = null;
                        $post->image_path = null;
                    endif;
                endif;
            endif;
            $post->save();
            $post->categories()->detach(); //remove old relationship
            $catwarning = "<br>IN: ";
            if (isset($form['category'])):
                foreach ($form['category'] as $cat):
                    $category = Category::where('slug', '=', $cat)->first();
                    $post->categories()->attach($category);
                    $catwarning .= "<span class='label label-default'>$category->category </span>&nbsp";
                endforeach;
                $catwarning .= " categories";
            else:
                $catwarning = "<br><strong style='color:red'>Announcement does not have any categories... nobody will ever see it!</strong>";
            endif;
            Session::flash('message', '<h5>Succesfully edited <strong>' . $post->title . '</strong> announcement!</h5>' . $redirect . $catwarning);
            return Redirect::to('/saleadminpanel/news');
        endif;

        if ($id):
            $announcement = Announcement::find($id);
            $announcementDay = date("d/m/Y", strtotime(substr($announcement->publish_date, 0, -9)));

            $announcementHour = date("h:i A", strtotime(substr($announcement->publish_date, -8)));
            $cats = Category::all();
            $postCats = $announcement->categories()->get();
            $sluglist = array();
            foreach ($postCats as $c) :
                array_push($sluglist, $c->slug);
            endforeach;


            $performer_id = Input::get('performerid');
            $this->layout->customjs = View::make('admin.news.cssjs');
            //$category = Category::where('slug','=', $editingCat)->first();
            $this->layout->content = View::make('admin.news.edit', array('announcementDay' => $announcementDay, 'announcementHour' => $announcementHour, 'post' => $announcement, 'categories' => $cats, 'postcats' => $sluglist));
            $this->layout->sidebar = View::make('admin.news.announcement-side', array());
            $this->layout->mainmenu = View::make('admin.mainmenu');
        else:
            return Redirect::to('/saleadminpanel/news');
        endif;
    }

    public function add_announcement()
    {
        if (Input::has('title')):
            $form = Input::get();
            $day = date("Y-m-d", strtotime($form['publish-date']));
            $hour = date("H:i", strtotime($form['publish-time']));
            if ($form['publish-now'] == 1):
                $time = $day . " " . date('H:i');
            else:
                $time = $day . ' ' . $hour;
            endif;

            $user = Auth::user();
            if ($form['a-type'] == 'link'):
                $post = Announcement::firstOrCreate([
                    'slug' => Performer::find($form['pid'])->slug, // not overrride this
                ]);
                $post->text = '';
                $post->is_page = false;
                $post->performer_id = $form['pid'];

            else:
                $post = Announcement::firstOrCreate([
                    'slug' => StringHelper::create_slug($form['slug'])
                ]);
                $post->text = $form['text'];
                $post->is_page = true;
                $post->performer_id = null;
            endif;
            $post->status = 1;
            $post->admin_id = $user->id;
            $post->excerpt = $form['excerpt'];
            $post->title = $form['title'];
            $post->note = $form['note'];
            $post->publish_date = $time;

            if (Input::hasFile('image')):
                $file = Input::file('image');
                $awsConfig = Config::get("aws");
                $aws = Aws::factory($awsConfig);
                $cdn = VarsHelper::get_cdn('aws');
                $ext = $file->getClientOriginalExtension();
                $filename = StringHelper::create_slug($form['slug']) . "-" . rand(1, 100) . ".{$ext}";
                $key = "news-images/$filename";
                $filepath = $file->getRealPath();
                $type = $file->getMimeType();
                S3Helper::put($filepath, $key, $type);
                $post->image_key = $key;
                $post->image_path = S3Helper::get_path($key);
            endif;
            $post->save();
            $post->categories()->detach(); //remove old relationship

            if (isset($form['announce-buffer'])):
                //var_dump($post->image_path);die();
                $this->buffer($form, $post->image_path);
            endif;

            $catwarning = "<br>IN: ";
            if (isset($form['category'])):
                foreach ($form['category'] as $cat):
                    $category = Category::where('slug', '=', $cat)->first();
                    $post->categories()->attach($category);
                    $catwarning .= "<span class='label label-default'>$category->category </span>&nbsp";
                endforeach;
                $catwarning .= " categories";
            else:
                $catwarning = "<br><strong style='color:red'>Announcement does not have any categories... nobody will ever see it!</strong>";
            endif;
            Session::flash('message', '</h5>Succesfully created <strong>' . $post->title . '</strong> announcement!</h5>' . $catwarning);
            return Redirect::to('/saleadminpanel/news');
        endif;

        $cats = Category::all();
        $performer_id = Input::get('pid');

        $bufferSettings = SiteSetting::wherelabel('twitter')->orWhere('label', 'LIKE', "google")->orWhere('label', 'LIKE', "facebook")->get();

        $this->layout->cssjs = View::make('admin.news.cssjs');
        if ($performer_id):
            $performer = Performer::find($performer_id);
            $this->layout->content = View::make('admin.news.createperformer', array('categories' => $cats, 'performer' => $performer));
        else:
            $this->layout->content = View::make('admin.news.create', array('categories' => $cats, 'bufferSettings' => $bufferSettings));
        endif;
    }

    public function buffer($post, $imagePath)
    {
        try {
            $url = 'xxxxxxxxxxxxxxxxxxxx';
            $text = $post['buffer-text'] . ' ' . $_SERVER['HTTP_HOST'] . '/concerts/' . $post['slug'];
            $day = date("Y-m-d", strtotime($post['publish-date']));
            $hour = date("H:i", strtotime($post['publish-time']));
            $time = $day . ' ' . $hour;

            foreach ($post as $key => $value):
                $settings = SiteSetting::wheredescription($key)->first();
                if (is_object($settings)):
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url); // SEND TO URL
                    curl_setopt($ch, CURLOPT_HEADER, 0); // EMPTY HEADERS
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // RETURN SERVER ANSVER
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0); // GO TO REDIRECTION
                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);// TIMEOUT
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// SWICH OFF SSL_VERIFYPEER
                    curl_setopt($ch, CURLOPT_POST, 1); // USE Post data
                    if ($post['publish-now'] == 1):
                        curl_setopt($ch, CURLOPT_POSTFIELDS, array(
                            'text' => $text,
                            'profile_ids[]' => $settings->value,
                            'now' => 1,
                        ));
                    else:
                        curl_setopt($ch, CURLOPT_POSTFIELDS, array(
                            'text' => $text,
                            'profile_ids[]' => $settings->value,
                            'scheduled_at' => $time,
                        ));
                    endif;
                    curl_close($ch);
                endif;
            endforeach;
        } catch (Exception $e) {
            echo 'Exception text:' . $e->getMessage();
        }

    }

    /***************************************************************************
     *
     * This is category admin admin
     ****************************************************************************/
    public function categories_admin($id = null)
    {
        $categories = Category::orderBy('order', 'ASC')->get();
        if ($id = Input::get('edit')):
            $category = Category::find($id);
            if (!$category)
                return Redirect::to('/saleadminpanel/news/managecategories');
            $this->layout->content = View::make('admin.news.singlecategoryedit', array('category' => $category, 'categories' => $categories));
        else:
            $this->layout->content = View::make('admin.news.allcategories');
        endif;
        $this->layout->cssjs = View::make('admin.news.categorycssjs');
        //$category = Category::where('slug','=', $editingCat)->first();
        $this->layout->sidebar = View::make('admin.news.categoryadmin-side', array('categories' => $categories));
        $this->layout->mainmenu = View::make('admin.mainmenu');
    }
}