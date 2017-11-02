<?php

namespace App\Http\Controllers\Adminpanel;

use App\Models\Template;
use Illuminate\Http\Request;
use App\Http\Requests\TemplateRequest;
use Yajra\DataTables\Facades\DataTables;
use App\Support\EntityWorker\EntityWorker;

class TemplateController extends BaseController
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
    private $model = Template::class;

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
            return DataTables::eloquent(Template::query())
                ->addColumn('action', function (Template $template) {
                    return view('adminpanel.template.action')->with(['template' => $template]);
                })->make(true);
        }

        return view('adminpanel.template.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('adminpanel.template.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  TemplateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(TemplateRequest $request)
    {
        $template = $this->entityWorker->createEntity($request->except(['_token']));

        return json_encode([
            'result' => true,
            'id'     => $template->id
        ]);
    }

    /**
     * Show the form for editing specific resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {
        $template = Template::findOrFail($id);
        $view     = view('adminpanel.template.edit')->withTemplate($template);

        if ($request->ajax()) {
            return $view;
        }

        return view('adminpanel.template.index')->with(['edit' => $view]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  TemplateRequest $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(TemplateRequest $request, $id)
    {
        $template = $this->entityWorker->updateEntity($id, $request->except(['_token', '_method']));

        return json_encode([
            'result' => true,
            'id'     => $template->id
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

    public function show($id){
        return Template::findOrFail($id);
    }
}
