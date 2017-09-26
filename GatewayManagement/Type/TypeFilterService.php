<?php

namespace App\Forward\Type;

use Illuminate\Database\Eloquent\Builder;
use App\Support\Http\FilterService;

class TypeFilterService extends FilterService
{
    /**
     * @var TypeListRequest
     */
    protected $request;
    protected $requestClass = TypeListRequest::class;

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