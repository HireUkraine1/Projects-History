<?php

namespace App\Http\Controllers\Common;

use App\Faq;
use App\StaticPage;
use Illuminate\Http\Request;

class StaticPagesController extends CommonSearchContrloller
{
    /**
     * Service page
     *
     * @return mixed
     */
    public function service()
    {
        $page = StaticPage::where('header', 'О сервисе')->first();
        $content = $page->content;
        return view('common.service')->with('content', $content)->with('data', $this->data);
    }

    /**
     * Help page
     *
     * @return mixed
     */
    public function help()
    {
        $questions = Faq::all()->toArray();
        $page = StaticPage::where('header', 'Помощь')->first();
        $content = $page->content;
        return view('common.help')->with('content', $content)->with('questions', $questions)->with('data', $this->data);
    }

    /**
     * Contact us page
     *
     * @return mixed
     */
    public function contactUs()
    {
        $page = StaticPage::where('header', 'Связаться с нами')->first();
        $content = $page->content;
        return view('common.contact-us')->with('content', $content)->with('data', $this->data);;
    }

}
  