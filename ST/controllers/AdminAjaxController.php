<?php

class AdminAjaxController extends BaseController
{

    /*
___________________________________________________________________
    Below are ajax controllers and are filtered by route to be
    Authed AND Ajax only. No direct posts
___________________________________________________________________
    */

    public function load_location()
    {
        $type = Input::get('type');
        $geo = new GeoLocation;
        switch ($type) {
            case 'states': //load states
                $country = Input::get('country');
                $states = $geo->get_states($country);
                $show = $states->toArray();
                echo json_encode($show);
                break;
            case 'cities': //load cities
                $state = Input::get('state');
                $cities = $geo->get_cities($state);
                $show = View::make('admin.ajax.city-show-cities', ['cities' => $cities]);
                echo $show;
                break;
            default:
                # code...
                break;
        }

    }

    public function load_nearby()
    {
        $slug = Input::get('slug');
        $distance = Input::get('distance');
        if (!$distance) $distance = 20;
        echo $slug;
        echo $distance;
        $nearby = DB::table('location_nearby')->where('center_slug', $slug)->where('distance', '<=', $distance)->orderBy('distance', 'ASC')->get();
        $location = Location::where('slug', $slug)->with('burbs')->with('burbs.location')->first();
        $currentMetro = [];
        foreach ($location->burbs as $b):
            $currentMetro[] = $b->location_id;
        endforeach;
        $show = View::make('admin.ajax.nearby', ['nearby' => $nearby, 'currentMetro' => $currentMetro, 'location' => $location])->__tostring();
        //
        echo $show;
    }

    public function add_remove_metro()
    {
        $op = Input::get('op');
        switch ($op) {
            case 'add':
                $location_id = Input::get('id');
                $parent_id = Input::get('parent_id'); //cluster

                $burb = Burb::where(['location_id' => $location_id])->first();
                if (!$burb) $burb = new Burb;
                $burb->parent_location_id = $parent_id;
                $burb->location_id = $location_id;
                $burb->save();
                // $location->metro()->attach($cluster->id);
                echo $burb->id;
                # code...
                break;

            case 'remove':
                $id = Input::get('id');
                $parent_slug = Input::get('parent_slug');
                $nearby = Burb::where('id', $id)->with('location')->first();
                $return = new stdClass;
                $return->location_id = $nearby->location_id;
                $return->city = $nearby->location->city . ", " . $nearby->location->state;
                $dist = DB::table('location_nearby')->where('center_slug', $parent_slug)->where('slug', $nearby->location->slug)->first();
                $return->distance = number_format($dist->distance, 2, ',', '.');
                $return->ok = 1;
                $nearby->delete();
                echo json_encode($return);
            default:
                # code...
                break;
        }

    }

    public function quickfind()
    {
        $q = trim(Input::get('term'));
        $slug = StringHelper::create_slug($q);
        $cities = Location::where('slug', 'LIKE', "%$slug%")->get();
        $results = [];
        foreach ($cities as $c):
            $tmp1['slug'] = $c->slug;
            $tmp1['label'] = $c->city . ", " . $c->state;
            array_push($results, $tmp1);
        endforeach;

        echo json_encode($results);
    }

    public function global_search()
    {
        $q = trim(Input::get('term'));

        if (Str::length($q) > 2):
            $return = [];
            $performers = Performer::where('name', 'LIKE', "%$q%")->get();
            foreach ($performers as $p):
                $tmp['id'] = $p->id;
                $tmp['destination'] = "/saleadminpanel/performer/" . $p->slug;
                $tmp['label'] = "PERFORMER: " . $p->name;
                // $tmp['value']		= $p->name;
                array_push($return, $tmp);
            endforeach;

            $announcements = Announcement::where('title', 'LIKE', "%$q%")->get();
            foreach ($announcements as $a):
                $tmp2['id'] = $a->id;
                $tmp2['destination'] = "/saleadminpanel/news/editannouncement/" . $a->id;
                $tmp2['label'] = "NEWS: " . $a->title;
                array_push($return, $tmp2);
            endforeach;

            $slug = StringHelper::create_slug($q);
            $cities = Location::where('slug', 'LIKE', "%$slug%")->get();
            foreach ($cities as $c):
                $tmp1['id'] = $c->id;
                $tmp1['destination'] = "/saleadminpanel/city/" . $c->slug;
                $tmp1['label'] = "LOCATION: " . $c->city . ", " . $c->state;
                array_push($return, $tmp1);
            endforeach;

            echo json_encode($return);
        endif;
    }

    public function feature_city()
    {

        $action = (Input::get('featured')) ? "remove" : "feature";
        $slug = Input::get('slug');
        $city = Input::get('city');
        $state = Input::get('state');
        $country = Input::get('country');
        $feat_slug = StringHelper::create_slug($city);
        $location = Location::where('slug', $slug)->first();
        switch ($action) {
            case 'remove':

                //remove from featured
                DB::table('featured_locations')->where('real_slug', '=', $slug)->delete();

                //remove from redirect (those redirects are set for legacy links, for example boston-ma to boston, so remove boston-ma)
                SlugRedirect::where('url', '=', "/" . $slug)->delete();
                //add redirect from (to) to real slug

                UrlHelper::createRedirect($feat_slug, $slug, 301);
                /*$data = [
                              'url'				=> "/".$feat_slug,
                              'to_url'			=> "/".$slug,
                              'redirect_type'		=> 301,
                              'status'			=> 1,
                          ];
                SlugRedirect::create($data);*/
                $return = ['error' => 0, 'message' => 'Redirect Successfully Setup!'];

                echo json_encode($return);
                break;
            case 'feature':

                //add to featured
                $data = [
                    'city' => $city,
                    'state' => $state,
                    'country' => $country,
                    'real_slug' => $slug,
                    'feat_slug' => $feat_slug,
                    'set_by_admin_id' => 1,
                    'location_id' => $location->id
                ];
                DB::table('featured_locations')->insert($data);

                //remove any legacy unfeatured redirects
                SlugRedirect::where('to_url', '=', "/" . $slug)->delete();

                //add redirect to feature slug
                UrlHelper::createRedirect($slug, $feat_slug, 301);
                /*$data = [
                              'url'				=> "/".$slug,
                              'to_url'			=> "/".$feat_slug,
                              'redirect_type'		=> 301,
                              'status'			=> 1,
                          ];
                SlugRedirect::create($data);*/
                $return = ['error' => 0, 'message' => 'Redirect Successfully Setup!'];

                echo json_encode($return);
                break;
            default:
                $return = ['error' => 1, 'message' => 'Error Occured!'];

                echo json_encode($return);
                # code...
                break;
        }

    }

    public function addedit_category()
    {

        $category = trim(Input::get('category'));
        if (!$category) {
            $return = ['error' => 1, 'message' => "You submitted empty cateogry "];
        } else {
            $order = Input::get('order');
            $id = Input::get('id');
            $slug = StringHelper::create_slug($category);

            if ($cat = Category::find($id)): // we are updating
                if ($existingCat = Category::where('slug', '=', $slug)->where('id', '<>', $id)->first()):
                    $oldName = $cat->first()->category;
                    $return = ['error' => 1, 'message' => " Hold on!  Sorry, but category $category alredy exists! ", 'oldname' => $oldName];
                else:
                    $oldName = $cat->category;
                    $oldSlug = $cat->slug;
                    $cat->category = $category;
                    $cat->slug = $slug;
                    $cat->order = $order;
                    $cat->save();
                    $return = ['error' => 0, 'message' => "The category $oldName ($oldSlug) has been updated to $category ($slug).", 'slug' => $slug];
                endif;
            else:
                if ($cat = Category::where('slug', '=', $slug)->first()):
                    $return = ['error' => 1, 'message' => "Sorry, but category $category alredy exists! Try editing? "];
                else:
                    $cat = Category::create([
                        'slug' => $slug,
                        'category' => $category,
                        'order' => $order
                    ]);
                    $return = ['error' => 0, 'message' => "Successfully created a category $category", 'slug' => $slug];
                endif;
            endif;
        }
        echo json_encode($return);
    }

    public function lookup_performer()
    {
        $q = Input::get('term');
        if (Str::length($q) > 2):
            $performers = Performer::where('name', 'LIKE', "%$q%")->get();

            $return = [];
            foreach ($performers as $p):
                $tmp['id'] = $p->id;
                $tmp['slug'] = $p->slug;
                $tmp['label'] = $p->name;
                $tmp['value'] = $p->name;
                array_push($return, $tmp);
            endforeach;
            echo json_encode($return);
        endif;

    }

    public function admin_category()
    {
        $op = Input::get('op');
        $id = Input::get('id');
        $category = Category::find($id);
        switch ($op) {
            case 'move':
                $toId = Input::get('toId');
                $toCat = Category::find($toId);
                $posts = $category->announcements()->get(); //get all posts

                $category->announcements()->detach(); //detach all posts from this category нах!
                foreach ($posts as $p) : //look through all posts and collect other categories
                    $currentCats = $p->categories()->get();
                    $reSync = [];
                    foreach ($currentCats as $c):
                        array_push($reSync, $c->id);
                    endforeach;
                    array_push($reSync, $toId); //finally add the last category
                    $p->categories()->sync($reSync);
                endforeach;
                $return = ['error' => 0, 'message' => "Successfully moved a announcements to $toCat->category"];
                Session::flash('message', "Successfully moved a announcements to $toCat->category");

                break;
            case 'remove':
                $name = $category->category;
                if ($category->announcements()->count()):
                    $return = ['error' => 1, 'message' => "Cannot delete $name category because it has announcements!"];
                else:
                    $category->delete();
                    $return = ['error' => 0, 'message' => "Successfully deleted $name"];
                endif;
                break;;
            default:
                # code...
                break;
        }

        echo json_encode($return);
    }

    public function add_redirect()
    {
        $to = Input::get('to');
        $from = Input::get('from');
        $type = Input::get('type');
        $force = Input::get('force');
        if ($force):
            $to = StringHelper::create_slug($to);
            $from = StringHelper::create_slug($from);
        endif;
        if (UrlHelper::isRedirected($from)):
            $return = ['error' => 1, 'message' => "Redirect with this from url already exists!!!"];
        else:
            $data = UrlHelper::createRedirect($from, $to, $type);
            $return = [
                'error' => 0,
                'message' => "Created Ok!!",
                'from' => $data->url,
                'to' => $data->to_url,
                'type' => $data->redirect_type,
                'created' => $data->created_at,
                'id' => $data->id
            ];
        endif;
        echo json_encode($return);
    }

    public function remove_redirect()
    {
        $id = Input::get('id');
        DB::table('active_redirects')->where('id', $id)->delete();
        echo json_encode(['error' => 0, 'message' => "Successfully deleted!"]);
    }

    public function feature_performer_massaction()
    {
        $data = Input::get('data');
        $message = '';
        foreach ($data as $item):
            $id = $item['id'];
            $p = Performer::find($id);
            $type = $item['type'];
            $feature = $item['feature'];
            $fp = FeaturedPerformer::firstOrCreate([
                'performer_id' => $id
            ]);
            $fp->{$type} = $feature;

            $message .= ($feature) ? "<p>$p->name is <strong>ADDED</strong> <i>$type</i> </p>" : "<p>$p->name is <strong style='color:red;'>REMOVED</strong> from <i>$type</i> </p>";
            $fp->save();
            if (!$fp->geo && !$fp->home && !$fp->side):
                $fp->delete();
                //	$message .= "<p style='color:red;'>$p->name HAS NO FEATURED SECTIONS!!!!</p>";
            endif;
        endforeach;
        echo json_encode(['error' => 0, 'message' => $message]);

    }

    public function feature_performer()
    {
        $id = Input::get('id');
        $type = Input::get('type');
        $feature = Input::get('feature');
        $fp = FeaturedPerformer::firstOrCreate([
            'performer_id' => $id
        ]);
        $fp->{$type} = $feature;
        $message = ($feature) ? "Featured performer in $type " : "<strong>REMOVED FEATURED</strong> from $type ";
        $fp->save();
        if (!$fp->geo && !$fp->home && !$fp->side):
            $fp->delete();
            $message .= "<p>THIS PERFORMER HAS NO FEATURED SECTIONS!!!!</p>";
        endif;
        echo json_encode(['error' => 0, 'message' => $message]);
    }

    public function spin_text()
    {
        $type = Input::get('type');
        switch ($type):
            case 'pb': //performer bio
                $id = Input::get('performer_id');
                $textData = Spinner::performer_bio_text($id);
                $textData->expire = date('Y-m-d', strtotime($textData->expire));
                echo json_encode($textData);
                break;
            case 'pt': //performer tour
                $id = Input::get('performer_id');
                $textData = Spinner::performer_tour_dates($id);
                $textData->expire = date('Y-m-d', strtotime($textData->expire));
                echo json_encode($textData);
                break;
            case 'pd': //performer discography
                $id = Input::get('performer_id');
                $textData = Spinner::performer_discography($id);
                $textData->expire = date('Y-m-d', strtotime($textData->expire));
                echo json_encode($textData);
                break;

            default:
                # code...
                break;
        endswitch;
    }

    public function save_text()
    {
        $type = Input::get('type');
        switch ($type):
            case 'pb': //performer bio
                $performer_id = Input::get('performer_id');
                $text = Input::get('text');
                $expire = Input::get('expire');
                $expire = ($expire) ? date('Y-m-d', strtotime($expire)) : '0000-00-00';
                $custom = Input::get('custom');
                $currentText = DB::table('page_texts')->where('performer_id', $performer_id)
                    ->where('type', 'pb')
                    ->first();
                if (!$currentText):
                    $insertData = [
                        'performer_id' => $performer_id,
                        'type' => 'pb',
                        'expire' => $expire,
                        'text' => $text,
                        'custom' => $custom,
                    ];
                    DB::table('page_texts')->insert($insertData);
                else:
                    $updateData = [
                        'expire' => $expire,
                        'text' => $text,
                        'custom' => $custom,
                    ];

                    DB::table('page_texts')->where('performer_id', $performer_id)
                        ->where('type', 'pb')
                        ->update($updateData);
                endif;
                $returnData = ['text' => $text, 'expire' => $expire, 'custom' => $custom];
                break;

            case 'pt': //performer tour
                $performer_id = Input::get('performer_id');
                $text = Input::get('text');
                $expire = Input::get('expire');
                $expire = ($expire) ? date('Y-m-d', strtotime($expire)) : '0000-00-00';
                $custom = Input::get('custom');
                $currentText = DB::table('page_texts')->where('performer_id', $performer_id)
                    ->where('type', 'pt')
                    ->first();
                if (!$currentText):
                    $insertData = [
                        'performer_id' => $performer_id,
                        'type' => 'pt',
                        'expire' => $expire,
                        'text' => $text,
                        'custom' => $custom,
                    ];
                    DB::table('page_texts')->insert($insertData);
                else:
                    $updateData = [
                        'expire' => $expire,
                        'text' => $text,
                        'custom' => $custom,
                    ];

                    DB::table('page_texts')->where('performer_id', $performer_id)
                        ->where('type', 'pt')
                        ->update($updateData);
                endif;
                $returnData = ['text' => $text, 'expire' => $expire, 'custom' => $custom];
                break;

            case 'pd': //performer dicography
                $performer_id = Input::get('performer_id');
                $text = Input::get('text');
                $expire = Input::get('expire');
                $expire = ($expire) ? date('Y-m-d', strtotime($expire)) : '0000-00-00';
                $custom = Input::get('custom');
                $currentText = DB::table('page_texts')->where('performer_id', $performer_id)
                    ->where('type', 'pd')
                    ->first();
                if (!$currentText):
                    $insertData = [
                        'performer_id' => $performer_id,
                        'type' => 'pd',
                        'expire' => $expire,
                        'text' => $text,
                        'custom' => $custom,
                    ];
                    DB::table('page_texts')->insert($insertData);
                else:
                    $updateData = [
                        'expire' => $expire,
                        'text' => $text,
                        'custom' => $custom,
                    ];

                    DB::table('page_texts')->where('performer_id', $performer_id)
                        ->where('type', 'pd')
                        ->update($updateData);
                endif;
                $returnData = ['text' => $text, 'expire' => $expire, 'custom' => $custom];
                break;

            default:
                # code...
                break;
        endswitch;
        echo json_encode($returnData);
    }

    public function toggleCoupon()
    {
        $data = Input::all();
        $c = Coupon::where('id', $data['id'])->first();
        $c->status = $data['status'];
        $c->save();
        $message = ($data['status']) ? "Coupon <strong>{$c->code}</strong> was <strong>ACTIVATED</strong>" : "Coupon <strong>{$c->code}</strong> was <strong>DISABLED</strong>";
        echo json_encode(['error' => 0, 'message' => $message]);

    }

    public function updateMemberInfo()
    {
        $idFan = Input::get('id-fan');
        $newStatus = Input::get('status');;
        $newNotification = Input::get('dont_bother');
        $newPsw = Input::get('newpass');

        $fanInfo = Fan::where('id', $idFan)->first();
        if ($newPsw != '') $fanInfo->password = md5($newPsw);
        $fanInfo->status = $newStatus;
        $fanInfo->dont_bother = $newNotification;
        $fanInfo->save();

        $message = 'Success';
        echo json_encode(['error' => 0, 'message' => $message]);
    }

    public function updateMemberInfoLegal()
    {
        $idFan = Input::get('id-fan');
        $emaiInfo = Input::get('email-info');
        $phoneInfo = Input::get('phone-info');
        $cellPhoneInfo = Input::get('cell-phone-info');
        $addressInfo = Input::get('address-info');
        $addresInfo2 = Input::get('address2-info');
        $countryInfo = Input::get('country-info');
        $stateInfo = Input::get('state-info');
        $sityInfo = Input::get('city-info');
        $zipInfo = Input::get('zip-info');
        $statusInfo = Input::get('status-info');

        $fanInfo = FanInfo::where('fan_id', $idFan)->first();
        $fanInfo->email = $emaiInfo;
        $fanInfo->phone = $phoneInfo;
        $fanInfo->cell_phone = $cellPhoneInfo;
        $fanInfo->address = $addressInfo;
        $fanInfo->address_2 = $addresInfo2;
        $fanInfo->country = $countryInfo;
        $fanInfo->state = $stateInfo;
        $fanInfo->city = $sityInfo;
        $fanInfo->zip = $zipInfo;
        $fanInfo->status = $statusInfo;
        $fanInfo->save();

        $message = 'Success';
        echo json_encode(['error' => 0, 'message' => $message]);
    }

    public function deleteMember()
    {
        $fanId = Input::get('idMember');
        $fan = Fan::where('id', $idFan)->first();
        $fanEmail = $fan->email;
        //Keep fans, just disable it
        $fan->status = -1;
        DB::table('profiles')->where('fan_id', $fanId)->delete();
        DB::table('lost_passwords')->where('fan_id', $fanId)->delete();
        DB::table('notification_settings')->where('fan_id', $fanId)->delete();
        DB::table('location_fans')->where('fan_id', $fanId)->delete();
        DB::table('pending_fans')->where('email', $fanEmail)->delete();
        $fan->save();

        echo json_encode(['error' => 0]);

    }

    public function updateCC()
    {
        $id = Input::get('id-fan');
        $fan = Fan::whereid($id)->first();
        $stripeId = $fan->info->stripe_id;

        if ($fan->info->status == -1):
            $message = "Member hasn't paid yet";
            echo json_encode(['error' => 1, 'message' => $message]);
            die();
        endif;
        if (Input::has('stripeToken')):
            try {

                $token = Input::get('stripeToken');
                $sCustomer = \Stripe\Customer::retrieve($stripeId);
                $sCustomer->card = $token;
                $sCustomer->save();
                $message = 'Successfully updated account!';
                echo json_encode(['error' => 0, 'message' => $message]);

            } catch (Exception $e) {
                $message = "Error when updating account!";
                echo json_encode(['error' => 1, 'message' => $message]);
            }
        endif;

    }

    public function activationEmail()
    {
        $id = Input::get('fanId');

        $fan = Fan::where('id', $id)->first();

        if ($fan->status === 0):
            $message = "This account was BLOCKED!";
            $eM = 1;
        else:
            $name = $fan->name;
            $hash = $fan->hash_link;
            $email = $fan->email;
            Mail::send('emails.frontend.fans.email-confirm', array('name' => $name, 'hash' => $hash), function ($message) use ($email, $name) {
                $message->to($email, $name)->subject('site: Please Confirm Your Email!');
            });
            $message = "Email sent!";
            $eM = 0;
        endif;

        echo json_encode(['error' => $eM, 'message' => $message]);

    }

    public function resetPassword()
    {
        $id = Input::get('fanId');

        $fan = Fan::where('id', $id)->first();
        $email = $fan->email;
        $hash = sha1(date('U') . $email . date('U') . rand(1, 435) . "site.random-string");
        $resetEntry = ['fan_id' => $id, 'hash' => $hash, 'created_at' => date('Y-m-d H:i:s')];
        // just in case delete old entry
        DB::table('lost_passwords')->where('fan_id', $id)->delete();
        DB::table('lost_passwords')->insert($resetEntry);
        $name = $fan->name;
        Mail::send('emails.frontend.fans.password-reset', array('name' => $name, 'hash' => $hash), function ($message) use ($email, $name) {
            $message->to($email, $name)->subject('site: Your Password Reset Link!');
        });

        $messageAdmin = "reset password sent";

        echo json_encode(['error' => 0, 'message' => $messageAdmin]);

    }

    public function checkDateConcert()
    {

        $date = date('Y-m-d', strtotime(Input::get('data')));
        //$dateArray=array('date'=>$date);
        //$date = "2015-06-27"; // date('Y-m-d'); FOR TEST ONLY becuase database does not have new data. Last copy was in March

        $recentPerformers = Performer::whereHas('concerts', function ($q) use ($date) {

            $q->where('created_at', 'LIKE', "%$date%")->where('date', '>', date('y-m-d'));

        })->with(['concerts' => function ($q2) use ($date) {

            $q2->where('created_at', 'LIKE', "%$date%")->orderBy('date', 'DESC');

        }])->get();


        $data = array('date' => $date, 'recentPerformers' => $recentPerformers);
        echo json_encode($data);
        //echo json_encode($dateArray);


    }

    public function concertsChart()
    {
        $countDay = Input::get('i');
        $concert = array();
        /*if($countDay=='all'){
            $lastDays=Concert::orderBy('created_at', 'ASC')->first();
            $datetime1 = new DateTime($lastDays->created_at);
            $datetime2 = new DateTime();
            $interval = $datetime1->diff($datetime2);
            $countDay=$interval->days;
        }*/

        for ($i = 0; $i <= $countDay; $i++) {
            $date = date("Y-m-d", time() - ($i * (24 * 60 * 60)));
            //$d=date("M d", strtotime($date));
            $concert[] = array($date, Concert::where('created_at', 'LIKE', "%$date%")->count());

        }

        $data = array('date' => date('F j, Y', strtotime($date)), 'concert' => $concert, 'error' => 0, 'day' => $countDay);
        echo json_encode($data);

    }


    public function performersChart()
    {
        $countDay = Input::get('i');

        $performers = array();

        for ($i = 0; $i <= $countDay; $i++) {
            $date = date("Y-m-d", time() - ($i * (24 * 60 * 60)));

            $performers[] = array($date, Concert::where('created_at', 'LIKE', "%$date%")->distinct('name')->count('name'));

        }

        $data = array('performers' => $performers, 'error' => 0, 'day' => $countDay);

        echo json_encode($data);

    }

    public function ticketsChart()
    {
        $countDay = Input::get('i');
        $tickets = array();

        for ($i = 0; $i <= $countDay; $i++) {
            $date = date("Y-m-d", time() - ($i * (24 * 60 * 60)));

            $tickets[] = array($date, OrderTickets::where('order_at', 'LIKE', "%$date%")->count());

        }

        $data = array('date' => $date, 'tickets' => $tickets, 'error' => 0);
        echo json_encode($data);
    }

    public function revenueMarkupChart()
    {

        $countDay = Input::get('i');
        $revenue = array();


        for ($i = 1; $i <= $countDay; $i++) {

            $date = date("Y-m-d", time() - ($i * (24 * 60 * 60)));
            $revenue[] = array($date, OrderTickets::where('order_at', 'LIKE', "%$date%")->get(array('retail', 'markup')));
        }

        $data = array('date' => date('F j, Y', strtotime($date)), 'revenue' => $revenue, 'error' => 0);
        echo json_encode($data);


    }

    public function searchAllCity()
    {

        $searchCity = Input::get('term');
        $city = GeoLocation::whereRaw("city REGEXP '{$searchCity}([^0-9]*)'")->groupBy('slug')->get();
        $return = [];
        foreach ($city as $i):
            $tmp['value'] = $i->slug;
            $tmp['label'] = "$i->city ($i->state)";

            array_push($return, $tmp);
        endforeach;
        echo json_encode($return);

    }


    public function searchAllState()
    {

        $searchState = Input::get('term');
        $state = Location::whereRaw("state_full REGEXP '{$searchState}([^0-9]*)'")->orderBy('state_full', 'asc')->groupBy('state')->get();
        $return = [];
        foreach ($state as $i):
            $tmp['value'] = $i->state;
            $tmp['label'] = "{$i->state_full} ($i->state)";

            array_push($return, $tmp);
        endforeach;
        echo json_encode($return);

    }

    public function fieldForm()
    {
        // $fan=Fan::where('dont_bother', 0)->orderBy('id','asc')->lists('id');

        $allFans = Fan::where('dont_bother', 0);

        if (Input::has('status') && Input::get('status') != ''):
            $allFans->where('status', Input::get('status'));
        endif;

        if (Input::has('last_login_from') && Input::get('last_login_from') != ''):
            $allFans->where('last_login', '>', date('Y-m-d', strtotime(Input::get('last_login_from'))));
        endif;

        if (Input::has('last_login_to') && Input::get('last_login_to') != ''):
            $allFans->where('last_login', '<=', date('Y-m-d', strtotime(Input::get('last_login_to'))));
        endif;

        if (Input::has('created_at_from') && Input::get('created_at_from') != ''):
            $allFans->where('created_at', '>', date('Y-m-d', strtotime(Input::get('created_at_from'))));
        endif;

        if (Input::has('created_at_to') && Input::get('created_at_to') != ''):
            $allFans->where('created_at', '<=', date('Y-m-d', strtotime(Input::get('created_at_to'))));
        endif;

        if (Input::has('payStatus') && Input::get('payStatus') != ''):
            $status = Input::get('payStatus');
            $allFans->whereHas('info', function ($query) use ($status) {
                $query->where('status', $status);
            });
        endif;

        // search states
        if (Input::has('states')):
            $allFans->whereHas('info', function ($query) {
                $query->whereIn('state', Input::get('states'));
            });
        endif;


        // search cities
        if (Input::has('cities')):


            $searchCities = Input::get('cities');
            $location = [];

            foreach ($searchCities as $city):
                $geosearch = GeoLocation::where('slug', $city)->first();
                $location[$geosearch->state][] = $geosearch->city;
                $locationNearby = DB::table('location_nearby')->where('center_slug', $city)->where('distance', '<', 100)->whereNotIn('state', ['00'])->take(60)->orderBy('distance', 'ASC')->get(['city', 'state']);

                foreach ($locationNearby as $locations):
                    $location[$locations->state][] = $locations->city;
                endforeach;

            endforeach;

            if (Input::has('states')):

                foreach (Input::get('states') as $state):
                    if (array_key_exists($state, $location)):
                        unset($location[$state]);
                    endif;
                endforeach;

            endif;

            $idFans = [];
            foreach ($location as $key => $value):

                $geoFansId = Fan::where('id', '<>', 0)->where('dont_bother', 0)->whereHas('info', function ($query) use ($key, $value) {
                    $query->where('state', $key)->whereIn('city', $value);
                })->get(['id']);

                if (is_object($geoFansId)):
                    foreach ($geoFansId as $idF):
                        $idFans[] = $idF->id;
                    endforeach;
                endif;
            endforeach;
            $post = $idFans;
            if (count($idFans) > 0 && Input::has('states')):

                $fan = $allFans->orderBy('id', 'asc')->lists('id');

                $allCityFansId = Fan::where('dont_bother', 0)->whereHas('info', function ($query) use ($idFans) {
                    $query->whereIn('id', $idFans);
                })->orderBy('id', 'asc')->lists('id');

                $fan = array_merge($fan, $allCityFansId);

            elseif (count($idFans) > 0 && !Input::has('states')):

                $allFans->whereHas('info', function ($query) use ($idFans) {
                    $query->whereIn('id', $idFans);
                });
                $fan = $allFans->orderBy('id', 'asc')->lists('id');

            elseif (count($idFans) == 0 && Input::has('states')):

                $fan = $allFans->orderBy('id', 'asc')->lists('id');

            elseif (count($idFans) == 0 && !Input::has('states')):

                $allFans->whereHas('info', function ($query) {
                    $query->whereIn('id', 0);
                });
                $fan = $allFans->orderBy('id', 'asc')->lists('id');

            endif;

        else:
            $fan = $allFans->orderBy('id', 'asc')->lists('id');
        endif;


        $maillists = Maillist::all();
        ///$post=$locationNearby;
        $data = array('fan' => $fan, 'maillists' => $maillists, 'error' => 0);

        echo json_encode($data);
    }


    public function memberToList()
    {

        $members = Input::get('members');
        $idList = Input::get('idlist');


        //$maillist=Maillist::whereid($idList)->first();

        foreach ($members as $member) {

            //$maillist->fans()->detach($member);

            $maillist = Maillist::whereHas('fans', function ($query) use ($member) {
                $query->where('fan_id', 'LIKE', $member);
            })->whereid($idList)->first();

            if (!is_object($maillist)) {
                $list = Maillist::whereid($idList)->first();
                $list->fans()->attach($member, array('status' => 1));
            }


        }


        $data = array('error' => 0);
        echo json_encode($data);

    }


    /*

    public function searchAllState(){
        $country = Input::get('country');
        $searchState=Input::get('state');
        $states = Location::where('country',$country)->whereRaw("state_full REGEXP '{$searchState}([^0-9]*)'")->orderBy('state_full','asc')->groupBy('state')->get();
        echo json_encode($states);

    }*/

    public function statistic()
    {

        $tag = Input::get('tag');
        $http1 = 'https://mandrillapp.com/api/1.0/messages/search-time-series.json';

        $query1 = ' {
                                "key": "iA96c2enNAA42PEV276slQ",
                                "tags": [

                                    "' . $tag . '"
                                ]
                            }';

        $ch1 = curl_init();
        curl_setopt($ch1, CURLOPT_URL, $http1);
        curl_setopt($ch1, CURLOPT_HEADER, 0);
        curl_setopt($ch1, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch1, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($ch1, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch1, CURLOPT_POST, 1);
        curl_setopt($ch1, CURLOPT_POSTFIELDS, $query1);
        $response1 = curl_exec($ch1);
        curl_close($ch1);

        $response1 = json_decode($response1);
        $data = array('error' => 0, 'response' => $response1);
        echo json_encode($data);
    }


}
