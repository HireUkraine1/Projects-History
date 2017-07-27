<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function getBranch($ip)
    {
        $instances = DB::table('instance_creating')->where('ip', 'XX.XX.XX.' . $ip)->get();
        if (count($instances) > 0) {
            $instance = end($instances);
            echo "$instance->branch_name $instance->commit_hash::$instance->branch_custom_code $instance->sha_custom_code::$instance->branch_custom_patch $instance->sha_custom_patch";
        } else {
            echo 'error';
        }
    }

    public function gitLog(Request $request)
    {
        if ($request->isMethod('post')) {
            $ip = $request->ip;
            $key = $request->key;
            $msg = $request->msg;

            if ($key != 'XXXX') {
                echo 'false api key';
            } else {
                $db = DB::table('instance_creating')
                    ->where('ip', 'XX.XX.XX.' . $ip)
                    ->first();
                if (!is_null($db)) {
                    $log = json_decode($db->log);
                    $log[] = $msg;
                    $log = json_encode($log);

                    DB::table('instance_creating')
                        ->where('ip', 'XX.XX.XX.' . $ip)
                        ->update(['log' => $log]);
                    echo 'success';
                } else {
                    echo 'false instance is not exsist';
                }
            }
        } else {
            echo 'false method';
        }
    }
}

 
