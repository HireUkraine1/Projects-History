<?php

namespace App\Models;

interface PageCompileStatus
{
    const WAITING = 0;

    const SUCCESS = 1;

    const ERROR = 2;
}
