<?php

namespace App\Forward\Gateway\Group;

use App\Support\Http\FilterService;
use Illuminate\Database\Eloquent\Builder;

class GroupFilterService extends FilterService
{
    /**
     * @var GroupListRequest
     */
    protected $request;
    protected $requestClass = GroupListRequest::class;

    /**
     * @param Builder $query
     */
    public function viewable(Builder $query)
    {
    }

    public function query(Builder $query)
    {
        $this->prepare()->apply($query);
        // Filter raw text search
        if ($searchText = $this->request->input('q')) {
            $query->search(
                $this->search->search($searchText)
            );
        }
        return $query;
    }
}