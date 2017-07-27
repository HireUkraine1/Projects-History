<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Language;
use App\Http\Requests\RecipesRequest;
use App\Recipes;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;

class RecipesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        return view('admin.pages.recipes_index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.pages.recipes_create', ['locales' => Language::locales()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param RecipesRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(RecipesRequest $request)
    {
        Recipes::create($request->all());
        return redirect()->back();
    }


    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function anyData()
    {
        return Datatables::of(Recipes::select('*'))->make(true);
    }
}
