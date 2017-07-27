<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AppModel extends Model implements AppModelInterface
{
    public function scopeGetAll($query)
    {
        $result = $query->get();
        if (count($result) == 0) {
            $result = collect([[]]);
        };
        return $result;
    }
}
