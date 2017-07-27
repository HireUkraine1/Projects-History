<?php

use Aws\Common\Aws;
use Aws\S3\Exception\S3Exception;

class AdminController extends BaseController
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
        if ($article):
            if ($article == 'nomadsposts'):
                $announcements = Announcement::has('categories', '=', 0)->paginate(10);
            else:
                $category = Category::where('slug', $article)->first();
                $announcements = $category->announcements()->orderBy('publish_date', 'DESC')->paginate(10);
            endif;
        else:
            $announcements = Announcement::orderBy('publish_date', 'DESC')->paginate(10);
        endif;
        $cats = Category::orderBy('category', 'ASC')->get();
        $this->layout->cssjs = View::make('admin.news.cssjs');
        $this->layout->content = View::make('admin.news.all', array('announcements' => $announcements));
        $this->layout->sidebar = View::make('admin.news.sidebar', array('categories' => $cats));
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
            $this->layout->cssjs = View::make('admin.news.cssjs');
            //$category = Category::where('slug','=', $editingCat)->first();
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
            $user = Auth::user();
            $post = Announcement::find($aid);
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
            $post->status = (isset($form['draft'])) ? 0 : 1;
            // $post->admin_id			= $user->id;
            $post->excerpt = $form['excerpt'];
            $post->title = $form['title'];
            $post->note = $form['note'];
            $post->publish_date = date('Y-m-d', strtotime($form['publish-date'])) . " " . date('H:i:s');

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
            $cats = Category::all();
            $postCats = $announcement->categories()->get();
            $sluglist = array();
            foreach ($postCats as $c) :
                array_push($sluglist, $c->slug);
            endforeach;
            $performer_id = Input::get('performerid');
            $this->layout->cssjs = View::make('admin.news.cssjs');
            $this->layout->content = View::make('admin.news.edit', array('post' => $announcement, 'categories' => $cats, 'postcats' => $sluglist));
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
            $post->status = (isset($form['draft'])) ? 0 : 1;
            $post->admin_id = $user->id;
            $post->excerpt = $form['excerpt'];
            $post->title = $form['title'];
            $post->note = $form['note'];
            $post->publish_date = date('Y-m-d', strtotime($form['publish-date'])) . " " . date('H:i:s');

            if (Input::hasFile('image')):
                $file = Input::file('image');
                //Only do CDN if needed
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
        $this->layout->cssjs = View::make('admin.news.cssjs');
        //$category = Category::where('slug','=', $editingCat)->first();
        if ($performer_id):
            $performer = Performer::find($performer_id);
            $this->layout->content = View::make('admin.news.createperformer', array('categories' => $cats, 'performer' => $performer));
        else:
            $this->layout->content = View::make('admin.news.create', array('categories' => $cats));
        endif;
        $this->layout->sidebar = View::make('admin.news.announcement-side', array());
        $this->layout->mainmenu = View::make('admin.mainmenu');
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
        $this->layout->sidebar = View::make('admin.news.categoryadmin-side', array('categories' => $categories));
        $this->layout->mainmenu = View::make('admin.mainmenu');
    }

    public function logout()
    {
        Session::flush();
        return Redirect::to('/');
    }

}