<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Worker;
use Illuminate\Http\Request;


class WorkerPageController extends Controller
{
    /**
     * List of workers
     *
     * @param $id
     * @return mixed
     */
    public function worker($id)
    {
        $ads = \App\Baner::where('city_id', $this->cityId)->where('show', 1)
            ->get()
            ->toArray();
        if (count($ads) < 1) {
            $ads = \App\Baner::where('city_id', 1)->where('show', 1)
                ->get()
                ->toArray();
        }
        $worker = Worker::where('id', 'LIKE', $id)->where('show', 1)
            ->first();
        $workerNewId = Worker::where('newId', 'LIKE', $id)->where('show', 1)
            ->first();
        if (!empty($worker)):
            $title = 'Ханди | ';
            $title .= $worker->cities[0]->city;
            $title .= ", " . $worker->first_name . ",";
            foreach ($worker->sub_categories as $sub_category):
                $title .= " $sub_category->name,";
            endforeach;
            if (count($worker->tags) > 0):
                foreach ($worker->tags as $tag):
                    $title .= " $tag->tag, ";
                endforeach;
            endif;
            $title .= $worker->user->tel;
        endif;
        if ($workerNewId instanceof Worker) {
            $video = [];
            $path = public_path($workerNewId->avatar_path);
            $filename = $path;
            if (count($workerNewId->videos) > 0) {
                $video = $workerNewId->videos;
            }
            return view('common.worker-page')->with('worker', $workerNewId)
                ->with('videos', $video)
                ->with('title', $title)
                ->with('ads', $ads);

        } elseif ($worker instanceof Worker) {
            if ($worker->newId) {
                return redirect('исполнитель/' . $worker->newId);
            }
            $video = [];
            $path = public_path($worker->avatar_path);
            $filename = $path;
            if (count($worker->videos) > 0) {
                $video = $worker->videos;
            }
            return view('common.worker-page')
                ->with('worker', $worker)
                ->with('videos', $video)
                ->with('title', $title)
                ->with('ads', $ads);
        } else {
            abort(404);
        }

    }
}
