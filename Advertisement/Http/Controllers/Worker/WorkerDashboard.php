<?php

namespace App\Http\Controllers\Worker;

use App\Http\Controllers\Controller;
use App\Http\Requests\EditWorker;
use Illuminate\Http\Request;


class WorkerDashboard extends Controller
{
    use \App\Http\Traits\WorkerTrait;

    /**
     * Create profile
     *
     * @return mixed
     */
    public function profile()
    {
        $user = \Sentinel::check();
        if ($user->worker) {
            $userWorker = \App\User::where('id', $user->id)->with('worker')
                ->with('worker.cities')
                ->with('worker.networks')
                ->with('worker.tags')
                ->with('worker.sub_categories')
                ->with('worker.videos')
                ->with('worker.images')
                ->first()
                ->toArray();
            $worker = $userWorker['worker'];
            if (!empty($worker['newId'])) {

                $workerUrl = $worker['newId'];
            } else {
                $workerUrl = $worker['id'];

            }
            $imagesAlbum = (count($worker['images']) > 0) ? $worker['images'] : [];
            $soc = (isset($worker['networks'])) ? $worker['networks'] : [];
            $workerNet = [];
            foreach ($soc as $net) {
                $workerNet[$net['type']] = $net['link'];
            };
            $city = (isset($worker['cities'][0])) ? $worker['cities'][0] : [];
            $tags = (isset($worker['tags'])) ? $worker['tags'] : [];
            $subCategory = (isset($worker['sub_categories'])) ? $worker['sub_categories'] : [];
            $video = (isset($worker['videos']) && !empty($worker['videos'])) ? $worker['videos'] : [];

            $popularCities = $this->popularCities();
            $categories = $this->getAllCat();
            $regions = $this->regions();
            $data = [
                'id' => $worker['id'],
                'first_name' => $worker['first_name'],
                'avatar_path' => $worker['avatar_path'],
                'description' => $worker['description'],
                'personal_site' => $worker['personal_site'],
                'show' => $worker['show'],
                'city' => $city,
                'tags' => $tags,
                'subCategory' => $subCategory,
                'email' => $userWorker['email'],
                'tel' => $userWorker['tel'],
                'pay' => $worker['pay'],
                'workerNet' => $workerNet,
                'video' => $video,
                'imagesAlbum' => $imagesAlbum,
                'popularCities' => $popularCities,
                'workerUrl' => $workerUrl,
            ];
            return view('common.profile')->with('data', $data)
                ->with('categories', $categories)
                ->with('regions', $regions);
        } else {
            return redirect('создание-кабинета');
        }


    }

    /**
     * Edit worker
     *
     * @param EditWorker $requests
     * @return mixed
     */
    public function editWorker(EditWorker $requests)
    {
        \Session::flash('message', ['false' => 'Ошибка, неправильно заполнены поля!']);
        $user = \Sentinel::check();

        if ($worker = $user->worker) {
            try {
                \DB::transaction(function () use ($worker, $user, $requests) {
                    $this->editWorkerName($worker, $requests['fname']);
                    $this->editWorkerDescription($worker, $requests['oinfo']);
                    $this->editWorkerVisible($worker, $requests['on']);
                    $this->editWorkerUrl($worker, $requests['url']);
                    $this->editWorkerAvatar($worker, $user->id, $requests->file('avatar'));
                    $this->editWorkerCity($worker, $requests['pcity']);
                    $this->editWorkerEmail($user, $requests['email']);
                    $this->editWorkerTag($worker, $requests['prof']);
                    $this->editWorkerSubCategories($worker, $requests->spec);
                    $this->editUserPwd($user, $requests['newpsw'], $requests['repitpsw']);
                    $socError = $this->editWorkerSocNet($worker, $requests);
                    if (count($socError) > 0) {
                        \DB::rollback();
                        return redirect()->back()->withErrors($socError);
                    }
                    $this->editWorkerYoutube($worker, $requests['video']);
                    $this->editWorkerSite($worker, $requests['site']);
                    if (!empty($requests->{'site'})) {
                        $site = filter_var(trim($requests->{'site'}), FILTER_VALIDATE_URL);
                        if ($site) {
                            if ($worker->personal_site != $site) {
                                $worker->personal_site = $site;
                                $worker->save();
                            }
                        } else {
                            return redirect()->back()->withErrors(['site' => 'Неправильная ссылка!']);
                        }
                    } else {
                        $worker->personal_site = $requests['site'];
                        $worker->save();
                    }
                    \Session::flash('message', ['true' => 'Обновление прошло успешно!']);
                });
                return redirect()->back();

            } catch (\Exception $e) {
                $exception = explode('|', $e->getMessage());
                \DB::rollback();
                if (isset($exception[1])) {
                    $key = $exception[0];
                    $value = $exception[1];
                    return redirect()->back()->withErrors([$key => $value]);
                } else {
                    \Session::flash('message', ['false' => $exception[0]]);
                    return redirect()->back();
                }
            }
        }
    }




}
