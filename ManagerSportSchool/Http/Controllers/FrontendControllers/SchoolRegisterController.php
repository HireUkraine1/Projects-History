<?php

namespace App\Http\Controllers\FrontendControllers;

use App\Http\Requests\SchoolFrontRequest;
use App\Models;

class SchoolRegisterController extends BaseController
{
    private $title;

    public function __construct()
    {
        parent::__construct();
        $this->title = 'sport Accredited School Application-Australias'; // in the fuature add to settings and add other meta data
    }

    /**
     *  Filing an application from school for accreditation
     *
     **/
    public function index()
    {
        $title = $this->title;
        $activities = Models\Activity::all();
        $categories = Models\Category::all();
        $businessStructures = Models\BusinessStructure::all();
        return view('templates.membership_registration', compact('title', 'activities', 'categories', 'businessStructures'));
    }

    /**
     *  Create application
     *
     **/
    public function store(SchoolFrontRequest $request)
    {
        $school = Models\School::create($request->all());
        if ($request->has('categories')) {
            $school->categories()->sync($request->get('categories'));
        }
        if ($request->has('business_structure')) {
            $school->business()->sync($request->get('business_structure'));
        }
        if ($request->has('activities')) {
            $school->activity()->sync($request->get('activities'));
        }
        return redirect()->back()->with('success', 'Your request has been sent');
    }
}
