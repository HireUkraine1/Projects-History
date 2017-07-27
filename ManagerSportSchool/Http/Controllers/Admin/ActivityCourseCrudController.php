<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ActivityCourseRequest as CreateRequest;
use App\Http\Requests\ActivityCourseRequest as UpdateRequest;
use App\Http\Traits\TraitCrudController;
use CrudController;

class ActivityCourseCrudController extends CrudController
{
    use TraitCrudController;

    private $access = ['list', 'update', 'create', 'delete'];
    private $activity_id;
    private $course_id;

    public function __construct()
    {
        parent::__construct();

        $this->crud->setModel("\App\Models\Activity");
        $name = 'activity course';
        $list = 'activity courses';
        if ($this->activity_id) {
            $name = 'activity "' . $this->crud->getEntry($this->activity_id)->name . '" ' . 'course';
            $list = 'activity "' . $this->crud->getEntry($this->activity_id)->name . '" ' . 'courses';
        }
        $this->crud->setModel("\App\Models\ActivityCourses");
        $this->crud->setRoute(config('base.route_prefix', 'admin') . 'activity/course');
        $this->crud->setEntityNameStrings($name, $list);
        $this->crud->access = $this->access;

        /*
        |----------------
        | CRUD COLUMNS
        |----------------
         */
        $this->crud->addColumn([
            'name' => 'name',
            'label' => 'Course Name',
        ]);
        /*
        |----------------
        | CRUD FIELDS
        |----------------
         */
        $this->crud->addField([
            'name' => 'name',
            'label' => 'Course Name',
        ]);

        $this->crud->addField([
            'name' => 'description',
            'type' => 'textarea',
            'label' => 'Description',
        ]);
    }

    /**
     * Course List
     *
     * @param $activity_id
     * @return mixed
     */
    public function coursesList($activity_id)
    {
        $this->crud->setRoute(config('base.route_prefix', 'admin') . '/activity/' . $activity_id . '/course');
        $this->crud->query->where('activity_id', '=', $activity_id);
        return parent::index();
    }

    /**
     * Create Course
     * @return mixed
     */
    public function create()
    {
        $this->crud->setRoute(config('base.route_prefix', 'admin') . '/activity/' . $this->activity_id . '/course');
        return parent::create();
    }

    /**
     * Store Course
     *
     * @param $activity_id
     * @param UpdateRequest $request
     * @return mixed
     */
    public function storeCourse($activity_id, CreateRequest $request)
    {
        $this->crud->hasAccessOrFail('create');
        $request->merge(['activity_id' => $activity_id]);
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
        return redirect()->route('activity-course-list', ['activity_id' => $activity_id]);
    }

    /**
     * Edit course
     *
     * @param $activityId
     * @param $courseId
     * @return mixed
     */
    public function editCourse($activityId, $courseId)
    {
        $this->crud->setRoute(config('base.route_prefix', 'admin') . '/activity/' . $activityId . '/course/' . $courseId . '/edit');
        $this->crud->hasAccessOrFail('update');
        $this->data['entry'] = $this->crud->getEntry($courseId);
        $this->data['crud'] = $this->crud;
        $this->data['fields'] = $this->crud->getUpdateFields($courseId);
        $this->data['id'] = $courseId;
        $this->data['back'] = config('base.route_prefix', 'admin') . '/activity/' . $activityId . '/course/';
        return view('crud::edit_landing', $this->data);
    }

    /**
     * Update course
     *
     * @param UpdateRequest $request
     * @param $activity_id
     * @param $id
     * @return mixed
     */
    public function updateCourse(UpdateRequest $request, $activity_id, $id)
    {
        $this->crud->setRoute(config('base.route_prefix', 'admin') . '/activity/' . $activity_id . '/course');
        return parent::updateCrud($request);
    }

    /**
     * Delete course
     *
     * @param $activityId
     * @param $courseId
     * @return mixed
     */
    public function deleteCourse($activityId, $courseId)
    {
        $this->crud->hasAccessOrFail('delete');
        return $this->crud->delete($courseId);
    }
}
