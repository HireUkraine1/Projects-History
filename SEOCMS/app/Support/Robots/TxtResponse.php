<?php

namespace App\Support\Robots;

use Illuminate\Support\Facades\Response;

/**
 * Class XmlResponse
 * @package XmlResponse
 */
class TxtResponse
{
    /**
     * @var array
     */
    private $header;

    /**
     * @var int
     */
    private $status;

    /**
     * TxtResponse constructor.
     * @param string $domen
     */
    public function __construct(string $domen)
    {
        $this->header = $this->header();
        $this->status = 200;
    }

    /**
     * @param RobotRules $rules
     * @return mixed
     */
    public function rules(RobotRules $rules)
    {
        $domainRules = "";
        foreach ($rules->getRules() as $rule) {
            $domainRules .= $rule ."\n";
        }

        return Response::make($domainRules, $this->status, $this->header);
    }


    /**
     * @return array
     */
    private function header()
    {
        return [
            'Content-Type' => 'text/plain'
        ];
    }


}

