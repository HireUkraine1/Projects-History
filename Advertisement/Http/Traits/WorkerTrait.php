<?php

namespace App\Http\Traits;

use App\Http\Controllers\AcResizeImage;
use App\Image as WorkerImage;
use App\Network;
use App\Tag;
use App\User;
use App\Video;
use App\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Image;
use Sentinel;


trait WorkerTrait
{

    /**
     * Create new user image
     *
     * @param Request $request
     * @return string
     */
    public function newImage(Request $request)
    {
        $user = Sentinel::check();
        $msg = ['error' => 1, 'msg' => 'Доступно только в платной версии'];
        if ($worker = $user->worker) {
            $countImag = ($worker->pay) ? 100 : 10;
            if (Input::file('img') != null) {
                $imgName = Input::file('img')->getClientOriginalName();
                $imgPath = 'workers-images/' . $user->id . '/' . $imgName;
                if (strlen($imgPath) > 119) {
                    $msg = ['error' => 1, 'msg' => 'Слишком длинное название изображения'];
                } else {
                    $checkImage = WorkerImage::where('worker_id', $worker->id)->where('route', '=', $imgPath)->first();

                    if ($checkImage instanceof WorkerImage) {
                        $msg = ['error' => 1, 'msg' => 'Изображение ' . $imgName . ', уже существует!'];
                    } else {
                        if ((int)$worker->images()->count() < $countImag) {
                            $type = Input::file('img')->extension();
                            if ($type === 'jpeg' || $type === 'jpg') {
                                $newImg = new WorkerImage;
                                $newImg->worker_id = $worker->id;
                                $newImg->route = $imgPath;
                                $newImg->save();
                                $path = public_path('workers-images/' . $user->id);
                                if (!File::exists($path)) {
                                    File::makeDirectory($path, $mode = 0775, true);
                                }
                                Image::make($request->file('img'))->save($imgPath);
                                $msg = ['error' => 0, 'id' => $newImg->id, 'route' => $newImg->route];
                            } else {
                                $msg = ['error' => 1, 'msg' => 'Неправильный формат изображения!'];
                            }
                        } else {
                            $msg = ['error' => 1, 'msg' => 'Мах количество изображений ' . $countImag . 'шт.'];
                        }
                    }
                }
            } else {
                $msg = ['error' => 1, 'msg' => 'Добавьте избражение!'];
            }
        }
        return json_encode($msg);
    }

    /**
     * Delete user image
     *
     * @param Request $request
     */
    public function delImage(Request $request)
    {
        $user = Sentinel::check();
        if ($worker = $user->worker) {
            $imgId = $request->get('id');
            $image = WorkerImage::where('id', $imgId)->first();

            if ($image instanceof WorkerImage) {
                if ($image->worker_id == $worker->id) {
                    $path = public_path($image->route);
                    File::delete($path);
                    $image->delete();
                }
            }
            echo $image->worker_id;
            echo $worker->id;
        }

    }

    /**
     * Edit worker's name
     * @param Worker $worker
     * @param $fname
     */
    protected function editWorkerName(Worker $worker, $fname)
    {
        $worker->first_name = $fname;
        $worker->save();
    }

    /**
     * Edit worker's description
     *
     * @param Worker $worker
     * @param $description
     */
    protected function editWorkerDescription(Worker $worker, $description)
    {
        $worker->description = $description;
        $worker->save();
    }

    /**
     * Edit worker's visibility
     *
     * @param Worker $worker
     * @param $show
     */
    protected function editWorkerVisible(Worker $worker, $show)
    {
        $worker->show = $show;
        $worker->save();
    }

    /**
     * Edit worker's Url
     *
     * @param Worker $worker
     * @param $newUrl
     * @throws \Exception
     */
    protected function editWorkerUrl(Worker $worker, $newUrl)
    {
        if ($newUrl) {
            if (is_numeric($newUrl)) {
                if ($newUrl != $worker->id) {
                    throw new \Exception('url|Неправильная ссылка, ссылка не может быть числом!');
                } else {
                    if ($worker->newId != $newUrl) {
                        $worker->newId = $newUrl;
                        $worker->save();
                    }
                }
            } else {
                $str = strtolower($newUrl);
                if (preg_match('/^([а-яА-ЯЁёa-zA-Z0-9_-]+)$/u', $str) != 0) {
                    if ($str != $worker->newId) {
                        $WorkerNewId = Worker::where('newId', $str)->first();
                        if ($WorkerNewId instanceof Worker) {
                            throw new \Exception('url|Url уже занят!');
                        } else {
                            $worker->newId = $str;
                            $worker->save();
                        }
                    }
                } else {
                    throw new \Exception('url|Есть недопустимые символы');
                }
            }
        } else {
            $worker->newId = 0;
            $worker->save();
        }
    }

    /**
     * Edit worker avatar
     *
     * @param Worker $worker
     * @param $userId
     * @param bool $avatar
     */
    protected function editWorkerAvatar(Worker $worker, $userId, $avatar = false)
    {
        if ($avatar) {
            $path = public_path('avatars/' . $userId);
            if (!File::exists($path)) {
                File::makeDirectory($path, 0775, true);
            }
            File::delete($path . '/avatar.jpg');
            $imgNew = Image::make($avatar)->save($path . '/temp.jpg');
            $img = new AcResizeImage($path . '/temp.jpg');
            if ($imgNew->height() != 120 || $imgNew->width() != 120) {
                $img->resize(120, 120);
            }
            $img->save($path . '/', 'avatar', 'jpg', true, 100);
            $worker->avatar_path = '/avatars/' . $userId . '/avatar.jpg';
            File::delete($path . '/temp.jpg');
            $worker->save();
        }
    }

    /**
     * Edit worker's city
     * @param Worker $worker
     * @param $pcity
     */
    protected function editWorkerCity(Worker $worker, $pcity)
    {
        $workerCity = $worker->cities->toArray();

        if (isset($workerCity[0]['id']) && $workerCity[0]['id'] != $pcity) {
            $worker->cities()->detach([$workerCity[0]['id']]);
            $worker->cities()->attach([$pcity]);
        } elseif (isset($workerCity[0]['id']) && $workerCity[0]['id'] == $pcity) {
            //do nothing;
        } else {
            $worker->cities()->attach([$pcity]);
        }
    }

    /**
     * Edit Email
     *
     * @param User $user
     * @param $email
     * @throws \Exception
     */
    protected function editWorkerEmail(User $user, $email)
    {
        if ($email != $user->email) {
            $checkUsersEmail = User::where('email', $email)->first();
            if ($checkUsersEmail instanceof User) {
                throw new \Exception('email|Email занят другим пользователем');
            } else {
                $user->email = $email;
                $user->save();
            }
        }
    }

    /**
     * Edit tags
     *
     * @param Worker $worker
     * @param $prof
     */
    protected function editWorkerTag(Worker $worker, $prof)
    {
        if (!is_array($prof)) {
            $prof = [];
        }
        $tags = Tag::select('tag')->where('worker_id', $worker->id)
            ->get()
            ->map(function ($worker) {
                return $worker->tag;
            })
            ->toArray();
        $difTag = array_diff($prof, $tags);
        foreach ($tags as $tag) {
            if (!in_array($tag, $prof)) {
                Tag::where('worker_id', $worker->id)->where('tag', $tag)->delete();
            }
        }

        foreach ($difTag as $tagNew) {
            $checkTag = Tag::where('tag', $tagNew)->where('worker_id', $worker->id)->first();
            if (!$checkTag instanceof Tag) {
                $tag = new Tag;
                $tag->worker_id = $worker->id;
                $tag->tag = $tagNew;
                $tag->save();
            }
        }
    }

    /**
     * Edit worker's sub-category
     *
     * @param Worker $worker
     * @param $specReq
     * @throws \Exception
     */
    protected function editWorkerSubCategories(Worker $worker, $specReq)
    {
        $subCategories = $worker->sub_categories
            ->map(function ($sub) {
                return $sub->id;
            })
            ->toArray();
        $spec = explode(',', $specReq);
        if (count($spec) > 5) {
            throw new \Exception('spec|Не больше 5 подкатегорий!');
        }
        $dif = array_diff($spec, $subCategories);
        foreach ($subCategories as $subExist) {
            if (!in_array($subExist, $spec)) {
                $worker->sub_categories()->detach([$subExist]);
            }
        }
        foreach ($dif as $subNew) {
            $worker->sub_categories()->attach([$subNew]);
        }
    }

    /** Edit worker soc. net.
     *
     * @param Worker $worker
     * @param $requests
     * @return array
     */
    protected function editWorkerSocNet(Worker $worker, $requests)
    {

        $socError = [];
        $arraySocNetwork = [
            'soc-vk' => '/:\/\/vk.com/',
            'soc-od' => '/:\/\/ok.ru/',
            'soc-fb' => '/:\/\/www.facebook.com/',
            'soc-in' => '/:\/\/www.linkedin.com/',
            'soc-mm' => '/:\/\/my.mail.ru/',
            'soc-gp' => '/:\/\/plus.google.com/'
        ];
        foreach ($arraySocNetwork as $type => $key) {
            $error = $this->socialNetwork($requests->{$type}, $type, $worker->id, $key);
            if ($error) {
                $socError[$type] = 'Неправильная ссылка!';
            }
        }
        return $socError;
    }

}
