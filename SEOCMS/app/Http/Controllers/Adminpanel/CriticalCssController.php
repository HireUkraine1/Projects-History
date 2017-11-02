<?php

namespace App\Http\Controllers\Adminpanel;

use App\Models\Pagesheet;
use App\Models\PageCompile;
use Illuminate\Http\Request;
use App\Jobs\CriticalCssJob;
use App\Models\PageCompileStatus;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Foundation\Bus\DispatchesJobs;

class CriticalCssController extends BaseController
{
    use DispatchesJobs;

    public function generate(Request $request)
    {
        $dimensions = '';
        $routes     = [];
        $processAll = filter_var($request->input('process'), FILTER_VALIDATE_BOOLEAN);

        if ($dimensionInput = trim($request->input('resolutions'))) {
            $dimensionsArray     = preg_split('/\r\n|[\r\n]/', $dimensionInput);
            $dimensionsJsonArray = [];

            foreach ($dimensionsArray as $dimension) {
                $dimensionNumbers      = explode('*', $dimension);
                $height                = array_shift($dimensionNumbers);
                $width                 = end($dimensionNumbers);
                $dimensionsJsonArray[] = "{height:$height,width:$width}";
            }

            $dimensions = implode(',', $dimensionsJsonArray);

            if ($dimensions) {
                $dimensions = "[$dimensions]";
            }
        }

        if ($routeInput = trim($request->input('routes'))) {
            $routes = preg_split('/\r\n|[\r\n]/', $routeInput);
        }

        if ($processAll) {
            $pages = Pagesheet::all();
        } else {
            $pages = Pagesheet::whereIn('url', $routes)->get();
        }

        $pages->each(function (Pagesheet $page) use ($dimensions) {
            $pageCompile = PageCompile::create([
                'page_id' => $page->id,
                'status'  => PageCompileStatus::WAITING
            ]);

            dispatch(new CriticalCssJob($page, $dimensions, $pageCompile));
        });

        return ['result' => true];
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of(PageCompile::with('page'))
                ->editColumn('created_at', function (PageCompile $compileLog) {
                    return $compileLog->created_at->format('d.m.Y H:i:s');
                })
                ->editColumn('status', function (PageCompile $compileLog) {

                    $status = $compileLog->status;

                    $message = trans("adminpanel/adminpanel.critical.statuses.{$status}");

                    if ($status === PageCompileStatus::ERROR) {
                        $message .= ': ' . $compileLog->error;
                    }

                    return $message;
                })->make(true);
        }

        return view('adminpanel.critical_css.index');
    }
}
