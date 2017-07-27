<?php

namespace App\Http\Controllers\School;

use App\Http\Requests\CourseSchoolRequest as CreateRequest;
use App\Http\Requests\CourseSchoolRequest as UpdateRequest;
use App\Http\Traits\TraitCrudController;
use CrudController;

class SchoolCourseCrudController extends CrudController
{

    use TraitCrudController;

    private $access = ['list', 'update', 'create', 'delete'];
    private $school;
    private $course_id;
    private $school_id;

    public function __construct()
    {
        parent::__construct();
        $this->middleware(function ($request, $next) {
            $this->school = \Auth::guard('school')->user();
            if ($this->school->status_id !== 3) {
                return redirect('school/account');
            }
            $this->course_id = \Route::getCurrentRoute()->parameter('course');
            return $next($request);
        });
        /*
        |--------------------------------------------------------------------------
        | BsportC CRUD INFORMATION
        |--------------------------------------------------------------------------
         */
        $this->crud->setModel("\App\Models\Course");
        $this->crud->setRoute('/school/course');
        $this->crud->setEntityNameStrings('school course', 'school courses');
        $this->crud->access = $this->access;
        $this->school_id = ($course = \App\Models\Course::select('school_id')->where('id', '=', $this->course_id)->first()) ? $course->school_id : null;
        /*
        |----------------
        | CRUD COLUMNS
        |----------------
         */
        $this->crud->addColumn([
            // 1-n relationship
            'label' => 'Template',
            'name' => 'activity_id',
            'type' => 'select',
            'attribute' => 'name',
            'entity' => 'template',
            'model' => "\App\Models\ActivityCourses",
        ]);
        $this->crud->addColumn([
            'name' => 'date',
            'label' => 'Start Date',
        ]);
        $this->crud->addColumn([
            // 1-n relationship
            'label' => 'School Address',
            'name' => 'landing_locations_id',
            'type' => 'select',
            'attribute' => 'address',
            'entity' => 'address',
            'model' => "\App\Models\LandingLocation",
        ]);
        $this->crud->addColumn([
            'name' => 'quantity_lessons',
            'label' => 'Quantity Lessons',
        ]);
        $this->crud->addColumn([
            'name' => 'quantity_places',
            'label' => 'Quantity Places',
        ]);
        $this->crud->addColumn([
            'name' => 'busy_places',
            'label' => 'Busy Places',
        ]);
        $this->crud->addColumn([
            'name' => 'price',
            'label' => 'Price',
        ]);
        /*
        |----------------
        | CRUD FIELDS
        |----------------
         */
        $this->crud->addField([ // select_from_array
            'name' => 'landing_id',
            'label' => "Course Type",
            'type' => 'select_from_array',
            'options' => [],
            'allows_null' => false,
        ]);
        $this->crud->addField([
            'label' => 'Course Activity',
            'name' => 'activity_id',
            'type' => 'select_activity',
            'attribute' => 'name',
            'model' => "\App\Models\Activity",
            'landing_field_name' => 'landing_id',
            'school_id' => $this->school_id,
            'course_id' => $this->course_id,
        ]);
        $this->crud->addField([
            'label' => 'Course Template',
            'name' => 'activity_courses_id',
            'type' => 'select_template',
            'attribute' => 'name',
            'landing_field_name' => 'landing_id',
            'activity_field_name' => 'activity_id',
            'school_id' => $this->school_id,
            'course_id' => $this->course_id,
        ]);
        $this->crud->addField([
            'label' => 'School Address',
            'name' => 'landing_locations_id',
            'type' => 'select_address',
            'attribute' => 'address',
            'landing_field_name' => 'landing_id',
            'model' => "\App\Models\LandingLocation",
            'school_id' => $this->school_id,
            'course_id' => $this->course_id,
        ]);
        $this->crud->addField([
            'name' => 'date',
            'type' => 'datetime_picker',//date_picker
            'label' => 'Start Date',
        ]);
        $this->crud->addField([
            'name' => 'quantity_lessons',
            'label' => 'Quantity Lessons',
        ]);
        $this->crud->addField([
            'name' => 'quantity_places',
            'label' => 'Quantity Places',
        ]);
        $this->crud->addField([
            'name' => 'busy_places',
            'label' => 'Busy Places',
        ]);
        $this->crud->addField([
            'name' => 'price',
            'label' => 'Price',
        ]);
    }

    // public function coursesList($school_id)
    // {
    //     $this->crud->setRoute(config('base.route_prefix', 'admin') . '/school/' . $school_id . '/course');
    //     $this->crud->query->where('school_id', '=', $school_id);
    //     return parent::index();
    // }

    public function create()
    {
        $this->crud->create_fields['landing_id']["options"] = $this->schoolType();
        $this->crud->setRoute('/school/course');
        return parent::create();
    }

    private function schoolType()
    {
        $schoolCat = \App\Models\School::where('id', '=', $this->school->id)->with('categories')->first();//categories
        $schoolCat = $schoolCat ? $schoolCat->categories : [];
        $cat = [];
        foreach ($schoolCat as $activeCat) {
            $cat[] = $activeCat->id;
        }
        $schoolLandings = \App\Models\SchoolLanding::where('school_id', '=', $this->school->id)->with('category')->whereIn('sport', $cat)->get();
        $landings = [];
        $landings[0] = 'Select Type';
        foreach ($schoolLandings as $landing) {
            $landings[$landing->id] = $landing->category->name;
        }
        return $landings;
    }

    public function store(CreateRequest $request)
    {
        // dd( $request->all());
        $this->crud->hasAccessOrFail('create');
        $request->merge(['school_id' => $this->school->id]);
        if (is_null($request)) {
            $request = \Request::instance();
        }
        foreach ($request->input() as $key => $value) {
            if (empty($value) && $value !== '0') {
                $request->request->set($key, null);
            }
        }
        $item = $this->crud->create($request->except(['save_action', '_token', '_method']));
        $this->data['entry'] = $this->crud->entry = $item;
        $this->setSaveAction();
        return redirect('/school/course');
    }

    public function edit($id)
    {
        $this->crud->hasAccessOrFail('update');
        $this->crud->create_fields['landing_id']["options"] = $this->schoolType();
        // get the info for that entry
        $this->data['entry'] = $this->crud->getEntry($id);
        $this->data['crud'] = $this->crud;
        $this->data['saveAction'] = $this->getSaveAction();
        $this->data['fields'] = $this->crud->getUpdateFields($id);
        $this->data['fields']['landing_id']["options"] = $this->schoolType();
        $this->data['id'] = $id;
        return view($this->crud->getEditView(), $this->data);
    }

    public function update(UpdateRequest $request)
    {
        return parent::updateCrud($request);
    }
}
