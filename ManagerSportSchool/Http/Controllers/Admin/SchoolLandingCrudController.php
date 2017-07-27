<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SchoolGalleryRequest;
use App\Http\Requests\SchoolLandingAjaxRequest as CreateAjaxRequest;
use App\Http\Requests\SchoolLandingRequest as UpdateRequest;
use App\Http\Traits\TraitCrudController;
use App\Models\LandingImages;
use App\Models\PreviewSchoolLanding;
use App\Models\SchoolLanding;
use CrudController;
use GuzzleHttp\Psr7\Request;
use Illuminate\Http\Request as SchoolRequest;

class SchoolLandingCrudController extends CrudController
{
    use TraitCrudController;

    private $access = ['update', 'list', 'landing', 'updateLanding'];
    private $schoolLandings;
    private $school_id;
    private $landing_id;
    private $schoolCat;
    private $school;

    public function __construct(SchoolRequest $school_request)
    {
        parent::__construct();
        /*
        |--------------------------------------------------------------------------
        | BsportC CRUD INFORMATION
        |--------------------------------------------------------------------------
         */
        $this->school_id = \Route::getCurrentRoute()->hasParameter('school_id') ? \Route::getCurrentRoute()->parameter('school_id') : 0;
        $this->landing_id = \Route::getCurrentRoute()->hasParameter('landing_id') ? \Route::getCurrentRoute()->parameter('landing_id') : 0;
        $this->schoolCat = \App\Models\School::where('id', '=', $this->school_id)->with('categories')->first();
        $cat = [];
        foreach ($this->schoolCat->categories ?? [] as $activeCat) {
            $cat[] = $activeCat->id;
        }
        $this->crud->setModel("\App\Models\School");
        $name = 'school landing';
        $list = 'school landings';
        if ($this->school_id) {
            $name = 'school "' . $this->crud->getEntry($this->school_id)->name . '" ' . 'landing';
            $list = 'school "' . $this->crud->getEntry($this->school_id)->name . '" ' . 'landings';
        }

        $this->schoolLandings = SchoolLanding::where('school_id', '=', $this->school_id)->whereIn('sport', $cat)->get();
        $this->crud->setModel("\App\Models\SchoolLanding");
        $this->crud->setRoute(config('base.route_prefix', 'admin') . '/school/' . $this->school_id . '/landing');
        $this->crud->setEntityNameStrings($name, $list);
        $this->crud->access = $this->access;
        $this->crud->addColumn([
            // 1-n relationship
            'label' => 'School name',
            'name' => 'school_id',
            'type' => 'select',
            'attribute' => 'name',
            'entity' => 'school',
            'model' => "\App\Models\Category",
        ]);
        $this->crud->addColumn([
            // 1-n relationship
            'label' => 'Selected Sport',
            'name' => 'sport',
            'type' => 'select',
            'attribute' => 'name',
            'entity' => 'category',
            'model' => "\App\Models\Category",
        ]);
        $this->crud->addColumn([
            // 1-n relationship
            'label' => 'Status',
            'name' => 'active',
            'type' => 'boolean',
            // optionally override the Yes/No texts
            'options' => [0 => 'Inactive', 1 => 'Active'],
        ]);
        /*
        |----------------
        | CRUD FIELDS
        |----------------
         */
        $this->crud->addField([
            'label' => 'Landing Sport',
            'name' => 'sport',
            'type' => 'radio_relations',
            'field_key' => 'school_id',
            'field_value' => $this->school_id,
            'entity' => 'category',
            'model' => "\App\Models\SchoolCategory",
            'model_second' => "\App\Models\School",
        ]);
        $this->crud->addField([
            'label' => 'Choice Landing Activities',
            'name' => 'activities',
            'type' => 'activities',
            'entity' => 'category',
            'model' => "\App\Models\Category",
            'landing_id' => $this->landing_id,
            'attribute' => "name",
            'pivot' => true,
        ]);
        $this->crud->addField([
            'label' => 'Choice Templates Course Of Activity',
            'name' => 'templates',
            'type' => 'activity_course_templates',
            'entity' => 'activity',
            'model' => "\App\Models\Activity",
            'landing_id' => $this->landing_id,
            'attribute' => "name",
            'pivot' => true,
        ]);
        $this->crud->addField([
            'label' => "School Banner",
            'name' => "banner",
            'type' => 'image',
            'upload' => true,
            'crop' => true,
        ]);
        $this->crud->addField([
            'label' => "School Preview Banner",
            'name' => "thumbnail",
            'type' => 'image',
            'upload' => true,
            'crop' => true,
        ]);
        $this->crud->addField([
            'name' => 'about_us',
            'label' => 'About Us',
            'type' => 'ckeditor',
        ]);
        $this->crud->addField([
            'name' => 'meet_team',
            'label' => 'Meet the Team',
            'type' => 'team',
        ]);
        $this->crud->addField([
            'label' => 'Services Overview',
            'name' => 'service_overview',
            'type' => 'ckeditor',
        ]);
        $this->crud->addField([
            'name' => 'features',
            'label' => 'School Features',
            'type' => 'ckeditor',
        ]);
        $this->crud->addField([
            'name' => 'location_features',
            'label' => 'Location Features',
            'type' => 'ckeditor',
        ]);
        $this->crud->addField([
            'name' => 'tourist_attributes',
            'label' => 'Tourist Attributes',
            'type' => 'ckeditor',
        ]);
        $this->crud->addField([
            'name' => 'accomodations',
            'label' => 'Accomodations',
            'type' => 'ckeditor',
        ]);
        $this->crud->addField([
            'name' => 'gallery_label',
            'type' => 'custom_html',
            'value' => '<br><h2>Gallery</h2><hr>',
        ]);
        $this->crud->addField([
            'name' => 'galleries',
            'label' => 'Gallery',
            'type' => 'gallery',
            'entity' => 'galleries',
            'model' => "\App\Models\LandingImages",
            'landing_id' => $this->landing_id,
            'attribute' => 'image',
        ]);
        $this->crud->addField([
            'name' => 'location',
            'type' => 'multi_location',
            'landing_id' => $this->landing_id,
            'model' => "\App\Models\LandingLocation",
        ]);
        $this->crud->addField([
            'name' => 'approve_label',
            'type' => 'custom_html',
            'value' => '<br><h2>Approve</h2><hr>',
        ]);
        $this->crud->addField([
            'name' => 'active',
            'label' => 'Show Landing Page?',
            'type' => 'radio',
            'options' => [
                0 => "No",
                1 => "Yes",
            ],
        ]);
    }

    /**
     * School's landings
     *
     * @return mixed
     */
    public function index()
    {
        $this->crud->hasAccessOrFail('list');
        $this->data['crud'] = $this->crud;
        $this->data['title'] = ucfirst($this->crud->entity_name_plural);
        if (!$this->data['crud']->ajaxTable()) {
            $this->data['entries'] = $this->schoolLandings;
        }
        return view('crud::list_school_landing', $this->data);
    }

    /**
     * Update landing
     *
     * @param UpdateRequest $request
     * @param $school_id
     * @param $id
     * @return mixed
     */
    public function updateLanding(UpdateRequest $request, $school_id, $id)
    {
        $this->updateLocation($id, $request['address']);
        unset($request['address']);
        $this->crud->setRoute(config('base.route_prefix', 'admin') . '/school/' . $school_id . '/landing');
        $request = $this->fieldArrayToJson($request, 'meet_team');
        return parent::updateCrud($request);
    }

    /**
     * Update Landing's location
     *
     * @param $landingId
     * @param $address
     */
    protected function updateLocation($landingId, $address)
    {
        $locationExist = \App\Models\LandingLocation::where('landing_id', '=', $landingId)->get();
        $idLocations = (isset($address['old'])) ? array_keys($address['old']) : [];
        foreach ($locationExist as $location) {
            if (!in_array($location->id, $idLocations)) {
                $location->delete();
            }
        }

        foreach ($idLocations as $locationId) {
            \App\Models\LandingLocation::where('id', '=', $locationId)
                ->update([
                    'alias' => $address['old'][$locationId]['alias'],
                    'country' => $address['old'][$locationId]['country'],
                    'address' => $address['old'][$locationId]['address'],
                    'latitude' => $address['old'][$locationId]['latitude'],
                    'longitude' => $address['old'][$locationId]['longitude'],
                ]);
        }

        $newLocations = (isset($address['new'])) ? $address['new'] : [];
        foreach ($newLocations as $newLocation) {
            \App\Models\LandingLocation::create([
                'landing_id' => $landingId,
                'alias' => $newLocation['alias'],
                'country' => $newLocation['country'],
                'address' => $newLocation['address'],
                'latitude' => $newLocation['latitude'],
                'longitude' => $newLocation['longitude'],
            ]);
        }
    }

    /**
     * Create Landing
     *
     * @param CreateRequest $request
     * @param $school_id
     * @return mixed
     */
    public function storeLanding(CreateRequest $request, $school_id)
    {
        $this->crud->hasAccessOrFail('create');
        $request->merge(['school_id' => $school_id]);
        $request = $this->fieldArrayToJson($request, 'meet_team');
        $address = $request['address'];
        unset($request['address']);
        if (is_null($request)) {
            $request = \Request::instance();
        }
        foreach ($request->input() as $key => $value) {
            if (empty($value) && $value !== '0') {
                $request->request->set($key, null);
            }
        }
        $item = $this->crud->create($request->except(['save_action', '_token', '_method']));
        $this->updateLocation($item->id, $address);
        $this->data['entry'] = $this->crud->entry = $item;
        $this->setSaveAction();
        return redirect()->route('landing-list', ['school_id' => $school_id]);
    }

    /**
     * Add gallery
     *
     * @param SchoolGalleryRequest $request
     * @param $school_id
     * @param $landing_id
     * @return mixed
     */
    public function addGallery(SchoolGalleryRequest $request, $school_id, $landing_id)
    {
        if ($request->hasFile('gallery')) {
            $gallery = $request->file('gallery');
            $fileName = md5($gallery->getClientOriginalName() . strtotime("now")) . '.' . $gallery->extension();
            $path = $gallery->storeAs('school_images/' . $school_id . '/landings/' . $landing_id, $fileName, 'uploads');
            $key = LandingImages::create(['landing_id' => $landing_id, 'image' => 'uploads/' . $path]);
            return response()->json(['caption' => 'uploads/' . $path, 'url' => '/' . config('base.route_prefix', 'admin') . '/school/' . $school_id . '/gallery/' . $landing_id, 'key' => $key]);
        }
        if ($request->has('key')) {
            LandingImages::find($request->get('key'))->delete();
            return response()->json();
        }
        return response()->json();
    }

}
