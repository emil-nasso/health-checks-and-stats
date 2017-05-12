<?php

namespace HCAS;

class StatsCollector
{
    private $storagePath;
    private $healthChecks = [];

    function __construct(string $storagePath)
    {
        if (!is_dir($storagePath)) {
            mkdir($storagePath);
        }
        $this->storagePath = $storagePath;
    }

    function addHealthCheck(\Closure $healthCheck)
    {
        $this->healthChecks[] = $healthCheck;
    }

    function runHealthChecks()
    {
        return collect($this->healthChecks)->map(function ($item) {
            return $item();
        })->filter()->toArray();
    }

    function loadAll(): array
    {
        $result = [];
        $files = glob($this->storagePath . "/*.log");
        foreach ($files as $filename) {
            $basename = basename($filename, ".log");
            $result[$basename] = $this->load($basename);
        }
        return $result;
    }

    function add(string $key, $value)
    {
        file_put_contents($this->getFilePath($key), json_encode($value) . PHP_EOL, FILE_APPEND | LOCK_EX);
    }

    function load($key)
    {
        $contents = file_get_contents($this->getFilePath($key));
        $lines = explode(PHP_EOL, $contents);
        $lines = collect($lines)->filter()->map(function ($item) {
            return json_decode($item);
        })->toArray();
        return array_reverse($lines);
    }

    private function getFilePath(string $filename)
    {
        return $this->storagePath . "/" . $filename . ".log";
    }

}