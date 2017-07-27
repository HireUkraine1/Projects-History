<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SchoolRequest as UpdateRequest;
use App\Models;
use CrudController;

class SchoolApplicationCrudController extends CrudController
{

    private $access = ['list', 'update', 'delete'];

    public function __construct()
    {
        parent::__construct();
        /*
        |--------------------------------------------------------------------------
        | BsportC CRUD INFORMATION
        |--------------------------------------------------------------------------
         */
        $this->crud->setModel("\App\Models\School");
        $this->crud->setRoute(config('base.route_prefix', 'admin') . '/application');
        $this->crud->setEntityNameStrings('application', 'applications');
        $this->crud->access = $this->access;
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
        $this->crud->addColumn([
            'label' => "Status",
            'type' => 'select',
            'name' => 'status_id',
            'entity' => 'status',
            'attribute' => 'name',
            'model' => "App\Models\SchoolStatus",
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
            'label' => 'Businesses',
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
        $this->crud->addField([ // SELECT
            'type' => 'select',
            'label' => 'Status',
            'name' => 'status_id',
            'entity' => 'status',
            'attribute' => 'name',
            'model' => "App\Models\SchoolStatus",
        ]);
    }

    /**
     * List of application
     *
     * @return mixed
     */
    public function index()
    {
        $this->crud->hasAccessOrFail('list');
        $this->data['crud'] = $this->crud;
        $this->data['title'] = ucfirst($this->crud->entity_name_plural);
        if (!$this->data['crud']->ajaxTable()) {
            $this->data['entries'] = Models\School::where('status_id', '<>', 3)->get();
        }
        return view('crud::list', $this->data);
    }

    /**
     * Update application
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
        if (is_null($request)) {
            $request = \Request::instance();
        }
        foreach ($request->input() as $key => $value) {
            if (empty($value) && $value !== '0') {
                $request->request->set($key, null);
            }
        }
        $item = $this->crud->update($request->get($this->crud->model->getKeyName()),
            $request->except('save_action', '_token', '_method'));
        $this->data['entry'] = $this->crud->entry = $item;
        if ($request->status_id == 3) {
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
            $item->date = date('Y-m-d');
            $item->save();
        }
        $this->setSaveAction();
        return $this->performSaveAction();
    }

    /**
     * Check country
     *
     * @param $request
     * @return mixed
     */
    private function checkCountry($request)
    {
        if ($request->country !== 'Australia') {
            $request->merge([
                'insurance' => '',
                'insurance_start_date' => '',
                'insurance_annual_revenue' => '',
                'insurance_incidents' => '',
            ]);
        }
        return $request;
    }
}
