<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\User;
use App\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Sentinel;

class SuperAdminUser extends Controller
{
    use \App\Http\Traits\WorkerTrait;

    /**
     * Page of registered user
     *
     * @param Request $request
     * @return mixed
     */
    public function users(Request $request)
    {
        $query = '';
        if (isset($request->user) && !empty($request->user)) {
            $query = trim($request->user);
        }
        $users = User::where('email', 'LIKE', "%$query%")->orWhere('tel', 'LIKE', "%$query%")->paginate(10);
        return view('super-admin.users')->with('users', $users);
    }

    /**
     * Edit user
     *
     * @param $id
     * @return mixed
     */
    public function userEdit($id)
    {
        $user = User::where('id', $id)->first();
        if ($user->worker) {
            $userWorker = User::where('id', $id)
                ->with('worker')
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
            $city = (isset($worker['cities'][0])) ? $worker['cities'][0] : []; //выборка города
            $tags = (isset($worker['tags'])) ? $worker['tags'] : []; //профессий
            $subCategory = (isset($worker['sub_categories'])) ? $worker['sub_categories'] : []; //всех подкатегорий
            $video = (isset($worker['videos'])) ? $worker['videos'] : []; //видео
            $popularCities = $this->popularCities();
            $categories = $this->getAllCat();
            $bun = $user->inRole('bun');
            $data = [
                'user' => $userWorker['id'],
                'id' => $worker['id'],
                'first_name' => $worker['first_name'],
                'middle_name' => $worker['middle_name'],
                'sername' => $worker['sername'],
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
                'bun' => (int)$bun
            ];
            return view('super-admin.user-edit')
                ->with('data', $data)
                ->with('categories', $categories);
        } else {
            return view('super-admin.empty-user');
        }
    }

    /**
     * Change status worker
     * @return string
     */
    public function payWorker()
    {
        $id = Input::get('id');
        $worker = Worker::where('user_id', $id)->first();
        if ($worker instanceof Worker) {
            $worker->pay = (int)Input::get('pay');
            $worker->save();

            $path = public_path('workers-images/' . $id);
            if (!File::exists($path)) {
                File::makeDirectory($path, $mode = 0775, true);
            }
            return json_encode(['error' => 0]);
        } else {
            return json_encode(['error' => 1, 'message' => ['err' => [0 => 'Пользователь не найден!']]]);
        }

    }

}
