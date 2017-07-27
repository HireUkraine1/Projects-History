<?php

use Guzzle\Http\ClientInterface;

class FanController extends BaseController
{

    /*
    |--------------------------------------------------------------------------
    | Default City Controller
    |--------------------------------------------------------------------------
    |
    | You may wish to use controllers instead of, or in addition to, Closure
    | based routes. That's great! Here is an example controller method to
    | get you started. To route to this controller, just add the route:
    |
    |
    |
    */


    /*	status
        Fan->status Codes:
            -1 - Self deleted
            0 - blocked (CANNOT LOGIN)
            1 - active
            2 - reserved
            3 - pending email activation

        FanInfo->sttus Codes:
            -1 - Created, hasn't paid yet
            0 - Expired Invoice or canceled
            1 - Paid and active



    */
    protected $layout = 'frontend.layouts.site';


    public function login()
    {


        if (Session::has('redirect')):
            Session::flash('toUrl', Session::get('fromUrl'));
        endif;


        if (Session::has('fan')) return Redirect::to('/stage');
        $breadcrumbs = [
            ['link' => '/become-a-fan', 'title' => 'Sign In'],
            // ['link' => '#', 'title' => "Track Settings for $trackPerformer->name"],
        ];
        View::share('breadcrumbs', $breadcrumbs);
        $metadata = [
            'meta' => [
                'title' => "Sign up for a site Account",
                'description' => "Sign up for a site account and get all the great features site has to offer"
                // 'robots'		=> "noindex, nofollow",
            ]
        ];
        View::share('metadata', $metadata);

        $this->layout->tagline = View::make('frontend.fans.login-tagline');
        $this->layout->content = View::make('frontend.fans.login-content');
        $this->layout->customjs = View::make('frontend.fans.login-customjs');
    }

    public function logout()
    {
        $this->_dropSession();
        Session::forget('cartData'); // clear old pending data
        Session::forget('cartRedirect');
        $metadata = [
            'meta' => [
                'robots' => "noindex, nofollow",
            ]
        ];
        View::share('metadata', $metadata);
        $message = ['success' => "You have Successfully logged out!"];
        return Redirect::to('/become-a-fan')->with('notifications', $message);
    }

    private function _dropSession()
    {
        Session::forget('fan');
        Session::forget('activeProfile');
    }

    public function stage()
    {

        if (Session::has('providersUrl')):
            $providersUrl = Session::get('providersUrl');
            Session::forget('providersUrl');
            return Redirect::to($providersUrl);
        endif;
        $fan = Session::get('fan');
        $fanId = $fan->id;


        $fanConcert = Fan::whereid($fanId)->first();
        if (!$fanConcert->concerts->isEmpty()):

            $concertid = $fanConcert->concerts[0]->id;
            $fanConcert->concerts()->detach();

            return Redirect::to('/tickets/' . $concertid);
        endif;


        $currentNotifications = NotificationSettings::where('fan_id', $fan->id)->with('performer')->with('performer.images')->get();
        $concertsTrack = ConcertTrack::where('fan_id', $fan->id)->get();
        $ConcertNote = [];
        $i = 0;
        foreach ($concertsTrack as $concertTrack):
            $ConcertNote[$i]['trackId'] = $concertTrack->id;
            $ConcertNote[$i]['concertId'] = $concertTrack->concert_id;
            $concert = Concert::where('id', $concertTrack->concert_id)->first();
            $ConcertNote[$i]['concertName'] = $concert->name;
            $ConcertNote[$i]['concertCity'] = $concert->location->city;
            $ConcertNote[$i]['concertDate'] = $concert->date;
            $ConcertNote[$i]['concertSection'] = $concertTrack->place_section;
            $ConcertNote[$i]['concertCountTickert'] = $concertTrack->count_tickets;
            $ConcertNote[$i]['concertPriceTickert'] = $concertTrack->price_tickets;
            $i++;

        endforeach;

        $trackedPerformers = [];
        foreach ($currentNotifications as $cn):
            $trackedPerformers[$cn->performer_id]['performer'] = $cn->performer;
            $trackedPerformers[$cn->performer_id]['notifications'][] = $cn->days;
        endforeach;


        $breadcrumbs = [
            ['link' => '/stage', 'title' => 'Dashboard'],
        ];
        $title = "site Tracker | Who is on stage near you?!";
        $description = "Track your favorite artists in locations near you ";
        $keywords = "";
        View::share('breadcrumbs', $breadcrumbs);
        $metadata = [
            'meta' => [
                'description' => $description,
                'keywords' => $keywords,
                'robots' => "noindex, nofollow",
                'title' => $title,
            ],
            'og' => [
                'description' => $description,
                'keywords' => $keywords,
                'robots' => "noindex, nofollow",
                'title' => $title
            ]
        ];
        View::share('metadata', $metadata);
        $this->layout->tagline = View::make("frontend.fans.stage-tagline");

        $this->layout->customjs = View::make('frontend.fans.tracker-customjs');
        $this->layout->content = View::make('frontend.fans.stage-content', ['trackedPerformers' => $trackedPerformers, 'ConcertNote' => $ConcertNote]);
    }

    public function settings()
    {


        $fan = Session::get('fan');

        if (isset($fan->info->stripe_id) && $fan->info):
            $fan = $this->_reSession($fan->id);
            $sCustomer = PaymentHelper::getCustomer($fan->info->stripe_id);
            $sSubs = $sCustomer->subscriptions->data;
        else:
            $sCustomer = false;
            $sSubs = false;
        endif;


        $notificationSettings = NotificationSettings::where('fan_id', $fan->id)->with('performer')->get();
        $fanSubscriptions = false;
        if ($fan->info):
            $fanSubscriptions = Subscription::where('fan_id', $fan->id)->orderBy('created_at', 'desc')->with('invoice')->get();
        endif;

        $breadcrumbs = [
            ['link' => '/settings', 'title' => 'site Account Settings'],
        ];
        View::share('breadcrumbs', $breadcrumbs);
        $title = "site Tracker Settings | Who is on stage near you?!";
        $description = "Track your favorite artists in locations near you ";
        $keywords = "";
        $metadata = [
            'meta' => [
                'description' => $description,
                'keywords' => $keywords,
                'robots' => "noindex, nofollow",
                'title' => $title,
            ],
            'og' => [
                'description' => $description,
                'keywords' => $keywords,
                'robots' => "noindex, nofollow",
                'title' => $title
            ]
        ];
        View::share('metadata', $metadata);
        $this->layout->tagline = View::make("frontend.fans.settings-tagline");

        $this->layout->customjs = View::make('frontend.fans.settings-customjs');
        $this->layout->content = View::make('frontend.fans.settings-content', ['notificationSettings' => $notificationSettings, 'sCustomer' => $sCustomer, 'sSubs' => $sSubs, 'fanSubscriptions' => $fanSubscriptions]);
    }

    private function _reSession($fanId)
    {
        $fan = Fan::where('id', $fanId)->with('profiles')->with('info')->first();
        Session::put('fan', $fan);
        return $fan;
    }

    public function loginWithFacebook()
    {

        $message = [];
        $this->gtfo(false); //log off everyone just in case
        // get data from input
        $code = Input::get('code');

        // get fb service
        $fb = OAuth::consumer('Facebook');

        // check if code is valid

        // if code is provided get user data and sign in
        if (!empty($code)):

            $prevUrl = parse_url(Request::server('HTTP_REFERER'));

            if (preg_match("/\/tickets\//", $prevUrl['path'])):
                $idConcert = preg_replace("/[^0-9]/", '', $prevUrl['path']);
            else:
                $idConcert = false;
            endif;

            // This was a callback request from facebook, get the token
            $token = $fb->requestAccessToken($code);

            // Send a request with it
            $result = json_decode($fb->request('/me'), true);
            if ($result):
                $email = $result['email'];
                $fbId = $result['id'];
                //check if user already exists,
                $profile = Profile::where('email', $email)->where('provider', 'facebook')->where('identifier', $fbId)->first();
                //

                // $currentFan = Fan::where('email',$email)->where('status','1')->first();
                if (!$profile):
                    //check for main user
                    $fan = Fan::where('email', $email)->with('profiles')->with('info')->first();

                    if (!$fan):
                        //create main user
                        $fan = new Fan;
                        $fan->email = $email;
                        $fan->name = $result['name'];
                        $fan->status = 1;
                        $fan->type = 1;
                        $fan->dont_bother = 0;
                        $fan->save();
                        if ($idConcert):
                            $fan->concerts()->attach($idConcert);
                        endif;
                    else:
                        if ($idConcert):
                            $fan->concerts()->attach($idConcert);
                        endif;
                    endif;

                    if ($fan->status === 0):
                        $message = ['danger' => "This account was BLOCKED! Please <a href='/contact'>contact us</a>"];
                        return Redirect::to('/become-a-fan')->with('notifications', $message);
                    endif;

                    if ($fan->status == -1):
                        $fan->status = 1;
                        $fan->save();
                        $message = ['success' => "Profile reactivated!!"];
                    endif;

                    $profile = new Profile;
                    $profile->fan_id = $fan->id;
                    $profile->email = $email;
                    $profile->identifier = $result['id'];
                    $profile->provider = 'facebook';
                    $profile->profileURL = "https://www.facebook.com/app_scoped_user_id/{$result['id']}/";
                    $profile->photoURL = "https://graph.facebook.com/{$result['id']}/picture?width=150&height=150";
                    $profile->displayName = $result['name'];
                    $profile->firstName = $result['first_name'];
                    $profile->lastName = $result['last_name'];
                    $profile->gender = isset($result['gender']) ? $result['gender'] : '';
                    $profile->language = $result['locale'];
                    $profile->emailVerified = ($result['verified']) ? $email : '';
                    $profile->save();
                    //create profile
                endif;
                $fan = Fan::where('id', $profile->fan_id)->with('profiles')->with('info')->first();
                if ($idConcert):
                    $fan->concerts()->attach($idConcert);
                endif;
                if ($fan->status === 0):
                    $message = ['danger' => "This account was BLOCKED! Please <a href='/contact'>contact us</a>"];
                    return Redirect::to('/become-a-fan')->with('notifications', $message);
                endif;

                $fan->last_login = date('Y-m-d H:i:s');
                $fan->save();
                $this->_reSession($fan->id);
                Session::put('activeProfile', $profile);
                return Redirect::to('/stage')->with('notifications', $message);
            else:
                echo "ERROR HAPPENED";
            endif;
        // $message = 'Your unique facebook user id is: ' . $result['id'] . ' and your name is ' . $result['name'];
        // echo $message. "<br/>";

        //Var_dump
        //display whole array().
        // dd($result);
        // if not ask for permission first
        else:
            // get fb authorization
            $url = $fb->getAuthorizationUri();

            if (Session::has('toUrl')):
                Session::put('providersUrl', Session::get('toUrl'));
            endif;
            return Redirect::to((string)$url);

        endif;
    }

    public function gtfo($redirect = true, $messages = null)
    {
        $this->_dropSession();


        if ($redirect):
            return Redirect::to('/become-a-fan')->with('notifications', $messages);
        endif;
        // dd($hybridauth);
    }

    public function loginWithGoogle()
    {
        $message = [];

        // get data from input
        $code = Input::get('code');

        // get google service
        $googleService = OAuth::consumer('Google');

        // check if code is valid

        // if code is provided get user data and sign in
        if (!empty($code)):
            $prevUrl = parse_url(Request::server('HTTP_REFERER'));

            if (preg_match("/\/tickets\//", $prevUrl['path'])):
                $idConcert = preg_replace("/[^0-9]/", '', $prevUrl['path']);
            else:
                $idConcert = false;
            endif;
            // This was a callback request from google, get the token
            $token = $googleService->requestAccessToken($code);

            // Send a request with it
            $result = json_decode($googleService->request('https://www.googleapis.com/oauth2/v1/userinfo'), true);
            if ($result):
                $email = $result['email'];
                $gId = $result['id'];
                //check if user already exists,
                $profile = Profile::where('email', $email)->where('provider', 'google')->where('identifier', $gId)->first();

                // $currentFan = Fan::where('email',$email)->where('status','1')->first();
                if (!$profile):
                    //check for main user
                    $fan = Fan::where('email', $email)->with('profiles')->with('info')->first();

                    if (!$fan):
                        //create main user
                        $fan = new Fan;
                        $fan->email = $email;
                        $fan->name = $result['name'];
                        $fan->status = 1;
                        $fan->type = 1;
                        $fan->dont_bother = 0;
                        $fan->save();
                        if ($idConcert):
                            $fan->concerts()->attach($idConcert);
                        endif;
                    else:
                        if ($idConcert):
                            $fan->concerts()->attach($idConcert);
                        endif;
                    endif;
                    if ($fan->status == -1):
                        $fan->status = 1;
                        $fan->save();
                        $message = ['success' => "Profile reactivated!!"];
                    endif;
                    if ($fan->status === 0):
                        $message = ['danger' => "This account was BLOCKED! Please <a href='/contact'>contact us</a>"];
                        return Redirect::to('/become-a-fan')->with('notifications', $message);
                    endif;
                    $profile = new Profile;
                    $profile->fan_id = $fan->id;
                    $profile->email = $email;
                    $profile->identifier = $result['id'];
                    $profile->provider = 'google';
                    $profile->profileURL = $result['link'];
                    $profile->photoURL = $result['picture'];
                    $profile->displayName = $result['name'];
                    $profile->firstName = $result['given_name'];
                    $profile->lastName = $result['family_name'];
                    $profile->language = $result['locale'];
                    $profile->emailVerified = ($result['verified_email']) ? $email : '';
                    $profile->save();
                    //create profile
                endif;
                $fan = Fan::where('id', $profile->fan_id)->with('profiles')->with('info')->first();
                if ($idConcert):
                    $fan->concerts()->attach($idConcert);
                endif;
                if ($fan->status === 0):
                    $message = ['danger' => "This account was BLOCKED! Please <a href='/contact'>contact us</a>"];
                    return Redirect::to('/become-a-fan')->with('notifications', $message);
                endif;

                $fan->status = 1;

                $fan->last_login = date('Y-m-d H:i:s');
                $fan->save();
                $this->_reSession($fan->id);
                Session::put('activeProfile', $profile);
                return Redirect::to($this->retirect_to_url(Session::get('toUrl')));//return Redirect::to('/stage')->with('notifications',$message);;
            else:
                echo "ERROR HAPPENED";
            endif;


        //Var_dump
        //display whole array().
        // if not ask for permission first
        else:
            // get googleService authorization
            $url = $googleService->getAuthorizationUri();
            if (Session::has('toUrl')):
                Session::put('providersUrl', Session::get('toUrl'));
            endif;
            // return to google login url
            return Redirect::to((string)$url);
        endif;
    }

    private function retirect_to_url($url = '/stage')
    {

        return (!$url) ? '/stage' : $url;


    }

    public function loginWithTwitter()
    {
        $message = [];
        $this->gtfo(false); //log off everyone just in case
        //POST to self to verify
        if (Session::has('tid') && Input::get('email') != ''):

            $updateId = Session::get('tid');
            $email = strtolower(Input::get('email'));
            $name = strtolower(Input::get('name'));
            $img = Session::get('img');
            $newHash = md5(date('U') . $email);
            $pendingUpdate = DB::table('pending_fans')->where('twitter_id', $updateId)->first();
            if ($pendingUpdate): //update and resend
                DB::table('pending_fans')->where('twitter_id', $updateId)->update(['email' => $email, 'hash' => $newHash]); //updated
            else: //no user to update, INSERT
                DB::table('pending_fans')->insert(['email' => $email, 'hash' => $newHash, 'twitter_id' => $updateId, 'created_at' => date('Y-m-d H:i:s'), 'name' => Input::get('name'), 'img' => $img]);
            endif;
            //TODO: SEND EMAIL WITH HASH AND NAME!

            Mail::send('emails.frontend.fans.twitter-confirm', array('name' => $name, 'hash' => $newHash), function ($message) use ($email, $name) {
                $message->to($email, $name)->subject('site: Please  Confirm Your Email!');
            });
            Session::forget('tid');
            Session::forget('img');
            return Redirect::to('/twitter-confirm')->with('twitter', 'confirm'); //send to thank you page
        endif;

        // get data from input
        $token = Input::get('oauth_token');
        $verify = Input::get('oauth_verifier');

        $hasEmail = Session::get('twitterEmail');
        // get twitter service
        $tw = OAuth::consumer('Twitter');

        // check if code is valid

        // if code is provided get user data and sign in
        if (!empty($token) && !empty($verify)):

            // This was a callback request from twitter, get the token
            $token = $tw->requestAccessToken($token, $verify);

            // Send a request with it
            $result = json_decode($tw->request('account/verify_credentials.json'), true);
            if ($result):
                // $result = json_decode( $tw->request( 'account/verify_credentials.json' ), true );
                $tId = $result['id'];
                Session::put('tid', $tId);
                Session::put('img', $result['profile_image_url_https']);
                $profile = Profile::where('provider', 'twitter')->where('identifier', $tId)->first();
                if ($profile && $profile->email): //legitimate and verified twitter account
                    $fan = Fan::where('id', $profile->fan_id)->with('profiles')->with('info')->first();
                    if ($fan->status === 0):
                        $message = ['danger' => "This account was BLOCKED! Please <a href='/contact'>contact us</a>"];
                        return Redirect::to('/become-a-fan')->with('notifications', $message);
                    endif;
                    $fan->status = 1;
                    $fan->type = 1;
                    $fan->last_login = date('Y-m-d H:i:s');
                    $fan->save();
                    $this->_reSession($fan->id);
                    Session::put('activeProfile', $profile);
                    return Redirect::to('/stage');
                else: //no profile verified,  creating pending
                    $pending = DB::table('pending_fans')->where('twitter_id', $tId)->first();
                    if ($pending): //user haspending request but needs to update record or resend link
                        $this->layout->content = View::make('frontend.fans.twitter-email-resend-content', ['email' => $pending->email, 'name' => $result['name']]);
                    else:
                        $this->layout->content = View::make('frontend.fans.twitter-email-content', ['name' => $result['name']]);
                    endif;
                endif;

            else:
                echo "ERROR HAPPENED";
            endif;
        else:
            // get request token
            $reqToken = $tw->requestRequestToken();

            // get Authorization Uri sending the request token
            $url = $tw->getAuthorizationUri(array('oauth_token' => $reqToken->getRequestToken()));
            if (Session::has('toUrl')):
                Session::put('providersUrl', Session::get('toUrl'));
            endif;
            // return to twitter login url
            return Redirect::to((string)$url);
        endif;
    }

    public function loginWithEmail()
    {
        $message = [];
        $creds = Input::all();
        $fan = Fan::where('email', strtolower(trim($creds['email'])))
            ->where('password', md5($creds['password']))
            ->with('profiles')
            ->with('info')
            ->first();
        if ($fan):
            if (isset($creds['concertid'])):
                $fan->concerts()->attach($creds['concertid']);
            endif;
            if ($fan->status == 1):
                $fan->last_login = date('Y-m-d H:i:s');
                $fan->save();
                $this->_reSession($fan->id);
                Session::put('activeProfile', false);
                return Redirect::to($this->retirect_to_url(Session::get('toUrl')));

            elseif ($fan->status == 3):
                $activateLink = "<a style='color:black' href='/send-activation-link/{$fan->email}'>Resend Activation Link</a>";
                $message = ['warning' => "Your account has not been activated yet! <p>" . $activateLink . "</p>"];
                return Redirect::to('/become-a-fan')->with('notifications', $message);
            elseif ($fan->status == -1):
                $message = ['success' => "You have REACTIVATED your account! Welcome back!"];
                $fan->last_login = date('Y-m-d H:i:s');
                $fan->status = 1;
                $fan->save();
                $this->_reSession($fan->id);
                Session::put('activeProfile', false);

                return Redirect::to('/stage')->with('notifications', $message);

            elseif ($fan->status === 0):
                $message = ['danger' => "This account was BLOCKED! Please <a href='/contact'>contact us</a>"];
                return Redirect::to('/become-a-fan')->with('notifications', $message);
            else:
                $message = ['warning' => "Error logging in. Try again or contact us."];
                return Redirect::to('/become-a-fan')->with('notifications', $message);
            endif;
        else:
            $message = ['warning' => "Fan not found!"];
            return Redirect::to('/become-a-fan')->with('notifications', $message);
        endif;
    }

    public function fanConfirm($hash)
    {
        $message = ['alert' => "Wrong confirmation link!"];
        if ($hash): //confirmation link
            $fan = Fan::where('hash_link', trim($hash))->first();
            if ($fan->status === 0):
                $message = ['danger' => "This account was BLOCKED! Please <a href='/contact'>contact us</a>"];

            elseif ($fan->status == -1):
                $fan->status = 1;
                $fan->dont_bother = 0;
                $fan->save();
                $message = ['success' => "Success!!! You have REACTIVATED your account! Welcome back!"];
            else:
                $fan->status = 1;
                $fan->dont_bother = 0;
                $fan->save();
                $message = ['success' => "Success!!! You may login!"];
            endif;
        endif;
        return Redirect::to('/become-a-fan')->with('notifications', $message);
    }

    public function resendActivation($email)
    {
        $message = [];

        if ($email):
            $fan = Fan::where('email', strtolower($email))->first();
            if ($fan->status === 0):
                $message = ['danger' => "This account was BLOCKED! Please <a href='/contact'>contact us</a>"];
                return Redirect::to('/become-a-fan')->with('notifications', $message);
            endif;
            if ($fan):
                $name = $fan->name;
                $hash = $fan->hash_link;
                $email = $fan->email;
                Mail::send('emails.frontend.fans.email-confirm', array('name' => $name, 'hash' => $hash), function ($message) use ($email, $name) {
                    $message->to($email, $name)->subject('site: Please Confirm Your Email!');
                });
                $message = ['success' => "Please check your email!"];
                return Redirect::to('/become-a-fan')->with('notifications', $message);
            endif;
        endif;
        $message = ['warning' => "Fan not found or already activated!!"];
        return Redirect::to('/become-a-fan')->with('notifications', $message);
    }

    public function twitterConfirm($hash = null)
    {
        $message = [];
        if ($hash): //confirmation link

            $pendingUser = DB::table('pending_fans')->where('hash', trim($hash))->first();
            if ($pendingUser):
                $email = $pendingUser->email;
                $name = $pendingUser->name;
                $tId = $pendingUser->twitter_id;
                $fan = Fan::where('email', $email)->with('profiles')->first();

                if (!$fan):
                    $fan = new Fan;
                    //create main user
                    $fan->email = $email;
                    $fan->name = $name;
                    $fan->status = 1;
                    $fan->type = 1;
                    $fan->dont_bother = 0;
                    $fan->save();
                endif;
                if ($fan->status === 0):
                    $message = ['danger' => "This account was BLOCKED! Please <a href='/contact'>contact us</a>"];
                    return Redirect::to('/become-a-fan')->with('notifications', $message);
                endif;

                if ($fan->status == -1):
                    $fan->status = 1;
                    $fan->save();
                    $message = ['success' => "Profile reactivated!!"];
                endif;
                $profile = new Profile;
                $profile->fan_id = $fan->id;
                $profile->email = $email;
                $profile->identifier = $tId;
                $profile->provider = 'twitter';
                $profile->emailVerified = $email;
                $profile->displayName = $name;
                $profile->profileURL = "https://twitter.com/intent/user?user_id=" . $tId;
                $profile->photoURL = $pendingUser->img;
                $profile->save();
                DB::table('pending_fans')->where('id', $pendingUser->id)->delete();

                return Redirect::to('/login-with-twitter')->with('notifications', $message);;
            else:
                $message = ['alert' => "Wrong confirmation link!"];
                return Redirect::to('/become-a-fan')->with('notifications', $message);
            endif;
        elseif (Session::has('twitter') && Session::get('twitter') == 'confirm'): //update confirmation email
            Session::forget('twitter');
            $this->layout->content = View::make('frontend.fans.twitter-email-thankyou-content');
        else: //you came baring no gifts, go home
            Session::forget('twitter');
            $message = ['alert' => "Wrong confirmation information!"];
            return Redirect::to('/become-a-fan')->with('notifications', $message);
        endif;
    }

    public function resetPassword($hash = null)
    {
        $message = [];

        $this->layout->customjs = View::make('frontend.fans.login-customjs');
        $metadata = [
            'meta' => [
                'robots' => "noindex, nofollow",
            ]
        ];
        View::share('metadata', $metadata);
        if ($hash):
            $resetEntry = DB::table('lost_passwords')->where('hash', trim($hash))->first();
            if ($resetEntry):
                $fan = Fan::where('id', $resetEntry->fan_id)->first();
                if ($fan->status === 0):
                    $message = ['danger' => "This account was BLOCKED! Please <a href='/contact'>contact us</a>"];
                    return Redirect::to('/become-a-fan')->with('notifications', $message);
                endif;
                Session::put('reset_fan_id', $fan->id);
                // Session::put('reset_hash',$hash);
                $checkSum = md5($hash . $fan->email) . "-" . $hash; //on receiving end must match to prevent fake posts
                $this->layout->content = View::make('frontend.fans.set-new-password-content', ['email' => $fan->email, 'hash' => $checkSum]);
            else:
                $message = ['alert' => "Wrong reset link or it has expired!!"];
                return Redirect::to('/become-a-fan')->with('notifications', $message);
            endif;
        else: //not a reset link
            $this->layout->content = View::make('frontend.fans.reset-password-content');
        endif;
    }

    public function trackPerformer($slug = null)
    {
        $performer = Performer::where('slug', trim($slug))->with('upcoming_concerts')->first();
        $fan = Session::get('fan');
        if (!$performer) return Redirect::to('/stage');
        $notificationSettings = NotificationSettings::where('performer_id', $performer->id)
            ->where('fan_id', $fan->id)
            ->where('type', 'email')
            ->orderBy('days', 'desc')->get();
        $stageNotifications = NotificationSettings::where('performer_id', $performer->id)
            ->where('fan_id', $fan->id)
            ->where('type', 'stage')
            ->first();
        $breadcrumbs = [
            ['link' => '/stage', 'title' => 'Stage'],
            ['link' => '#', 'title' => "Track $performer->name"],
        ];
        View::share('breadcrumbs', $breadcrumbs);
        $metadata = [
            'meta' => [
                'robots' => "noindex, nofollow",
            ]
        ];
        View::share('metadata', $metadata);
        $this->layout->tagline = View::make('frontend.fans.track-performer-tagline', ['performer' => $performer]);
        $this->layout->customjs = View::make('frontend.fans.tracker-customjs');

        $this->layout->content = View::make('frontend.fans.track-performer-content', ['performer' => $performer, 'notificationSettings' => $notificationSettings, 'stageNotifications' => $stageNotifications]);
    }

    public function trackLocation($slug = null)
    {
        $location = Location::where('slug', trim($slug))->first();
        if (!$location) return Redirect::to('/stage');
        $fan = Session::get('fan');
        $text = DB::table('city_text')->where('location_id', $location->id)->first();
        $alreadyTracked = DB::table('location_fans')->where('fan_id', $fan->id)->where('location_id', $location->id)->first();
        $nearBy = DB::table('location_nearby')->where('center_slug', $location->slug)->where('distance', '<', 25)->take(20)->orderBy('distance', 'ASC')->get();
        $nearByWithTrackingData = [];
        foreach ($nearBy as $nb):
            $nearByWithTrackingData[$nb->slug]['city'] = $nb->city;
            $nearByWithTrackingData[$nb->slug]['state'] = $nb->state;
            $nearByWithTrackingData[$nb->slug]['distance'] = $nb->distance;
            $nearByWithTrackingData[$nb->slug]['event_count'] = $nb->event_count;
            $nbLocation = Location::where('slug', $nb->slug)->first();
            $nbTracked = DB::table('location_fans')->where('fan_id', $fan->id)->where('location_id', $nbLocation->id)->first();
            $nearByWithTrackingData[$nb->slug]['track_id'] = ($nbTracked) ? $nbTracked->id : false;
            $nearByWithTrackingData[$nb->slug]['location_id'] = $nbLocation->id;
        endforeach;

        $breadcrumbs = [
            ['link' => '/stage', 'title' => 'Dashboard'],
            ['link' => '#', 'title' => "Track $location->city"],
        ];
        View::share('breadcrumbs', $breadcrumbs);
        $metadata = [
            'meta' => [
                'robots' => "noindex, nofollow",
            ]
        ];
        View::share('metadata', $metadata);
        $this->layout->tagline = View::make('frontend.fans.track-location-tagline', ['location' => $location]);
        $this->layout->customjs = View::make('frontend.fans.tracker-customjs');
        $this->layout->content = View::make('frontend.fans.track-location-content', ['location' => $location, 'alreadyTracked' => $alreadyTracked, 'nearBy' => $nearByWithTrackingData, 'text' => $text]);

    }

    public function memberSignup()
    {

        $activePlan = FanHelper::getActivePlan();
        if (!$activePlan):
            $message = ['warning' => "Sorry, but site does not accept memberships at this time! "];
            return Redirect::to("/stage")->with('notifications', $message);
        endif;
        $fan = Session::get('fan');
        $fan = $this->_reSession($fan->id);

        if ($_POST): //submitted form
            $post = Input::all();
            if (Input::has('password') && Input::has('password2')):
                if ($post['password'] != $post['password2'] || !StringHelper::strongPassword($post['password'])):
                    return Redirect::to('/become-a-member')->withInput(Input::except('password'));
                endif;
            endif;

            $fanInfo = FanInfo::where('fan_id', $fan->id)->first();
            if (!$fanInfo):
                $fanInfo = new FanInfo;
                $stripe_id = '';
            else:
                $stripe_id = $FanInfo->stripe_id;
            endif;
            $full_name = "{$post['first_name']} {$post['middle_name']} {$post['last_name']}";
            $email = strtolower($fan->email);
            // if(isset($post['password'])) $fanInfo->password = md5($post['password']);
            $fanInfo->fan_id = $fan->id;
            $fanInfo->legal_name = $full_name;
            $fanInfo->address = $post['address'];
            $fanInfo->address_2 = $post['address2'];
            $fanInfo->email = $email;
            $fanInfo->country = $post['country'];
            $fanInfo->state = $post['state'];
            $fanInfo->city = $post['city'];
            $fanInfo->zip = $post['zip'];
            $fanInfo->phone = $post['phone'];
            $fanInfo->cell_phone = $post['mobile'];
            $fanInfo->birthday = date('Y-m-d', strtotime($post['birthday']));
            $fanInfo->status = -1;
            //check if cx exits in stripe
            if ($stripe_id != ''):
                $pgCustomer = PaymentHelper::getCustomer($stripe_id);
            else:
                $createData = ['full_name' => $full_name, 'email' => $email, 'id' => $fan->id];
                $pgCustomer = PaymentHelper::createCustomer($createData);
            endif;

            $fanInfo->stripe_id = $pgCustomer->id;
            $fanInfo->save();
            return Redirect::to("/member-payment");
        endif;

        if (!$fan->info):
            $breadcrumbs = [
                ['link' => '/stage', 'title' => 'Dashboard'],
                ['link' => '#', 'title' => "TourPass Sign Up"],
            ];
            View::share('breadcrumbs', $breadcrumbs);
            $metadata = [
                'meta' => [
                    'robots' => "noindex, nofollow",
                ]
            ];
            View::share('metadata', $metadata);
            $this->layout->customjs = View::make('frontend.fans.member-customjs');
            $this->layout->tagline = View::make('frontend.fans.member-signup-tagline');
            $this->layout->content = View::make('frontend.fans.member-signup-content');


        else:
            return Redirect::to('/stage');
        endif;
    }

    //TRACKER ACTIONS
    public function payInvoice($id = null)
    {
        $fan = $this->_reSession(Session::get('fan')->id);
        if (Input::has('stripeToken')):
            $token = Input::get('stripeToken');
            $coupon = Input::get('applied_coupon');
            $result = PaymentHelper::subscribe($fan->id, $token, $coupon);
            if ($result['error'] === 0):
                $this->_reSession($fan->id);
                $message = ['success' => "Success! You are now a premium member!"];
                return Redirect::to("/stage")->with('notifications', $message);
            else:
                $message = ['warning' => $result['message']];
                return Redirect::to("/member-payment")->with('notifications', $message);

            endif;
        endif;
        if (isset($fan->info->status) && $fan->info->status === -1): // unpaid customer
            $metadata = [
                'meta' => [
                    'robots' => "noindex, nofollow",
                ]
            ];
            View::share('metadata', $metadata);
            $this->layout->content = View::make('frontend.fans.member-payment-content', ['fan' => $fan]);
            $this->layout->customjs = View::make('frontend.fans.payment-customjs');

        elseif (isset($fan->info->status) && $fan->info->status === 1): //active, no need to pay
            $message = ['info' => "You do not have a balance to pay!"];
            return Redirect::to("/stage")->with('notifications', $message);
        else: //something is not correct
            $message = ['warning' => "There is a problem with your account. Please contact us at <a href='mailto:payments@site.com'>payments@site.com</a>'!"];
            return Redirect::to("/stage")->with('notifications', $message);
        endif;

    }

    public function onePageMember()
    {
        $cartData = Session::get('cartData');
        $activePlan = FanHelper::getActivePlan();
        if (!$activePlan):
            $message = ['warning' => "Sorry, but site does not accept memberships at this time! "];
            return Redirect::to("/stage")->with('notifications', $message);
        endif;

        $fan = Session::get('fan');

        if (isset($fan->info->status) && $fan->info->status == 1):
            if ($cartData):
                return Redirect::to('/checkout/step2'); //send him to checkout
            else://already a member and no data
                return Redirect::to('/stage');
            endif;
        else: //this is either not a member, not a fan, or both
            if ($_POST): //submitted form
                $post = Input::all();
                if ($fan): //fan
                    $email = strtolower($fan->email);
                    $post = Input::all();
                    if (Input::has('password') && Input::has('password2')):
                        if ($post['password'] != $post['password2'] || !StringHelper::strongPassword($post['password'])):
                            $message = ['warning' => "Bad Password!"];
                            return Redirect::to('/quick-signup')->with('notifications', $message);
                        endif;
                    endif;

                    $fanInfo = FanInfo::where('fan_id', $fan->id)->first();
                    if (!$fanInfo):
                        $fanInfo = new FanInfo;
                        $stripe_id = '';
                        $fanInfo->fan_id = $fan->id;
                        $fanInfo->email = $email;
                        $fanInfo->country = $post['country'];
                        $fanInfo->state = $post['state'];
                        $fanInfo->city = $post['city'];
                        $fanInfo->zip = $post['zip'];
                        $fanInfo->phone = $post['phone'];
                    else:
                        $stripe_id = $FanInfo->stripe_id;
                    endif;
                    $full_name = $post['full_name'];

                    if (isset($post['password'])):
                        $fan->password = md5($post['password']);
                    endif;

                    $fanInfo->legal_name = $full_name;
                    $fanInfo->status = -1;

                    //check if cx exits in stripe

                    if ($stripe_id != ''):
                        $pgCustomer = PaymentHelper::getCustomer($stripe_id);
                    else:
                        $createData = ['full_name' => $full_name, 'email' => $email, 'id' => $fan->id];
                        $pgCustomer = PaymentHelper::createCustomer($createData);
                    endif;

                    $fanInfo->stripe_id = $pgCustomer->id;

                    $fanInfo->save();
                    if (Input::has('stripeToken')):
                        $token = Input::get('stripeToken');
                        $coupon = Input::get('applied_coupon');
                        $result = PaymentHelper::subscribe($fan->id, $token, $coupon);

                        if ($result['error'] === 0):
                            $fan->save();
                            $fanInfo->status = 1;
                            $fanInfo->save();
                            $this->_reSession($fan->id);
                            return Redirect::to('/checkout/step2'); //send him to checkout
                        else:
                            $this->_reSession($fan->id);
                            $message = ['warning' => $result['message']];
                            return Redirect::to("/quick-signup")->with('notifications', $message);
                        endif;
                    else:
                        $message = ['warning' => "Something went wrong. Please <a href='/contact'>Contct Us</a>"];
                        return Redirect::to('/quick-signup')->with('notifications', $message);
                    endif;

                else: //not a fan

                    $email = $post['email'];
                    if (Fan::where('email', $email)->count()):
                        $message = ['warning' => "This user already exists...try  different email!"];
                        return Redirect::to("/quick-signup")->with('notifications', $message);
                    else:
                        $post = Input::all();
                        $email = strtolower($post['email']);
                        $name = trim($post['full_name']);
                        $fan = new Fan;
                        $hash = md5($email . date('U') . rand(1, 555434));
                        $fan->email = $email;
                        $fan->name = $name;
                        $fan->password = md5($post['password']);
                        $fan->status = 3;
                        $fan->hash_link = $hash;
                        $fan->save();
                        $fanInfo = FanInfo::where('fan_id', $fan->id)->first();
                        if (!$fanInfo):
                            $fanInfo = new FanInfo;
                            $stripe_id = '';
                            $fanInfo->fan_id = $fan->id;
                            $fanInfo->email = $email;
                            $fanInfo->country = $post['country'];
                            $fanInfo->state = $post['state'];
                            $fanInfo->city = $post['city'];
                            $fanInfo->zip = $post['zip'];
                            $fanInfo->phone = $post['phone'];
                        else:
                            $stripe_id = $FanInfo->stripe_id;
                        endif;

                        $full_name = $post['full_name'];
                        if ($post['password']):
                            $fan->password = md5($post['password']);
                        endif;

                        $fanInfo->legal_name = $full_name;
                        $fanInfo->status = -1;

                        //check if cx exits in stripe
                        if ($stripe_id != ''):
                            $pgCustomer = PaymentHelper::getCustomer($stripe_id);
                        else:
                            $createData = ['full_name' => $full_name, 'email' => $email, 'id' => $fan->id];
                            $pgCustomer = PaymentHelper::createCustomer($createData);
                        endif;

                        $fanInfo->stripe_id = $pgCustomer->id;

                        $fanInfo->save();

                        if (Input::has('stripeToken')):
                            $token = Input::get('stripeToken');
                            $coupon = Input::get('applied_coupon');
                            $result = PaymentHelper::subscribe($fan->id, $token, $coupon);

                            if ($result['error'] === 0):
                                $fanInfo->status = 1;
                                $fanInfo->save();
                                $this->_reSession($fan->id);
                                $fan->save();

                                return Redirect::to('/checkout/step2'); //send him to checkout
                            else:
                                $this->_reSession($fan->id);
                                $message = ['warning' => $result['message']];
                                return Redirect::to("/quick-signup")->with('notifications', $message);

                            endif;
                        else:
                            $message = ['warning' => "Something went wrong. Please <a href='/contact'>Contct Us</a>"];

                            return Redirect::to('/quick-signup')->with('notifications', $message);
                        endif;
                    endif;
                endif;

            endif;
            $breadcrumbs = [
                ['link' => '#', 'title' => 'TourPass Membership'],
            ];
            View::share('breadcrumbs', $breadcrumbs);
            $metadata = [
                'meta' => [
                    'description' => "Sign up for site TourPass membership and never pay service fee again",
                    'keywords' => "tourpass,site membership, signup, member signup",
                    'title' => "Sign Up for TourPass Membership",
                ],
                'og' => [
                    'description' => "Sign up for site TourPass membership and never pay service fee again",
                    'keywords' => "tourpass,site membership, signup, member signup",
                    'title' => "Sign Up TourPass Membership"
                ]
            ];
            View::share('metadata', $metadata);

            $this->layout->customjs = View::make('frontend.fans.spm-customjs');
            $this->layout->tagline = View::make('frontend.fans.spm-tagline');
            $this->layout->content = View::make('frontend.fans.spm-content', ['fan' => $fan, 'cartData' => $cartData]);
        endif;

    }


    public function deleteYourself()
    {
        $message = ['alert' => "We are sad to see you go <i class='fa fa-frown-o'></i> "];
        Session::forget('cartData'); // clear old pending data
        if (Input::has('confirm')): //confirmation link
            $fan = Session::get('fan');

            if ($fan):
                $fanId = $fan->id;
                $fanEmail = $fan->email;
                //Keep fans, just disable it
                $fan->status = -1;
                // DB::table('fans')->where('id',$fanId)->delete();
                DB::table('profiles')->where('fan_id', $fanId)->delete();
                DB::table('lost_passwords')->where('fan_id', $fanId)->delete();
                DB::table('notification_settings')->where('fan_id', $fanId)->delete();
                DB::table('location_fans')->where('fan_id', $fanId)->delete();
                DB::table('pending_fans')->where('email', $fanEmail)->delete();
                $this->_dropSession();
                $fan->save();
                return Redirect::to('/become-a-fan')->with('notifications', $message);
            endif;
        endif;
        $metadata = [
            'meta' => [
                'robots' => "noindex, nofollow",
            ]
        ];
        View::share('metadata', $metadata);
        $this->layout->content = View::make('frontend.fans.delete-confirm-content');

    }

    public function unlinkProfile($provider = null)
    {
        $message = [];
        $activeProfile = Session::get('activeProfile');
        $fan = Session::get('fan');
        $message = ['alert' => "There was an error...Try Again?"];

        if ($activeProfile && $activeProfile->provider == $provider):
            $message = ['alert' => "Successfully unlinked <strong>" . ucwords($provider) . "</strong> from your account. It was your current active profile so you were also SIGNED OFF!!"];
            DB::table('profiles')->where('fan_id', $fan->id)->where('provider', $provider)->delete();
            $this->_dropSession();
            $this->gtfo(false);
            return Redirect::to('/become-a-fan')->with('notifications', $message);
        else:
            DB::table('profiles')->where('fan_id', $fan->id)->where('provider', $provider)->delete();
            $message = ['alert' => "Successfully unlinked <strong>" . ucwords($provider) . "</strong> from your account!"];
            $fan = Fan::where('id', $fan->id)->with('profiles')->first();
            $fan->updated_at = date('Y-m-d H:i:s');
            $fan->save();
            Session::put('fan', $fan);
            return Redirect::to('/settings')->with('notifications', $message);
        endif;
    }

    public function addItem($op = null, $id = null)
    {
        $result['error'] = 1;
        $result['message'] = "Error Occurred While Adding {$post->title}, Try Again?";
        switch ($op):
            case 'performer':
                $performer = Performer::find($id);
                if ($performer):
                    $fan->performers()->attach($performer->id);
                    $result['error'] = 0;
                    $result['message'] = "{$performer->name} Added Successfully!";
                else:
                    $result['error'] = 1;
                    $result['message'] = "Cannot Find This Performer!!";
                endif;
                break;
            case 'location':
                $location = Location::find($id);
                if ($location):
                    $fan->locations()->attach($location->id);
                    $result['error'] = 0;
                    $result['message'] = "{$location->city} Added Successfully!";
                else:
                    $result['error'] = 1;
                    $result['message'] = "Cannot Find This Location!!";
                endif;
                break;
            default:
                # code...
                break;
        endswitch;
        echo json_encode($result);
    }

    public function updateInfo()
    {
        if (Input::has('op') && Input::get('op') == 'update'):
            $fan = Session::get('fan');
            $post = Input::all();
            $fanInfo = FanInfo::where('fan_id', $fan->id)->first();
            $fanInfo->legal_name = $post['legal_name'];
            $fanInfo->address = $post['address'];
            $fanInfo->address_2 = $post['address2'];
            $fanInfo->country = $post['country'];
            $fanInfo->state = $post['state'];
            $fanInfo->city = $post['city'];
            $fanInfo->zip = $post['zip'];
            $fanInfo->phone = $post['phone'];
            $fanInfo->cell_phone = $post['mobile'];
            $fanInfo->birthday = date('Y-m-d', strtotime($post['birthday']));
            $fanInfo->save();
            $this->_reSession($fan->id);
            $message = ['success' => "Successfully updated account!"];
            return Redirect::to('/settings')->with('notifications', $message);
        else:
            $metadata = [
                'meta' => [
                    'robots' => "noindex, nofollow",
                ]
            ];
            View::share('metadata', $metadata);
            $this->layout->customjs = View::make('frontend.fans.update-customjs');
            $this->layout->content = View::make('frontend.fans.update-info-content');
        endif;
    }

    public function updateCC()
    {
        $fan = Session::get('fan');
        $fan = $this->_reSession($fan->id);
        $stripeId = $fan->info->stripe_id;
        if ($fan->info->status == -1)
            return Redirect::to('/member-payment');
        if (Input::has('stripeToken')):
            try {
                $token = Input::get('stripeToken');
                $sCustomer = \Stripe\Customer::retrieve($stripeId);
                $sCustomer->card = $token;
                $sCustomer->save();
                $message = ['success' => "Successfully updated account!"];
                return Redirect::to('/settings')->with('notifications', $message);

            } catch (Exception $e) {
                $message = ['warning' => "Error when updating account!"];
                return Redirect::to('/settings/member-payment-update')->with('notifications', $message);
            }
        else:
            $metadata = [
                'meta' => [
                    'robots' => "noindex, nofollow",
                ]
            ];
            View::share('metadata', $metadata);
            $this->layout->customjs = View::make('frontend.fans.payment-customjs');
            $this->layout->content = View::make('frontend.fans.update-cc-content');
        endif;
    }

}