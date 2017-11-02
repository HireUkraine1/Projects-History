<?php

namespace App\Support\Robots;

use App\Models\DomainAlias;

class RobotRules
{
    public function getRules()
    {
        $rules = [];

        $domainAlias = DomainAlias::where('domain_url', config('settings.current_domain') )->first();

        if (!$domainAlias) {
            $domainAlias =   DomainAlias::where('master', true )->first();
        }

        if ($domainAlias) {
            $rules = explode('\r\n',$domainAlias->robotstxt);
        }
        return $rules;

    }

}