<?php

namespace App\Http\Controllers\Admin;

use App\Events\TranslationUpdated;
use Illuminate\Http\Request;
use Hpolthof\Translation\Controllers\TranslationsController as TransController;

class TranslationsController extends TransController
{

    public function getIndex()
    {
        \Debugbar::disable();
        return view('admin.pages.translation');
    }

    /**
     * Store with event trigger to flush cache
     *
     * @param Request $request
     *
     * @return string
     */
    public function postStore(Request $request)
    {
        $return = parent::postStore($request);

        if ($return == 'OK') {
            event(new TranslationUpdated($request->get('group'), $request->get('locale')));
        }
        return $return;
    }
}