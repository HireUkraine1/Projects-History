<?php

use Guzzle\Http\Client;

class BufferController extends BaseController
{
    public function index()
    {
        try {
            $client = new GuzzleHttp\Client();
            $http = 'https://api.bufferapp.com/1/profiles.json?access_token=1/a6ebb9275bc8ef00efc9b93bb4e67e1f';
            $response = $client->get($http);
            $reasonseBody = $response->gesaleody();
            $bufferUserProfile = json_decode($reasonseBody);
            foreach ($bufferUserProfile as $provider):
                $settingsRow = SiteSetting::where('value', $provider->{'_id'})->first();
                if (!is_object($settingsRow)):
                    $setting = new SiteSetting;
                    $setting->string_id = $provider->formatted_service;
                    $setting->label = $provider->service;
                    $setting->description = $provider->service_username;
                    $setting->value = $provider->{'_id'};
                    $setting->save();
                endif;
            endforeach;

            return Redirect::to('/saleadminpanel/settings/buffer');
        } catch (Exception $e) {
            echo 'Exception text:' . $e->getMessage();
        }

    }


}