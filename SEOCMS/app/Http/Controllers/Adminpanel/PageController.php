<?php

namespace App\Http\Controllers\Adminpanel;

use App\Models\Pagesheet;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Requests\PageRequest;
use App\Support\EntityWorker\EntityWorker;

class PageController extends BaseController
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
    private $model = Pagesheet::class;

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
            return DataTables::of(Pagesheet::query())
                ->addColumn('action', function (Pagesheet $page) {
                    return view('adminpanel.page.action')->with(['page' => $page]);
                })
                ->addColumn('template', function (Pagesheet $page) {
                    return $page->template->name;
                })->make(true);
        }

        return view('adminpanel.page.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('adminpanel.page.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  PageRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(PageRequest $request)
    {
        $page = $this->entityWorker->createEntity($request->except(['_token']));

        return json_encode([
            'result' => true,
            'id'     => $page->id
        ]);
    }

    /**
     * Show the form for editing specific resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page = Pagesheet::findOrFail($id);

        return view('adminpanel.page.edit')->withPage($page);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  PageRequest $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(PageRequest $request, $id)
    {
        $page = Pagesheet::findOrFail($id);

        if ($page->sitemappriority == $request->input('sitemappriority')) {
            $request->merge(['sitemappriority' => 1]);
        }

        $page = $this->entityWorker->updateEntity($id, $request->except(['_token', '_method', 'create_template']));

        return json_encode([
            'result' => true,
            'id'     => $page->id
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
