<?php

namespace App\Http\Controllers\School\Auth;

use App\Http\Controllers\FrontendControllers\BaseController;
use App\Http\Requests\SchoolFrontRequest;
use App\Mail;
use App\Models;
use CrudPanel;
use Create;
use Illuminate\Http\Request;

class MembershipController extends BaseController
{
    private $pivot;

    public function __construct()
    {
        $this->pivot = ['categories', 'sportsections', 'businesses'];

        $this->crud = app()->make(CrudPanel::class);
        parent::__construct();
    }

    /**
     *  Filing an application from school for accreditation
     *
     **/
    public function application(Request $request)
    {
        $session = $request->session()->all();

        $categories = Models\Category::whereIn('id', [1, 2, 3])->get();
        $businessStructures = Models\BusinessStructure::orderBy('id', 'ASC')->get();
        $sportSections = Models\sportSection::all();
        return view('templates.membership_school', compact('sportSections', 'categories', 'businessStructures', 'session'));
    }

    /**
     *  Create bill
     *
     **/
    public function createBill(SchoolFrontRequest $request)
    {
        $inputs = $request->except(['_token']);
        foreach ($inputs as $key => $value) {
            session(['membership.' . $key => $value]);
        }
        return redirect()->route('checkout');
    }

    public function checkout(Request $request)
    {
        if (!session()->has('membership') || !is_array(session()->get('membership'))) {
            return redirect()->route('membership');
        }
        $total = 0;
        $sections = [];
        $membership = $inputs = session()->get('membership');
        $categories = $membership['categories'] ?? [];
        for ($i = 0; $i < count($categories); ++$i) {
            $category = $categories[$i];
            $price = ($i === 0) ? \Config::get('settings.main_cattegory_price') : \Config::get('settings.addition_cattegory_price');
            $sport = Models\Category::where('id', '=', $category)->first();
            $sections = ($category == 2 && is_array($membership['sportsections'])) ? Models\sportSection::whereIn('id', $membership['sportsections'])->get() : [];
            session(['membership.checkout_categories.' . $category => ['price' => $price, 'category' => $sport, 'sections' => $sections]]);
            $total += $price;
        }
        session(['membership.total' => $total]);
        $membership = session()->get('membership');
        $membership['inputs'] = $inputs;

        return view('templates.membership_school_checkout', $membership);
    }

    public function storeSchool(SchoolFrontRequest $request)
    {
        if ($request->insurance == 2) {
            $request->merge(['insurance' => 0]);
        }
        $this->checkCountry($request);
        $credentials = ['password' => uniqid(false), 'email' => $request->email];
        $request->merge(['password' => $credentials['password']]);
        $request->merge(['status_id' => 1]);
        $inputs = $request->except(['_token']);
        $school = $this->create($inputs);
        \Mail::to($request->email)->send(new Mail\ApplicationAccreditedSchool($credentials));
        return redirect("school/login");
    }

    private function checkCountry($request)
    {
        if ($request->country !== 'Australia') {
            $request->merge([
                'insurance' => null,
                'insurance_start_date' => null,
                'insurance_annual_revenue' => null,
                'insurance_incidents' => null,
            ]);
        }
        return $request;
    }

    private function create($data)
    {
        $insurance_start_date = (!empty($data['insurance_start_date'])) ? date('Y-m-d', \DateTime::createFromFormat('d/m/Y', $data['insurance_start_date'])->getTimestamp()) : null;
        $data['insurance_start_date'] = $insurance_start_date;
        $syncField = array_intersect_key($data, array_flip($this->pivot));
        $school = Models\School::create(array_except($data, $this->pivot));
        $this->syncPivot($school, $syncField);
        return $school;
    }

    private function syncPivot($model, $syncField)
    {
        foreach ($syncField as $key => $field) {
            $model->{$key}()->sync($field);
        }
    }
}
