<?php

namespace App\Http\Controllers\FrontendControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Mail;
use App\Models;

class ContactFormController extends Controller
{
    public function send(Requests\ContactFormRequest $request)
    {
        $data = [];
        $data['from'] = $request->email;
        switch ($request->interest) {
            case 1:
                $emailTo = \Config::get('settings.contact_sport_email');
                $interest = Models\Category::where('id', '=', 1)->first();
                break;
            case 2:
                $emailTo = \Config::get('settings.contact_sport_email');
                $interest = Models\Category::where('id', '=', 2)->first();
                break;
            case 3:
                $emailTo = \Config::get('settings.contact_sport_email');
                $interest = Models\Category::where('id', '=', 3)->first();
                break;
            default:
                $emailTo = \Config::get('settings.contact_sport_email');
                $interest = Models\Category::where('id', '=', 1)->first();
                break;
        }
        $data['interest'] = $interest->name;
        $data['message'] = $request->message;

        \Mail::to($emailTo)->send(new Mail\ContactFormMail($data));
        session()->flash('send', 'Your message was sent successfully');
        return back();
    }
}
