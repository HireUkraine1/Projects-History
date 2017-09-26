<?php

namespace App\Forward\Gateway;

use Illuminate\Database\Eloquent\Builder;
use App\Support\Http\FilterService;

class GatewayFilterService extends FilterService
{
    /**
     * @var GatewayListRequest
     */
    protected $request;
    protected $requestClass = GatewayListRequest::class;

    /**
     * @param Builder $query
     */
    public function viewable(Builder $query)
    {
        $checkPerms = function () use ($query) {
            if (!$this->permission->has('network.forward.read')) {
                abort(403, 'You do not have access to read Port Forwarding.');
            }
        };

        $this->auth->only([
            'admin' => $checkPerms,
            'integration' => $checkPerms,
        ]);
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