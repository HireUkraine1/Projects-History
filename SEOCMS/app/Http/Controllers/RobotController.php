<?php

namespace App\Http\Controllers;

use App\Support\Robots\RobotRules;

class RobotController extends Controller
{

    /**
     * Output dynamic robot.txt
     *
     * @return mixed
     */
    public function index()
    {
        $rules = new RobotRules();

        return response()->robots($rules);
    }
}
