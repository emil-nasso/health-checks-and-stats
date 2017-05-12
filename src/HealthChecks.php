<?php

namespace HCAS;

class HealthChecks
{
    private $healthChecks = [];

    function add(\Closure $healthCheck)
    {
        $this->healthChecks[] = $healthCheck;
    }

    function run()
    {
        return collect($this->healthChecks)->map(function ($item) {
            return $item();
        })->filter()->toArray();
    }

}