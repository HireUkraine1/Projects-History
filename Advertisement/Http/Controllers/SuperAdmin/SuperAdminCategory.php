<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Category;
use App\Helper\CreateSlug;
use App\Http\Controllers\Controller;
use App\SubCategory;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Request;

class SuperAdminCategory extends Controller
{
    /**
     * List of category
     *
     * @return mixed
     */
    public function categories()
    {
        $categories = Category::all();
        return view('super-admin.categories')->with('categories', $categories);
    }

    /**
     * Create category by ajax
     * @return string
     */
    public function ajaxCreateNewCategory()
    {
        $nameCat = Input::get('nameCat');
        if (Request::ajax()) {
            $valid = Validator::make(Input::all(), [
                'nameCat' => 'required|max:120|unique:categories,name',
            ]);

            if ($valid->fails()) {
                $data = ['error' => 1, 'message' => $valid->errors()];
            } else {
                $category = new Category;
                $category->name = Input::get('nameCat');
                $category->slug = CreateSlug::ruSlug(Input::get('nameCat'));
                $category->save();
                $message = $category->id;
                $data = ['error' => 0, 'category' => $message];
            }
            return json_encode($data);
        }

    }

    /**
     * Create new sub category
     *
     * @return string
     */
    public function ajaxCreateNewSubCategory()
    {
        if (Request::ajax()) {
            $valid = Validator::make(Input::all(), [
                'nameCat' => 'required|max:120',
            ]);
            $subCategory = SubCategory::where('category_id', Input::get('categotyid'))->where('name', Input::get('nameCat'))->first();
            if (!$subCategory instanceof SubCategory) {
                if ($valid->fails()) {
                    $data = ['error' => 1, 'message' => $valid->errors()];
                } else {
                    $category = new SubCategory;
                    $category->name = Input::get('nameCat');
                    $category->category_id = Input::get('categotyid');
                    $category->slug = CreateSlug::ruSlug(Input::get('nameCat'));
                    $category->save();
                    $data = ['error' => 0, 'category' => $category->category_id];
                }
            } else {
                $data = ['error' => 1, 'message' => ['err' => [0 => 'Подраздел уже существует!']]];
            }
            return json_encode($data);
        }
    }

    /**
     * Switch category
     */
    public function ajaxSwichSubCategory()
    {
        if (Request::ajax()) {
            $subCategory = SubCategory::where('id', Input::get('subCategotyId'))->first();
            if ($subCategory instanceof SubCategory) {
                $subCategory->show = (int)Input::get('subCategotyChecked');
                $subCategory->save();
                return json_encode(['error' => 0]);
            } else {
                return json_encode(['error' => 1, 'message' => ['err' => [0 => 'Подкатегории не существует!']]]);
            }
        }
    }

    /**
     * Delete sub category
     *
     * @return string
     */
    public function ajaxDeleteSubCategory()
    {
        if (Request::ajax()) {
            $subCategory = SubCategory::where('id', Input::get('subCategotyId'))->first();
            if ($subCategory instanceof SubCategory) {
                $subCategory->delete();
                $data = ['error' => 0];
            } else {
                $data = ['error' => 1, 'message' => ['err' => [0 => 'Подкатегории не существует!']]];
            }
            return json_encode($data);
        }
    }

    /**
     * Delete category
     *
     * @return string
     */
    public function ajaxDeleteCategory()
    {
        if (Request::ajax()) {
            $category = Category::where('id', Input::get('categotyId'))->first();
            if ($category instanceof Category) {
                $category->delete();
                $data = ['error' => 0];
            } else {
                $data = ['error' => 1, 'message' => ['err' => [0 => 'Категории не существует!']]];
            }
            return json_encode($data);
        }

    }

    /**
     * Edit sub-category
     *
     * @return string
     */
    public function ajaxEditSubCategory()
    {
        if (Request::ajax()) {
            $valid = Validator::make(Input::all(), [
                'nameCat' => 'required|max:120',
            ]);
            $subCategory = SubCategory::where('name', Input::get('nameCat'))->where('id', Input::get('idSubCategory'))->first();
            if ($subCategory instanceof SubCategory) {
                $data = ['error' => 0];
            } else {
                $subCategoryName = SubCategory::where('name', Input::get('nameCat'))->where('category_id', Input::get('catid'))->first();
                if ($subCategory instanceof SubCategory) {
                    $data = ['error' => 1, 'message' => ['err' => [0 => 'Подраздел уже существует!', 1 => $subCategoryName->id]]];
                } else {
                    if ($valid->fails()) {
                        $data = ['error' => 1, 'message' => $valid->errors()];
                    } else {
                        $editSubCategoryName = SubCategory::where('id', Input::get('idSubCategory'))->first();
                        if (!$editSubCategoryName instanceof SubCategory) {
                            $data = ['error' => 1, 'message' => ['err' => [0 => 'Подраздел не существует!']]];
                        } else {
                            $editSubCategoryName->name = Input::get('nameCat');
                            $editSubCategoryName->slug = CreateSlug::ruSlug(Input::get('nameCat'));
                            $editSubCategoryName->save();
                            $data = ['error' => 0];
                        }
                    }
                }
            }
            return json_encode($data);
        }
    }

    /**
     * Switch category
     *
     * @return string
     */
    public function ajaxSwichCategory()
    {
        if (Request::ajax()) {
            $category = Category::where('id', Input::get('categotyId'))->first();
            if ($category instanceof Category) {
                $category->show = (int)Input::get('categotyChecked');
                $category->save();
                $data = ['error' => 0];
            } else {
                $data = ['error' => 1, 'message' => ['err' => [0 => 'Категории не существует!']]];
            }
            return json_encode($data);
        }
    }

    /**
     * Category editor
     * @return string
     */
    public function ajaxEditCategory()
    {
        if (Request::ajax()) {
            $valid = Validator::make(Input::all(), [
                'nameCat' => 'required|max:120',
            ]);
            $subCategory = Category::where('name', Input::get('nameCat'))->where('id', Input::get('catid'))->first();
            if ($subCategory instanceof SubCategory) {
                $data = ['error' => 0];
            } else {
                if ($valid->fails()) {
                    $data = ['error' => 1, 'message' => $valid->errors()];
                } else {
                    $checkCategoryName = Category::where('name', Input::get('nameCat'))->first();
                    if ($checkCategoryName instanceof SubCategory) {
                        $data = ['error' => 1, 'message' => ['err' => [0 => 'Подраздел уже существует!']]];
                    } else {
                        $editCategoryName = Category::where('id', Input::get('catid'))->first();
                        $editCategoryName->name = Input::get('nameCat');
                        $editCategoryName->slug = CreateSlug::ruSlug(Input::get('nameCat'));
                        $editCategoryName->save();
                        $data = ['error' => 0];
                    }
                }
            }
            return json_encode($data);
        }
    }
}
