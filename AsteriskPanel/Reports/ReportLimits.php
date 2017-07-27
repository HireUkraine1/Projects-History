<?php

namespace App\Reports;

class ReportLimits
{
    /**
     * @var int
     */
    private $success = 5;

    /**
     * @var int
     */
    private $warning = 10;


    /**
     * @return array
     */
    public function getList()
    {
        return [
            'success' => $this->success,
            'warning' => $this->warning,
        ];
    }
}