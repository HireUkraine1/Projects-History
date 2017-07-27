<?php

namespace App\Http\Controllers\Admin\Services;

use App\Http\Controllers\Admin\CommonController;
use App\Http\Requests;
use App\Model;
use Illuminate\Http\Request;

class OptionsController extends CommonController
{
    public function index()
    {
        if ($this->admin->role_id == 3) {
            abort(403);
        }
        $optionsType = Model\OptionType::with('option')->get()->toArray();
        return view('admin.services.options.index')->with('optionsType', $optionsType);
    }

    public function edit($id)
    {
        if ($this->admin->role_id == 3) {
            abort(403);
        }
        $optionType = Model\OptionType::where('id', '=', $id)->with('option')->first();
        if (!$optionType instanceof Model\OptionType) {
            abort(404);
        }
        return view('admin.services.options.edit')->with('optionType', $optionType);
    }


    public function update(Requests\EditOptionValue $request, $id)
    {
        if ($this->admin->role_id == 3) {
            abort(403);
        }
        $i = 0;
        //check not empty
        if ($request->optionValue) {
            foreach ($request->optionValue as $key => $value) {
                if (is_array($request->optionValue[$key]['value'])) {
                    foreach ($value as $newOptionValue) {
                        if ($newOptionValue) {
                            $i++;
                        }
                    }
                }
                if (!is_array($request->optionValue[$key]['value']) && !empty($request->optionValue[$key]['value'])) {
                    $i++;
                }
            }
        }

        if (!$i) {
            \Session::flash('message', ['alert-danger' => 'Option value is required']);
            return redirect()->back();
        }

        $optionType = Model\OptionType::where('id', '=', $id)->with('option')->first();
        if (!$optionType instanceof Model\OptionType) {
            abort(404);
        }

        try {
            \DB::transaction(function () use ($request, $id) {
                $allValues = Model\Option::where('option_type_id', '=', $id)->get(['id'])->toArray();
                foreach ($allValues as $valueOptions) {
                    if (!array_key_exists($valueOptions['id'], $request->optionValue)) {
                        $delVal = Model\Option::where('option_type_id', '=', $id)
                            ->where('id', '=', $valueOptions['id'])
                            ->where(function ($query) {
                                $query->where('id', '!=', 1)->where('id', '!=', 8)->where('id', '!=', 13);
                            })->first();
                        if ($delVal instanceof Model\Option) {
                            $delVal->delete();
                        }
                    }
                }
                //new option value
                if (isset($request->optionNewValue) && is_array($request->optionNewValue)) {
                    foreach ($request->optionNewValue as $newOptionValue) {
                        if ($newOptionValue) {
                            Model\Option::create([
                                'value' => $newOptionValue,
                                'option_type_id' => $id
                            ]);
                        }
                    }
                }
                //update options value
                foreach ($request->optionValue as $key => $value) {
                    if (!is_array($request->optionValue[$key]['value']) && !empty($request->optionValue[$key]['value'])) {
                        $optionValues = Model\Option::where('option_type_id', '=', $id)->where('id', '=', $key)->first();
                        $optionValues->value = $request->optionValue[$key]['value'];
                        $optionValues->save();
                    }
                }
            });
            \DB::commit();
            \Session::flash('message', ['alert-success' => \Lang::get('messages.success')]);
        } catch (\Exception $e) {
            \DB::rollback();
            \Session::flash('message', ['alert-danger' => $e->getMessage()]);
        } finally {
            return redirect()->back();
        }

    }
}


