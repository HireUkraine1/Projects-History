<?php

namespace App\Forward\Port\Acl;

use App\Database\ModelRepository;

class AclRepository extends ModelRepository
{
    protected $model = Acl::class;
}