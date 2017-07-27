<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CourseRequest as CreateRequest;
use App\Http\Requests\CourseRequest as UpdateRequest;
use App\Http\Traits\TraitCrudController;
use CrudController;

class SchoolCourseCrudController extends CrudController
{
    use TraitCrudController;

    private $access = ['list', 'update', 'create', 'delete'];
    private $school_id;
    private $course_id;

    public function __construct()
    {
        parent::__construct();
        /*
        |--------------------------------------------------------------------------
        | BsportC CRUD INFORMATION
        |--------------------------------------------------------------------------
         */

        // $this->middleware(function ($request, $next) {
        $this->school_id = \Route::getCurrentRoute()->hasParameter('school_id') ? \Route::getCurrentRoute()->parameter('school_id') : 0;
        $this->course_id = \Route::getCurrentRoute()->hasParameter('course_id') ? \Route::getCurrentRoute()->parameter('course_id') : 0;
        //     return $next($request);
        // });
        $this->crud->setModel("\App\Models\School");
        $name = 'school course';
        $list = 'school courses';
        if ($this->school_id) {
            $name = 'school "' . $this->crud->getEntry($this->school_id)->name . '" ' . 'course ';
            $list = 'school "' . $this->crud->getEntry($this->school_id)->name . '" ' . 'courses';
        }
        $this->crud->setModel("\App\Models\Course");
        $this->crud->setRoute(config('base.route_prefix', 'admin') . '/school/course');
        $this->crud->setEntityNameStrings($name, $list);
        $this->crud->access = $this->access;
        $landings = $this->schoolType();
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
            'options' => $landings,
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

    /**
     * Get type school
     *
     * @return array
     */
    private function schoolType()
    {
        $schoolCat = \App\Models\School::where('id', '=', $this->school_id)->with('categories')->first();//categories
        $schoolCat = $schoolCat ? $schoolCat->categories : [];
        $cat = [];
        foreach ($schoolCat as $activeCat) {
            $cat[] = $activeCat->id;
        }
        $schoolLandings = \App\Models\SchoolLanding::where('school_id', '=', $this->school_id)->with('category')->whereIn('sport', $cat)->get();
        $landings = [];
        $landings[0] = 'Select Type';
        foreach ($schoolLandings as $landing) {
            $landings[$landing->id] = $landing->category->name;
        }
        return $landings;
    }

    /**
     * Get school's courses
     * @param $school_id
     * @return mixed
     */
    public function coursesList($school_id)
    {
        $this->crud->setRoute(config('base.route_prefix', 'admin') . '/school/' . $school_id . '/course');
        $this->crud->query->where('school_id', '=', $school_id);
        return parent::index();
    }

    /**
     * Create course
     *
     * @return mixed
     */
    public function create()
    {
        $this->crud->setRoute(config('base.route_prefix', 'admin') . '/school/' . $this->school_id . '/course');
        return parent::create();
    }

    /**
     * Store course
     *
     * @param $school_id
     * @param UpdateRequest $request
     * @return mixed
     */
    public function storeCourse($school_id, CreateRequest $request)
    {
        $this->crud->hasAccessOrFail('create');
        $request->merge(['school_id' => $school_id]);
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
        return redirect()->route('course-list', ['school_id' => $school_id]);
    }

    /**
     * Edit course
     *
     * @param $schoolId
     * @param $courseId
     * @return mixed
     */
    public function editCourse($schoolId, $courseId)
    {
        $this->crud->setRoute(config('base.route_prefix', 'admin') . '/school/' . $schoolId . '/course/' . $courseId . '/edit');
        $this->crud->hasAccessOrFail('update');
        // get the info for that entry
        $this->data['entry'] = $this->crud->getEntry($courseId);
        $this->data['crud'] = $this->crud;
        $this->data['fields'] = $this->crud->getUpdateFields($courseId);
        $this->data['id'] = $courseId;
        $this->data['back'] = config('base.route_prefix', 'admin') . '/school/' . $schoolId . '/course/';
        return view('crud::edit_landing', $this->data);
    }


}
