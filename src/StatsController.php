<?php

namespace HCAS;

use Illuminate\Http\JsonResponse;
use Laravel\Lumen\Routing\Controller as BaseController;

class StatsController extends BaseController
{
    /**
     * @var StatsCollector
     */
    private $statsCollector;

    function __construct(StatsCollector $statsCollector)
    {
        $this->statsCollector = $statsCollector;
    }

    function list()
    {
        return new JsonResponse($this->statsCollector->loadAll());
    }

    function healthCheck()
    {
        $httpStatus = 200;
        $errors = $this->statsCollector->runHealthChecks();
        if (count($errors) > 0) {
            $httpStatus = 500;
        }
        return new JsonResponse($errors, $httpStatus);
    }
}
