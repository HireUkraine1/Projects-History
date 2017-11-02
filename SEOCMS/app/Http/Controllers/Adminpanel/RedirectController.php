<?php

namespace App\Http\Controllers\Adminpanel;

use App\Models\Redirect;
use Illuminate\Http\Request;
use App\Http\Requests\RedirectRequest;
use Yajra\DataTables\Facades\DataTables;
use App\Support\EntityWorker\EntityWorker;

class RedirectController extends BaseController
{
    /**
     * @var EntityWorker
     */
    protected $entityWorker;

    /**
     * @var array
     */
    private $pivot = [];

    /**
     * @var string
     */
    private $model = Redirect::class;

    /**
     * RobotsController constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->entityWorker = new EntityWorker($this->model, $this->pivot);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of(Redirect::query())
                ->addColumn('action', function (Redirect $redirect) {
                    return view('adminpanel.redirect.action')->with(['redirect' => $redirect]);
                })->make(true);
        }

        return view('adminpanel.redirect.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('adminpanel.redirect.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  RedirectRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(RedirectRequest $request)
    {
        $redirect = $this->entityWorker->createEntity($request->except(['_token']));

        return json_encode([
            'result' => true,
            'id'     => $redirect->id
        ]);
    }

    /**
     * Show the form for editing specific resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $redirect = Redirect::findOrFail($id);

        return view('adminpanel.redirect.edit')->withRedirect($redirect);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  RedirectRequest $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(RedirectRequest $request, $id)
    {
        $redirect = $this->entityWorker->updateEntity($id, $request->except(['_token', '_method']));

        return json_encode([
            'result' => true,
            'id'     => $redirect->id
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->entityWorker->deleteEntity($id);

        return json_encode(['result' => true]);
    }
}
