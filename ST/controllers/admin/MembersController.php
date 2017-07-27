<?php


class MembersController extends BaseController
{
    protected $layout = 'admin.layouts.default';
    protected $conf = '';

    public function editFan($id = null)
    {
        $fan = Fan::where('id', $id)->with('profiles')->with('locations')->first();
        $currentNotifications = NotificationSettings::where('fan_id', $fan->id)->with('performer')->with('performer.images')->get();
        $trackedPerformers = [];
        foreach ($currentNotifications as $cn):
            $trackedPerformers[$cn->performer_id]['performer'] = $cn->performer;
            $trackedPerformers[$cn->performer_id]['notifications'][] = $cn->days;
        endforeach;

        $this->layout->customjs = View::make('admin.fans.cssjs');
        $this->layout->content = View::make('admin.fans.edit-fan-content', ['fan' => $fan, 'trackedPerformers' => $trackedPerformers]);
        $this->layout->stripejs = View::make('admin.fans.stripejs');
    }

    /**
     * MAin function use this.
     */
    public function viewPaidMembers($id = null)
    {
        Paginator::setPageName('fan_page');
        $sort = (Input::has('sort')) ? Input::get('sort') : "id";
        $order = (Input::has('order')) ? Input::get('order') : "asc";
        $paginate = (Input::has('perpage')) ? Input::get('perpage') : "10";

        $allFans = Fan::where('id', '<>', 0);

        if (Input::has('status') && Input::get('status') != ''):
            $allFans->where('status', Input::get('status'));
        endif;

        if (Input::has('last_login_from') && Input::get('last_login_from') != ''):
            $allFans->where('last_login', '>', date('Y-m-d', strtotime(Input::get('last_login_from'))));
        endif;

        if (Input::has('last_login_to') && Input::get('last_login_to') != ''):
            $allFans->where('last_login', '<=', date('Y-m-d', strtotime(Input::get('last_login_to'))));
        endif;

        if (Input::has('created_at_from') && Input::get('created_at_from') != ''):
            $allFans->where('created_at', '>', date('Y-m-d', strtotime(Input::get('created_at_from'))));
        endif;
        if (Input::has('created_at_to') && Input::get('created_at_to') != ''):
            $allFans->where('created_at', '<=', date('Y-m-d', strtotime(Input::get('created_at_to'))));
        endif;


        if (Input::has('payStatus')):
            $status = Input::get('payStatus');
            $allFans->whereHas('info', function ($query) use ($status) {
                $query->where('status', $status);
            });
        endif;

        $allFans = $allFans->with('profiles')
            ->with('info')
            ->with('subscriptions')
            ->with('subscriptions.invoice')
            ->with('notes')
            ->orderBy($sort, $order)->paginate($paginate);
        Paginator::setPageName('pending_page');
        $pendingFans = DB::table('pending_fans')->paginate(50);
        //search
        if (Session::get('searchFans')):
            $search = Session::get('searchFans');
            $searchData = $search['date'];
            if ($searchData != null):
                switch ($search['searchType']):
                    case 'Email':
                        $searchFans = $this->searchQuery($searchData, 'email');
                        break;
                    case 'Name':
                        $searchFans = $this->searchQuery($searchData, 'name');
                        break;
                    case 'FanId':
                        $searchFans = Fan::whereid($searchData)->get();
                        break;
                    case 'Stripe Id':
                        $stripeId = FanInfo::where('stripe_id', 'LIKE', "%$searchData%")->take(5)->get(array('fan_id'))->toArray();
                        $searchFans = Fan::whereIn('id', $stripeId)->get();
                        break;
                endswitch;
            else:
                $searchFans = null;
            endif;
        else:
            $searchFans = null;
        endif;
        //end search
        $this->layout->customjs = View::make('admin.fans.cssjs');

        $this->layout->content = View::make('admin.fans.view-fans-content', ['fans' => $allFans, 'pendingFans' => $pendingFans, 'searchFans' => $searchFans]);
    }

    public function searchQuery($searchData, $typeData)
    {

        return Fan::whereIn('id', function ($query) {
            $queryData = Session::get('searchFans')['date'];
            if (Session::get('searchFans')['searchType'] == 'Email'):
                $data = 'email';
            else:
                $data = 'name';
            endif;

            $query->select('fan_id')
                ->from(with(new FanInfo)->getTable())
                ->where($data, 'LIKE', "%$queryData%");
        })->orWhere($typeData, 'LIKE', "%$searchData%")->take(5)->get();

    }

    public function searchFans()
    {


        return Redirect::to('/saleadminpanel/members')->with('searchFans', Input::get());;


    }

}
