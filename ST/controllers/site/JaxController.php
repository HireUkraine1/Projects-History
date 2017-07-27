<?php

use Guzzle\Http\ClientInterface;

class JaxController extends BaseController
{


    public function userExists()
    {
        if (!Request::ajax()) return Redirect::to('/become-a-fan');
        $email = strtolower(Input::get('email'));
        if (Fan::where('email', $email)->count()):
            echo json_encode(['exist' => 1]);
        else:
            echo json_encode(['exist' => 0]);
        endif;

    }

    public function userSignup()
    {
        if (!Request::ajax()) return Redirect::to('/become-a-fan');
        $user = Input::get('user');
        $email = strtolower($user['email']);
        if (Fan::where('email', $email)->count()):
            echo json_encode(['type' => "error", "message" => "This user already exists...try  different email!"]);
        else:
            $name = trim($user['name']);
            $fan = new Fan;
            $hash = md5($email . date('U') . rand(1, 555434));
            $fan->email = $email;
            $fan->name = $name;
            $fan->password = md5($user['password']);
            $fan->status = 3;
            $fan->hash_link = $hash;
            $fan->save();
            if (isset($user['concertid'])):
                $fan->concerts()->attach($user['concertid']);
            endif;
            Mail::send('emails.frontend.fans.email-confirm', array('name' => $name, 'hash' => $hash), function ($message) use ($email, $name) {
                $message->to($email, $name)->subject('site: Please Confirm Your Email!');
            });
            echo json_encode(['type' => "warning", "message" => "Congratulations on your site Account! We sent you a confirmation email to {$email}. The email might take a few minutes to arrive. Also make sure to check your SPAM/JUNK folders."]);

        endif;
    }


    public function contactSupport()
    {
        if (!Request::ajax()) return Redirect::to('/become-a-fan');
        $input = Input::all();
        $fan = Session::get('fan');
        $fanInfo = Session::get('fanInfo');
        $email = $input['email'];
        $name = $input['name'];
        $additionalNote = '';
        $input['fan_id'] = 'guest';
        $input['info'] = false;

        if (!$fan):
            $fan = Fan::where('email', $email)->with('info')->first();
        endif;
        if ($fan):
            $info = $fan->info;
            $input['fan_id'] = $fan->id;
            $additionalNote = "ID: $fan->id\n\rSTATUS: $fan->status\n\rLAST LOGIN: $fan->last_login\n\rDONT BOTHER: $fan->dont_bother\n\rCREATED: $fan->created_at";
            if ($fan->info):
                $additionalNote .= "\n\rLEGAL NAME:$info->legal_name\n\rPHONES: $info->phone / $fan->cell_phone\n\rBDAY: $info->birthday \n\rADDY: $info->address, $info->city, $info->state - $info->zip\n\rSTATUS: $info->status";
            endif;
        endif;
        $subject = $input['subject'];
        $message = "\n\r Name: {$input['name']}\n\r Email: {$input['email']}\n\r Question: {$input['message']}\n\r\n\rFan Info: " . $additionalNote;

        mail("tourpass@site.com", $subject, $message);
        Mail::send('emails.frontend.fans.support', array('name' => $input['name']), function ($message) use ($email, $name) {
            $message->to($email, $name)->subject('site: Support Request Received!');
        });

        echo json_encode(['type' => "success", "message" => "site support received your message."]);
    }

    public function sendResetLink()
    {
        if (!Request::ajax()) return Redirect::to('/become-a-fan');

        $email = trim(strtolower(Input::get('email')));
        $fan = Fan::where('email', $email)->first();
        if ($fan):
            $hash = sha1(date('U') . $email . date('U') . rand(1, 435) . "site.random-string");
            $resetEntry = ['fan_id' => $fan->id, 'hash' => $hash, 'created_at' => date('Y-m-d H:i:s')];
            // just in case delete old entry
            DB::table('lost_passwords')->where('fan_id', $fan->id)->delete();
            DB::table('lost_passwords')->insert($resetEntry);
            $name = $fan->name;
            Mail::send('emails.frontend.fans.password-reset', array('name' => $name, 'hash' => $hash), function ($message) use ($email, $name) {
                $message->to($email, $name)->subject('site: Your Password Reset Link!');
            });
            echo "<div class='alert alert-success'><h3>We sent email to {$email} with reset link!</h3></div>";
        else:
            echo "<div class='alert alert-warning'><h3>Sorry, we don't have a fan with this email address, try again??</h3></div>";
        endif;
    }

    public function search()
    {
        if (!Request::ajax()) return Redirect::to('/become-a-fan');
        $op = Input::get('op');
        $query = trim(Input::get('query'));
        if ($query):

            switch ($op):
                case 'performer':
                    $performers = Performer::where('name', 'LIKE', '%' . $query . '%')->with('images')->get();
                    $show = View::make('frontend.fans.ajax.performer-results', ['performers' => $performers])->__tostring();
                    break;
                case 'location':
                    $slugit = StringHelper::create_slug($query);
                    $locations = Location::where('slug', 'LIKE', "%{$slugit}%")->orWhere('city', 'LIKE', "%{$query}%")->get();
                    $show = View::make('frontend.fans.ajax.location-results', ['locations' => $locations])->__tostring();
                    break;
            endswitch;
            echo $show;
        endif;
    }

    /**
     *    This function takes few security measuremment:
     * - check if there is an entry in lost_passwords for specific hash
     * - check if email posted and email in database matched
     * - check if decrypted checksum matches posted checksum
     */
    public function setPassword()
    {
        if (!Request::ajax()) return Redirect::to('/become-a-fan');
        $post = Input::all();
        $sessionId = Session::get('reset_fan_id');
        Session::forget('reset_fan_id');
        $checkSum = explode('-', $post['checksum']);
        //$checkSum = md5($hash.$fan->email)."-".$hash; //on receiving end must match to prevent fake posts
        $resetHash = $checkSum[1]; //get resetHash
        $fanReset = DB::table('lost_passwords')->where('hash', $resetHash)->first();
        if ($fanReset && $sessionId == $fanReset->fan_id):
            $fan = Fan::where('id', $fanReset->fan_id)->first();
            if ($fan->email == strtolower($post['email'])):
                $decryptChecksum = md5($resetHash . $fan->email);
                if ($decryptChecksum == $checkSum[0]):
                    $encPassword = md5($post['password']);
                    $fan->password = $encPassword;
                    $fan->save();
                    DB::table('lost_passwords')->where('id', $fanReset->id)->delete(); //drop the entry
                    echo json_encode(['error' => 0, 'message' => 'Password was reset!']); //attempted to post to not own email didn't guess checksum
                else:
                    echo json_encode(['error' => 1, 'message' => 'Failed to reset password!!']); //attempted to post to not own email didn't guess checksum
                endif;
            else:
                echo json_encode(['error' => 1, 'message' => 'Failed to reset password!!']); //attempted to post to not own email
            endif;
        else:
            echo json_encode(['error' => 1, 'message' => 'Cannot Find Fan!']);
        endif;
    }

    public function addRemovePerformerNotification()
    {
        $post = Input::all();
        $fan = Session::get('fan');
        $op = $post['op'];
        if ($op == 'add'):
            $pId = $post['performer_id'];
            $type = $post['type'];
            $days = trim($post['days']);
            $days = preg_replace('/[^0-9,]|,[0-9]*$/', '', $days);
            $performer = Performer::where('id', $pId)->first();
            $allowedTypes = ['email', 'stage'];
            if (!$performer || !in_array($type, $allowedTypes)):
                echo json_encode(['error' => 1, 'message' => 'Error Happened! Could not find performer or unknown type', 'data' => 0]);
            else:
                $currentNotification = NotificationSettings::where('fan_id', $fan->id)
                    ->where('days', $days)
                    ->where('type', $type)
                    ->where('performer_id', $pId)->count();
                if ($currentNotification):
                    echo json_encode(['error' => 1, 'message' => 'You already have ' . $type . ' reminder for ' . $days . ' days', 'data' => 0]);
                else:
                    $ns = new NotificationSettings;
                    $ns->fan_id = $fan->id;
                    $ns->type = $type;
                    $ns->performer_id = $pId;
                    $ns->days = $days;
                    $ns->save();
                    echo json_encode(['error' => 0, 'message' => 'Succesfully added ' . $type . ' reminder for ' . $days . ' days', 'data' => $ns->id]);
                endif;
            endif;
        # code...
        elseif ($op == 'remove'):
            $id = $post['id'];
            $ns = NotificationSettings::where('fan_id', $fan->id)->where('id', $id)->first();
            $days = $ns->days;
            $type = $ns->type;
            $ns->delete();
            echo json_encode(['error' => 0, 'message' => 'Succesfully removed ' . $type . ' reminder for ' . $days . ' days', 'data' => 0]);
        else:
            echo json_encode(['error' => 1, 'message' => 'Error Happened!', 'data' => 0]);
        endif;
    }

    public function addRemoveLocationTracking()
    {
        $post = Input::all();

        $fan = Session::get('fan');
        $op = $post['op'];
        $currentFan = Fan::where('id', $fan->id)->first();

        if ($op == 'add'):
            $location = Location::where('id', $post['id'])->first();
            $currentFan->locations()->detach($location->id);
            $currentFan->locations()->attach($location);
            echo json_encode(['error' => 0, 'message' => "Succesfully added {$location->city}, {$location->state} to the list!"]);
        elseif ($op == 'remove'):
            $location = Location::where('id', $post['id'])->first();
            $currentFan->locations()->detach($location->id);
            echo json_encode(['error' => 0, 'message' => "Succesfully removed {$location->city}, {$location->state} to the list!"]);
        else:
            echo json_encode(['error' => 1, 'message' => "Error Happened!"]);
        endif;
    }

    public function refreshLocations()
    {
        $fan = Session::get('fan');
        $trackingLocations = FanHelper::fetchFansLocations($fan->id);
        foreach ($trackingLocations as $city):
            echo "<li><a href='/track-location/{$city->slug}'>{$city->city}, {$city->state}</a>";
        endforeach;
    }

    /**
     * Disables or enables all notifications
     */
    public function donsaleother()
    {
        $fan = Session::get('fan');
        $new = ($fan->dont_bother) ? 0 : 1;
        $fan->dont_bother = $new;
        $fan->save();
        Session::put('fan', $fan);
        echo json_encode(['status' => $fan->dont_bother]);
    }

    public function getStates()
    {
        $country = Input::get('country');
        $states = Location::where('country', $country)->orderBy('state_full', 'asc')->groupBy('state')->get();
        echo json_encode($states);
    }

    public function getCoupon()
    {
        $code = strtoupper(Input::get('code'));
        $total = Input::get('total');
        $coupon = Coupon::where('code', $code)->first();
        echo json_encode(['error' => 0, 'message' => "Discount of \${$dollarsOff} was applied!", 'off' => $dollarsOff, 'newtotal' => $newtotal]);

    }

    private function _calculateDiscount($coupon, $total)
    {
        if ($coupon->dollars_off != 0): //this is ammount discount
            return $coupon->dollars_off;
        else:
            $percent = $coupon->percent_off / 100;
            $number = ($total * $percent);
            return number_format($number, 2, '.', ',');
        endif;
    }

    public function tourBus()
    {
        if (Input::has('op')):
            $formData = Input::all();
            if ($formData['op'] == 'contribute'):
                $body = "NEW CONTACT: \r\n";
                foreach ($formData as $key => $value):
                    $body .= "\n\r{$key}: \t\t{$value}";
                endforeach;
                mail("email@site.com,tourbuseconomics@site.com,julian@site.com", "saleE: Contact - {$formData['name']}", $body);
            else:
                VarsHelper::signup("saleE-signup", $formData['email']);
            endif;

            echo 1;
        endif;
    }

    public function concertNotification()
    {
        $message = ['error' => true, 'arrayTicketData' => 0, 'message' => 'Error'];
        echo json_encode($message);
    }

    private function _get_tickets($tnEventId = null)
    {
        $tn = new TicketNetwork\Api\TicketNetwork('ticketnetwork.tnProdData');
        $loadedConfig = Config::get('ticketnetwork.tnProdData.websiteConfigID');
        $params = array(
            'websiteConfigId' => $loadedConfig,
            'websiteConfigID' => $loadedConfig,
            'translationLanguageId' => '0',
            'eventId' => $tnEventId,
        );
        $tickets = $tn->run('GetEventTickets2', $params)->GetEventTickets2Result;
        return $tickets;
    }
}