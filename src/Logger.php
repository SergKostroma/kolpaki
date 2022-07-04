<?php

namespace App;

class Logger
{
    private $pathTofile = null;

    public function __construct(string $pathToFile)
    {
        $pathToFile = explode('/', $pathToFile);
        $pathToDir = $_SERVER['DOCUMENT_ROOT'] . "/";

        if (count($pathToFile) > 1) {
            $dirs = array_slice($pathToFile, 0, (count($pathToFile) - 1));
        }

        if (isset($dirs)) {

            foreach ($dirs as $dir) {
                if (empty($dir)) continue;

                $this->createDir($dir, $pathToDir);
                $pathToDir .= $dir . '/';
            }

        }

        $this->pathTofile = $pathToDir . $pathToFile[count($pathToFile) - 1];

        if (! file_exists($pathToDir . $pathToFile[count($pathToFile) - 1])) {
            $this->add('Start log');
        }
    }

    private function createDir($name, $path = '/')
    {
        $path = $path . $name;

        if (! file_exists($path)) {
            mkdir($path, 0777, true);
        }
    }

    public function add(string $message)
    {
        $now = new \DateTime();
        $message = "[" . $now->format('d.m.Y (H:i:s)') . "] $message" . PHP_EOL;
        file_put_contents($this->pathTofile,  $message, FILE_APPEND);
    }
}
