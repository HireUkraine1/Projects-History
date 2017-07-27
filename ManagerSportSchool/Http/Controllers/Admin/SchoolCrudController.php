<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SchoolRequest as StoreRequest;
use App\Http\Requests\SchoolRequest as UpdateRequest;
use App\Models;
use CrudController;

class SchoolCrudController extends CrudController
{

    private $idSchool;
    private $school_id;
    private $access = ['list', 'create', 'update', 'delete', 'landings-list', 'landing-edit', 'courses-list', 'document-list'];

    public function __construct()
    {
        parent::__construct();
        /*
        |--------------------------------------------------------------------------
        | BsportC CRUD INFORMATION
        |--------------------------------------------------------------------------
         */

        $this->middleware(function ($request, $next) {
            $this->idSchool = $this->school_id = \Route::getCurrentRoute()->hasParameter('school') ? \Route::getCurrentRoute()->parameter('school') : 0;
            return $next($request);
        });
        $this->crud->setModel("\App\Models\School");
        $this->crud->setRoute(config('base.route_prefix', 'admin') . '/school');
        $name = 'school';
        if ($this->idSchool) {
            $name = $this->crud->getEntry($this->idSchool)->name;
        }
        $this->crud->setEntityNameStrings($name, 'schools');
        $this->crud->access = $this->access;
        $this->crud->addButton('line', 'Landings list', 'getOpenButton', 'crud.buttons.school.landing_list_show');
        $this->crud->addButton('line', 'Courses list', 'getOpenButton', 'crud.buttons.school.courses_list_show');
        $this->crud->addButton('line', 'Document list', 'getOpenButton', 'crud.buttons.school.document_list_show');
        $this->crud->enableAjaxTable();
        /*
        |----------------
        | CRUD COLUMNS
        |----------------
         */
        $this->crud->addColumn([
            'name' => 'name',
            'label' => 'Business Name',
        ]);
        $this->crud->addColumn([
            'name' => 'trading_name',
            'label' => 'Trading Name',
        ]);
        $this->crud->addColumn([
            'name' => 'business_number',
            'label' => 'Business Number',
        ]);
        $this->crud->addColumn([
            'name' => 'phone',
            'label' => 'Phone',
        ]);
        $this->crud->addColumn([
            'name' => 'email',
            'label' => 'Email',
            'type' => 'email',
        ]);
        $this->crud->addColumn([
            'name' => 'website',
            'label' => 'Website',
        ]);
        /*
        |----------------
        | CRUD FIELDS
        |----------------
         */
//School bussiness data
        $this->crud->addField([
            'name' => 'name',
            'label' => 'Business Name*',
        ]);
        $this->crud->addField([
            'name' => 'trading_name',
            'label' => 'Trading Name',
        ]);
        $this->crud->addField([
            'name' => 'business_number',
            'label' => 'Business Number (ABN)*',
        ]);
        $this->crud->addField([
            'label' => 'School Businesses',
            'type' => 'checklist',
            'name' => 'businesses',
            'entity' => 'business',
            'attribute' => 'name',
            'model' => "\App\Models\BusinessStructure",
            'pivot' => true,
        ]);
//School owner contacts
        $this->crud->addField([
            'name' => 'contact_label',
            'type' => 'custom_html',
            'value' => '<br><h3>Contact</h3><hr>',
        ]);
        $this->crud->addField([
            'name' => 'first_name',
            'label' => 'First*',
        ]);
        $this->crud->addField([
            'name' => 'last_name',
            'label' => 'Last*',
        ]);
        $this->crud->addField([
            'name' => 'phone',
            'label' => 'Phone*',
        ]);
        $this->crud->addField([
            'name' => 'phone',
            'label' => 'Phone*',
        ]);
        $this->crud->addField([
            'name' => 'mobile',
            'label' => 'Mobile*',
        ]);
        $this->crud->addField([
            'name' => 'email',
            'label' => 'Email*',
        ]);
        $this->crud->addField([
            'name' => 'website',
            'label' => 'Website*',
        ]);
//School address
        $this->crud->addField([
            'name' => 'address_label',
            'type' => 'custom_html',
            'value' => '<br><h3>Address</h3><hr>',
        ]);
//School address
        $this->crud->addField([
            'label' => 'Autocomplite Bussines Address',
            'name' => 'autocomplite_bussines_address',
            'type' => 'autocomplite_bussines_address',
        ]);

        $this->crud->addField([
            'id' => 'country',
            'label' => 'Country*',
            'name' => 'country',
            'type' => 'addr_text',
        ]);
        $this->crud->addField([
            'id' => 'administrative_area_level_1',
            'label' => 'State / Province / Region*',
            'name' => 'state',
            'type' => 'addr_text',
        ]);
        $this->crud->addField([
            'id' => 'locality',
            'label' => 'City*',
            'name' => 'city',
            'type' => 'addr_text',
        ]);
        $this->crud->addField([
            'id' => 'address',
            'name' => 'street',
            'label' => 'Bussines Address*',
            'type' => 'addr_text',
        ]);
        $this->crud->addField([
            'id' => 'postal_code',
            'name' => 'postal',
            'label' => 'Postal/Zip Code (Bussines)*',
            'type' => 'addr_text',
        ]);

        $this->crud->addField([
            'name' => 'street_mailing',
            'label' => 'Trading address',
        ]);
        $this->crud->addField([
            'id' => 'postal_mailing',
            'name' => 'postal_mailing',
            'label' => 'Postal/Zip Code (Trading)',
            'type' => 'addr_text',
        ]);
// Insurance
        $this->crud->addField([
            'name' => 'Insurance_label',
            'type' => 'custom_html',
            'value' => '<br><h3>Insurance</h3><hr>',
            'class' => 'insurance_hide',
        ]);
        $this->crud->addField([
            'name' => 'insurance',
            'label' => '1. Do you require insurance?',
            'type' => 'radio',
            'options' => [
                1 => "Yes",
                0 => "No",
            ],
            'class' => 'insurance_hide',
        ]);
        $this->crud->addField([
            'name' => 'insurance_start_date',
            'label' => '2. If you require insurance, what is your anticipated start date?',
            'type' => 'date_picker',
            'class' => 'insurance_hide',
        ]);
        $this->crud->addField([
            'name' => 'insurance_annual_revenue',
            'label' => '3. What is your annual revenue?',
            'type' => 'radio',
            'options' => [
                75 => "0-$75K",
                150 => "$75-$150K",
                300 => "$150k-$300K",
                'over' => "over $300K",
            ],
            'class' => 'insurance_hide',
        ]);
        $this->crud->addField([
            'name' => 'insurance_incidents',
            'label' => 'Have you had any insurance claims in the past 24 months or workplace incidents? *',
            'type' => 'radio',
            'options' => [
                1 => "Yes",
                0 => "No",
            ],
            'class' => 'insurance_hide',
        ]);
        $this->crud->addField([
            'name' => 'insurance_hide_class',
            'type' => 'insurance',
            'country_field_name' => 'country',
        ]);
// Insurance end
        //Approve School
        $this->crud->addField([
            'name' => 'approve_label',
            'type' => 'custom_html',
            'value' => '<br><h2>Approve School Account</h2><hr>',
        ]);
        $this->crud->addField([
            'label' => 'Approve School Sports',
            'type' => 'checklist_sports',
            'name' => 'categories',
            'entity' => 'category',
            'attribute' => 'name',
            'model' => "\App\Models\Category",
            'pivot' => true,
            'hint' => 'Only for admin!',
        ]);
        $this->crud->addField([
            'label' => 'sport Section',
            'type' => 'checklist_sport_section',
            'name' => 'sportsections',
            'attribute' => 'name',
            'model' => "\App\Models\sportSection",
            'pivot' => true,
            'hint' => 'Only for admin!',
        ]);
        $this->crud->addField([
            'name' => 'date',
            'label' => 'Date of approve school',
            'type' => 'date_picker',
        ]);
//Approve School
        $this->crud->addField([
            'name' => 'approve',
            'label' => 'Enable',
            'type' => 'radio',
            'options' => [
                1 => "Yes",
                0 => "No",
            ],
        ]);
    }

    /**
     * Store school
     *
     * @param UpdateRequest $request
     * @return mixed
     */
    public function store(StoreRequest $request)
    {
        //make psw it is temporary decisions
        $request->merge(['password' => bcrypt(uniqid(false))]);
        if ($request->has('country')) {
            $request = $this->checkCountry($request);
        }
        $this->crud->hasAccessOrFail('create');
        // fallback to global request instance
        if (is_null($request)) {
            $request = \Request::instance();
        }
        // replace empty values with NULL, so that it will work with MySQL strict mode on
        foreach ($request->input() as $key => $value) {
            if (empty($value) && $value !== '0') {
                $request->request->set($key, null);
            }
        }
        // insert item in the db
        $item = $this->crud->create($request->except(['save_action', '_token', '_method']));
        $this->data['entry'] = $this->crud->entry = $item;
        $categories = $item->categories()->get();
        foreach ($categories as $category) {
            Models\SchoolLanding::create(['school_id' => $item->id, 'sport' => $category->id]);
        }
        $path = public_path('resourses/shools/' . $item->id);
        if (!\File::exists($path)) {
            \File::makeDirectory($path, 0775, true);
        }
        $this->setSaveAction();
        return $this->performSaveAction($item->getKey());
    }

    /**
     * Update School
     *
     * @param UpdateRequest $request
     * @return mixed
     */
    public function update(UpdateRequest $request)
    {
        if ($request->has('country')) {
            $request = $this->checkCountry($request);
        }
        $this->crud->hasAccessOrFail('update');
        // fallback to global request instance
        if (is_null($request)) {
            $request = \Request::instance();
        }
        // replace empty values with NULL, so that it will work with MySQL strict mode on
        foreach ($request->input() as $key => $value) {
            if (empty($value) && $value !== '0') {
                $request->request->set($key, null);
            }
        }
        // update the row in the db
        $item = $this->crud->update($request->get($this->crud->model->getKeyName()),
            $request->except('save_action', '_token', '_method'));
        $this->data['entry'] = $this->crud->entry = $item;
        $categories = $item->categories()->get();
        $usedSports = [];
        foreach ($categories as $category) {
            $usedSports[] = $category->id;
            if ($schoolLanding = Models\SchoolLanding::where('school_id', '=', $item->id)->where('sport', '=', $category->id)->first()) {
                $schoolLanding->active = 1;
                $schoolLanding->save();
            } else {
                Models\SchoolLanding::create(['school_id' => $item->id, 'sport' => $category->id]);
            }
        }
        $dissableSchoolLandings = Models\SchoolLanding::where('school_id', '=', $item->id)->whereNotIn('sport', $usedSports)->get();
        foreach ($dissableSchoolLandings as $dissableSchoolLanding) {
            $dissableSchoolLanding->active = 0;
            $dissableSchoolLanding->save();
        }
        $this->setSaveAction();
        return $this->performSaveAction();
    }
}
