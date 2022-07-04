<?php

namespace App;

use App\Api\Presto\Api as ApiPresto;

class Application
{
    private static $instance = null;
    private $api = null;

    public static $logger = null;

    private function __construct(){
        self::$logger = new Logger("/upload/Logs/Application/log.txt");
    }

    public static function getInstance(): self
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function initApiPresto()
    {
        $this->api['presto'] = new ApiPresto();
        return $this->api['presto'];
    }

    public function getObjectApi($nameApi)
    {
        if (! array_key_exists($nameApi, $this->api)) {
            throw new \Exception("api with name \"$nameApi\" is not created");
        }

        return $this->api[$nameApi];
    }
}
