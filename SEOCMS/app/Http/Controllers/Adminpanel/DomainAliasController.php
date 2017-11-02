<?php

namespace App\Http\Controllers\Adminpanel;

use App\Http\Requests\DomainAliasRequest;
use App\Models\DomainAlias;
use App\Support\EntityWorker\EntityWorker;

class DomainAliasController extends BaseController
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
    private $model = DomainAlias::class;

    /**
     * RobotsController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->entityWorker = new EntityWorker($this->model, $this->pivot);
    }

    /**
     * @param DomainAlias $domainAlias
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(\App\Models\DomainAlias $domainAlias)
    {
        $domainCollection = $domainAlias::get();

        return view('adminpanel.domain.index', compact('domainCollection'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('adminpanel.domain.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(DomainAliasRequest $request)
    {
        if ($request->get('master')) {
            DomainAlias::where('master', true)
                ->update(['master' => false]);
        }
        $domain = $this->entityWorker->createEntity($request->except(['_token']));
        $view = \View::make('adminpanel.domain.domain')->with('domain', $domain);
        $master = $domain->master;
        $data = [
            'result' => true,
            'id' => $domain->id,
            'view' => $view->render(),
            'master' => $master
        ];
        return json_encode($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(DomainAliasRequest $request, $id)
    {
        if ($request->get('master')) {
            DomainAlias::where('master', true)
                ->update(['master' => false]);
        }
        $domain = $this->entityWorker->updateEntity($id, $request->except(['_token']));
        $master = $domain->master;
        $data = [
            'result' => true,
            'master' => $master
        ];
        return json_encode($data);
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
        $data = [
            'result' => true
        ];
        return json_encode($data);
    }
}
