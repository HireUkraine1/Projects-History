<?php

class PageController extends BaseController
{

    /*
    |--------------------------------------------------------------------------
    | Default Static Page Controller
    |--------------------------------------------------------------------------
    |
    |
    */
    protected $layout = 'frontend.layouts.site';


    public function index()
    {
        $serve = Route::currentRouteName();
        $breadcrumbs = [];
        switch ($serve) {
            case 'about':
                $this->layout->tagline = View::make('frontend.static.about-tagline');
                $this->layout->content = View::make('frontend.static.about-content');
                $breadcrumbs = [
                    ['link' => '#', 'title' => 'About SITE'],
                ];
                $metadata = [
                    'meta' => [
                        'description' => "Our Mission is to bring you info on every performer, every tour, every concert, and every ticket",
                        'robots' => "noindex, nofollow",
                        'title' => "Learn About What site Has To Offer",
                    ],
                ];

                break;
            case 'premium-member':
                $activePlan = FanHelper::getActivePlan();
                View::share('activePlan', $activePlan);
                $this->layout->tagline = View::make('frontend.static.premium-member-tagline');
                $this->layout->content = View::make('frontend.static.premium-member-content');
                $breadcrumbs = [
                    ['link' => '#', 'title' => 'Premium Membership'],
                ];
                $metadata = [
                    'meta' => [
                        'description' => "Our Mission is to bring you info on every performer, every tour, every concert, and every ticket",
                        // 'robots'		=> "noindex, nofollow",
                        'title' => "Learn About site Premium Membership | $0 Fee",
                    ],
                ];
                break;
            case 'ticket-guarantee':
                $this->layout->tagline = View::make('frontend.static.ticket-guarantee-tagline');
                $this->layout->content = View::make('frontend.static.ticket-guarantee-content');
                $breadcrumbs = [
                    ['link' => '#', 'title' => 'Ticket Guarantee'],
                ];
                $metadata = [
                    'meta' => [
                        'robots' => "noindex, nofollow",
                        'title' => "site Ticket Guarantee and Secure Purchasing Information",
                        'description' => "Order your tickets online and feel safe knowing your order is protected by our secure checkout and 100% ticket guarantee.",
                    ],
                ];
                break;
            case 'contact':
                $url = URL::route('contact');
                $formDataz = false;
                $formDataz = Input::all();
                if (isset($formDataz['email'])):
                    // The message
                    $message = $formDataz['message'];
                    $message .= "\r\nFrom: " . $formDataz['name'] . " (" . $formDataz['email'] . ")";
                    // In case any of our lines are larger than 70 characters, we should use wordwrap()
                    $message = wordwrap($message, 70, "\r\n");

                    // Send
                    mail('info@site.com', "FORM CONTACT: " . $formDataz['subject'], $message);
                    return Redirect::to('contact')->with('message', true);
                endif;
                $this->layout->tagline = View::make('frontend.static.contact-tagline');
                $this->layout->content = View::make('frontend.static.contact-content');
                $breadcrumbs = [
                    ['link' => '#', 'title' => 'Contact SITE'],
                ];
                $metadata = [
                    'meta' => [
                        'description' => "Find all the contact information for site customer support and general inquiries",
                        'robots' => "noindex, nofollow",
                        'title' => "Contact site Customer Support Team",
                    ],
                ];
                break;
            case 'terms-and-conditions':
                $this->layout->tagline = View::make('frontend.static.terms-and-conditions-tagline');
                $this->layout->content = View::make('frontend.static.terms-and-conditions-content');
                $breadcrumbs = [
                    ['link' => '#', 'title' => 'Terms And Conditions'],
                ];
                $metadata = [
                    'meta' => [
                        'robots' => "noindex, nofollow",
                        'description' => "Terms, Conditions & Privacy Policy of usage and ticket purchasing on site.com",
                        'title' => "Terms, Conditions & Privacy Policy for site",
                    ],
                ];
                break;
            case 'membership-terms-and-conditions':
                $this->layout->tagline = View::make('frontend.static.terms-and-conditions-tagline');
                $this->layout->content = View::make('frontend.static.tourpass-terms-and-conditions-content');
                $breadcrumbs = [
                    ['link' => '#', 'title' => 'Terms And Conditions'],
                ];
                $metadata = [
                    'meta' => [
                        'robots' => "noindex, nofollow",
                        'description' => "Terms, Conditions & Privacy Policy of usage and ticket purchasing on site.com",
                        'title' => "Terms, Conditions & Privacy Policy for site",
                    ],
                ];
                break;
            case 'tour-tracker-signup':
                $input = Input::get();
                if ($input && isset($input['name']) && isset($input['email'])):
                    $message = VarsHelper::signup($input['name'], $input['email']);
                    if ($message === true):
                        return Redirect::to('site-tracker-signup')->with('message', $message);
                    else:
                        return Redirect::to('site-tracker-signup')->with('message', $message);
                    endif;
                endif;
                $this->layout->tagline = View::make('frontend.static.signup-tagline');
                $this->layout->content = View::make('frontend.static.signup-content');
                $breadcrumbs = [
                    ['link' => '#', 'title' => 'site Tracker Sign Up'],
                ];
                $metadata = [
                    'meta' => [
                        'robots' => "noindex, nofollow",
                        'title' => "SITE Tracker | Sign Up",
                    ],
                ];
                break;
            case 'tourbus-economics':

                $breadcrumbs = [
                    ['link' => '#', 'title' => 'TourBus Economics'],
                ];
                $metadata = [];
                $this->layout = View::make('frontend.layouts.tourbus-economics');
                break;

            case 'tourpass-support':
                $this->layout->customjs = View::make('frontend.fans.support-customjs');
                $this->layout->tagline = View::make('frontend.fans.support-tagline');
                $this->layout->content = View::make('frontend.fans.support-content');
                $breadcrumbs = [
                    ['link' => '#', 'title' => 'TourPass Support'],
                ];
                $metadata = [
                    'meta' => [
                        // 'robots'		=> "noindex, nofollow",
                        'title' => "TourPass Membership Support",
                        'description' => "Get Support for your TourPass Membership account from site support team"
                    ],
                ];
                break;

            case 'tourpass-about':
                $this->layout->tagline = View::make('frontend.static.tourpass-tagline');
                $this->layout->content = View::make('frontend.static.tourpass-content');
                $breadcrumbs = [
                    ['link' => '#', 'title' => 'TourPass Support'],
                ];
                $metadata = [
                    'meta' => [
                        // 'robots'		=> "noindex, nofollow",
                        'title' => "About TourPass Membership from site",
                        'description' => "The TourPass Membership from site allows you to purchase tickets with NO Service fees."
                    ],
                ];
                break;

            default:
                App::abort(404);
                break;
        }
        View::share('metadata', $metadata);
        View::share('breadcrumbs', $breadcrumbs);
    }

}