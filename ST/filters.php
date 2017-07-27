<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function ($request) {
    if (substr($request->header('host'), 0, 4) === 'www.') {
        $request->headers->set('host', 'site.com');
        return Redirect::to($request->path());
    }
    // DEBUG
    $_ENV['debug'] = [];
    // Singleton (global) object
    App::singleton('conf', function () {
        $tmp = DB::table('site_settings')->get();
        $app = [];
        foreach ($tmp as $v):
            $app[$v->string_id] = $v->value;
        endforeach;
        return $app;
    });
    $conf = App::make('conf');
    View::share('conf', $conf);

    //get top performers
    $toptours = FeaturedPerformer::with('performer')
        ->with('performer.images')
        ->with('performer.upcoming_concerts')
        // ->with('performer.concerts.venue')
        ->where('side', 1)
        ->orderBy(DB::raw('RAND()'))
        ->get();

    View::share('toptours', $toptours);

    //set payment gateway construct
    try {
        $key = Config::get('stripe.stripe.secret');
        Stripe::setApiKey($key);
    } catch (Exception $e) {

    }
});


App::after(function ($request, $response) {

});


/* Fans Auth Filter


*/

Route::filter('jaxfan', function () {
    if (Session::has('fan') && Request::ajax()):
        $fan = Session::get('fan');
        $activeProfile = Session::get('activeProfile');
    else:
        $fan = false;
        $activeProfile = false;
        return Redirect::to('/stage');
    endif;

});

Route::filter('fanauth', function () {
    if (Session::has('fan')):
        $fan = Session::get('fan');
        $activeProfile = Session::get('activeProfile');
    else:
        $fan = false;
        $activeProfile = false;
    endif;
    View::share('fan', $fan);
    View::share('activeProfile', $activeProfile);
});

Route::filter('fansonly', function () {
    if (Session::has('fan')):
        $fan = Session::get('fan');
        $activeProfile = Session::get('activeProfile');
        $refan = Fan::where('id', $fan->id)->first();
        if ($refan->status === 0):
            $message = ['danger' => "This account was BLOCKED! Please <a href='/contact'>contact us</a>"];
            Session::forget('fan');
            Session::forget('activeProfile');
            return Redirect::to('/become-a-fan')->with('notifications', $message);
        endif;
    else:
        $fan = false;
        $activeProfile = false;
    endif;
    $message = ['info' => "Must be logged in to go here!"];
    $redirect = 1;
    $fromUrl = $_SERVER['REQUEST_URI'];//parse_url($_SERVER['HTTP_REFERER']);
    Session::flash('fromUrl', $fromUrl);
    Session::flash('redirect', $redirect);

    if (!$fan) return Redirect::to('/become-a-fan')->with('notifications', $message);
});

Route::filter('pendingTwitter', function () {
    if (Session::has('pendingTwitter')):
        $pendingTwitter = Session::get('pendingTwitter');
    else:
        return Redirect::to('/become-a-fan');
    endif;
    View::share('pendingTwitter', $pendingTwitter);
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function () {

    if (Auth::guest()) {

        if (Request::ajax()) {
            $error = ['error' => 1, 'message' => "Unauthorized request!"];
            return json_encode($error);
        } else {
            return Redirect::guest('/saleadminlogin');
        }
    }
});

Route::filter('ajax', function () {
    if (!Request::ajax()) {
        // $error = ['error' => 1,'message' => "jaxy-jax only!!!"];
        //    return  json_encode($error);
        return "non-ajax submission!";
    }
});

Route::filter('cityredirect', function () {
    $param = Request::segment(1);
    $params = explode('+', $param);
    //first is always city
    if ($redirect = UrlHelper::isRedirected($params[0])):
        $filter = (isset($params[1])) ? "+" . $params[1] : '';

        return Redirect::to($redirect->to_url . $filter, $redirect->redirect_type);
    endif;
});

Route::filter('checkcity', function () {
    $param = Request::segment(1);
    $params = explode('+', $param);
    $city = $params[0];
    //because we cannot just be fucking with no cities here
    $isFeat = FeaturedLocation::where('feat_slug', $city)->count();
    $isCity = Location::where('slug', $city)->count();
    if (!$isCity && !$isFeat):
        App::abort(404, "Not Found!");
        return Response::make("Not Found", 404);
    endif;
});

Route::filter('auth.basic', function () {
    return Auth::basic();
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function () {
    if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function () {
    if (Session::token() != Input::get('_token')) {
        throw new Illuminate\Session\TokenMismatchException;
    }
});

//IMPLEMENT THIS FOR BEFORE
Route::filter('toptours', function () {
    $toptours = FeaturedPerformer::with('performer')
        ->with('performer.images')
        // ->with('performer.concerts')
        // ->with('performer.concerts.venue')
        ->where('side', 1)
        ->orderBy(DB::raw('RAND()'))
        ->take($conf['side_max_top_tours'])
        ->take(10)
        ->get();

    View::share('toptours', $toptours);
});


//check permissions-performers
Route::filter('permissions-performers', function () {
    $permissions = [];
    foreach (Auth::user()->permission as $permission):
        $permissions[] = $permission->permission_id;
    endforeach;

    if (!in_array(1, $permissions)):
        return Redirect::to('/saleadminpanel');
    endif;
});

//check permissions-performers


//check permissions-memberds
Route::filter('permissions-members', function () {
    $permissions = [];
    foreach (Auth::user()->permission as $permission):
        $permissions[] = $permission->permission_id;
    endforeach;

    if (!in_array(2, $permissions)):
        return Redirect::to('/saleadminpanel');
    endif;
});
//permissions-members


//check permissions-news
Route::filter('permissions-news', function () {
    $permissions = [];
    foreach (Auth::user()->permission as $permission):
        $permissions[] = $permission->permission_id;
    endforeach;

    if (!in_array(3, $permissions)):
        return Redirect::to('/saleadminpanel');
    endif;
});

//check permissions-news

//check permissions-cities
Route::filter('permissions-city', function () {
    $permissions = [];
    foreach (Auth::user()->permission as $permission):
        $permissions[] = $permission->permission_id;
    endforeach;

    if (!in_array(4, $permissions)):
        return Redirect::to('/saleadminpanel');
    endif;
});
//check permissions-cities


//check permissions-settitgs
Route::filter('permissions-settitgs', function () {
    $permissions = [];
    foreach (Auth::user()->permission as $permission):
        $permissions[] = $permission->permission_id;
    endforeach;

    if (!in_array(5, $permissions)):
        return Redirect::to('/saleadminpanel');
    endif;
});
//check permissions-settitgs

//check permissions-reports
Route::filter('permissions-reports', function () {
    $permissions = [];
    foreach (Auth::user()->permission as $permission):
        $permissions[] = $permission->permission_id;
    endforeach;

    if (!in_array(6, $permissions)):
        return Redirect::to('/saleadminpanel');
    endif;
});
//check permissions-reports


//check permissions-reports
Route::filter('newsletters-administration', function () {
    $permissions = [];
    foreach (Auth::user()->permission as $permission):
        $permissions[] = $permission->permission_id;
    endforeach;

    if (!in_array(8, $permissions)):
        return Redirect::to('/saleadminpanel');
    endif;
});

//check permissions-reports
