<?php


class ReportController extends BaseController
{
    protected $layout = 'admin.layouts.default';
    protected $conf = '';

    public function csv()
    {
        $request = Input::get('request');
        $type = Input::get('type');
        $lfcr = chr(10);
        $output = '"URL","Title","Wikipedia","Keywords"' . $lfcr;
        switch ($type) {
            case 'performers':
                $skip = ['tribute', 'symphony', 'orchestra'];
                $pattern = '/(' . strtolower(implode('|', $skip)) . ')/';
                $performers = Performer::all();
                $filename = "site_" . date('Y-m-d-H-i-s') . "_performers.csv";
                foreach ($performers as $p):
                    if (!preg_match($pattern, strtolower($p->name))):
                        $output .= '"http://www.site.com/concerts/' . $p->slug . '","' . $p->name . ' Tour Dates And Concert Schedule","","' . $p->name . '"' . $lfcr;
                    endif;
                endforeach;
                $headers = array(
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                );
                return Response::make(rtrim($output, "\n"), 200, $headers);
                break;
            case 'concerts':
                $concerts = Concert::where('date', '>', date('Y-m-d'))->orderBy('date', 'asc')->groupBy('slug')->get();
                $filename = "site_" . date('Y-m-d-H-i-s') . "_concerts.csv";
                foreach ($concerts as $c):
                    $output .= '"http://www.site.com/concerts/' . $c->slug . '","' . $c->name . '"' . $lfcr;
                endforeach;
                $headers = array(
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                );
                return Response::make(rtrim($output, "\n"), 200, $headers);
                break;
            case 'announcements':
                $announcements = Announcement::where('status', 1)->orderBy('publish_date', 'asc')->get();
                $filename = "site_" . date('Y-m-d-H-i-s') . "_announcements.csv";
                $settings = SiteSetting::where('string_id', 'annuncements_post_url')->first();
                foreach ($announcements as $a):
                    $url = ($a->is_page) ? $settings->value : '/concerts/';
                    $output .= '"http://www.site.com' . $url . $a->slug . '","' . str_replace('"', "'", $a->title) . '"' . $lfcr;
                endforeach;
                $headers = array(
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                );
                return Response::make(rtrim($output, "\n"), 200, $headers);
                break;
            default:
                $this->layout->content = View::make('admin.reports.csv');
                $this->layout->mainmenu = View::make('admin.mainmenu');
                break;
        }
    }

}