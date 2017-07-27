<?php

namespace App\Http\Controllers\School;

use App\Http\Requests\LandingRequest as UpdateRequest;
use App\Http\Requests\SchoolGalleryRequest;
use App\Http\Traits\TraitCrudController;
use App\Models\LandingImages;
use App\Models\SchoolLanding;
use CrudController;
use GuzzleHttp\Psr7\Request;
use Illuminate\Http\Request as SchoolRequest;

class SchoolLandingCrudController extends CrudController
{

    use TraitCrudController;

    private $access = ['update', 'list', 'landing', 'updateLanding'];
    private $school;
    private $landing_id;

    public function __construct(SchoolRequest $school_request)
    {
        parent::__construct();

        /*
        |--------------------------------------------------------------------------
        | BsportC CRUD INFORMATION
        |--------------------------------------------------------------------------
         */
        $this->middleware(function ($request, $next) {
            $this->school = \Auth::guard('school')->user();
            if ($this->school->status_id !== 3) {
                return redirect('school/account');
            }
            $this->landing_id = \Route::getCurrentRoute()->parameter('landing');
            return $next($request);
        });
        $this->crud->setModel("\App\Models\SchoolLanding");
        $this->crud->setRoute('/school/landing');
        $this->crud->setEntityNameStrings('school landing', 'school landings');
        $this->crud->access = $this->access;

        /*
        |----------------
        | CRUD COLUMN
        |----------------
         */
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
    }

    public function index()
    {
        $schoolCat = \App\Models\School::where('id', '=', $this->school->id)->with('categories')->first();
        foreach ($schoolCat->categories as $activeCat) {
            $cat[] = $activeCat->id;
        }
        $schoolLandings = SchoolLanding::where('school_id', '=', $this->school->id)->whereIn('sport', $cat)->get();

        $this->crud->hasAccessOrFail('list');
        $this->data['crud'] = $this->crud;
        $this->data['title'] = ucfirst($this->crud->entity_name_plural);
        // get all entries if AJAX is not enabled
        if (!$this->data['crud']->ajaxTable()) {
            $this->data['entries'] = $schoolLandings;
        }
        return view('crud::list_school_landing', $this->data);
    }

    public function edit($id)
    {
        $this->crud->hasAccessOrFail('update');
        $landing = $this->crud->model->where('school_id', $this->school->id)->where('id', '=', $id)->first();
        if (!$landing) {
            abort("404");
        }
        $this->data['landing_id'] = \Route::getCurrentRoute()->parameter('landing');
        $this->data['gallery_upload_url'] = '/school/gallery/' . $this->data['landing_id'];
        return parent::edit($id);
    }

    public function updateLanding(UpdateRequest $request)
    {
        $landing = $this->crud->model->where('school_id', $this->school->id)->where('id', '=', $request->id)->first();
        if (!$landing) {
            abort("404");
        }
        $this->updateLocation($request->id, $request['address']);
        unset($request['address']);
        $request = $this->fieldArrayToJson($request, 'meet_team');
        return parent::updateCrud($request);
    }

    protected function updateLocation($landingId, $address)
    {
        // dd($landingId, $address);
        $locationExist = \App\Models\LandingLocation::where('landing_id', '=', $landingId)->get();
        $idLocations = (isset($address['old'])) ? array_keys($address['old']) : [];
        foreach ($locationExist as $location) {
            if (!in_array($location->id, $idLocations)) {
                $location->delete();
            }
        }
        foreach ($idLocations as $locationId) {
            \App\Models\LandingLocation::
            where('id', '=', $locationId)
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

    public function addGallery(SchoolGalleryRequest $request, $landing_id)
    {
        if ($request->hasFile('gallery')) {
            $gallery = $request->file('gallery');
            $fileName = md5($gallery->getClientOriginalName() . strtotime("now")) . '.' . $gallery->extension();
            $path = $gallery->storeAs('school_images/' . $this->school->id . '/landings/' . $this->landing_id, $fileName, 'uploads');
            $key = LandingImages::create(['landing_id' => $this->landing_id, 'image' => 'uploads/' . $path]);
            return response()->json(['caption' => 'uploads/' . $path, 'url' => '/school/gallery/' . $this->landing_id, 'key' => $key]);
        }
        if ($request->has('key')) {
            LandingImages::find($request->get('key'))->delete();
            return response()->json();
        }
        return response()->json();
    }
}
