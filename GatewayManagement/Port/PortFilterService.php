<?php

namespace App\Forward\Port;

use App\Support\Http\FilterService;
use Illuminate\Database\Eloquent\Builder;

class PortFilterService extends FilterService
{
    /**
     * @var PortListRequest
     */
    protected $request;
    protected $requestClass = PortListRequest::class;

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