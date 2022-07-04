<?php
include $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
include $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/const.php';

set_exception_handler(function ($exception) {
    $class = $exception->getTrace()[0]['class'];
    $file = "(" . $exception->getFile() . "[" . $exception->getLine() . "]" . ") ";

    if (property_exists($class, 'logger')) {
        ($class::$logger)->add($file . $exception->getMessage());
    } else {
        $defaultLogger = new \App\Logger('/upload/Logs/generalLog.txt');
        $defaultLogger->add($file . $exception->getMessage());
    }
});

\App\Application::getInstance();

function htmlDetailProduct($products, $type)
{
    $product = $products[0];

    ob_start();
    include $_SERVER['DOCUMENT_ROOT'] . "/resources/views/modal.detail/$type.php";
    $html = ob_get_contents();
    ob_end_clean();
    return $html;
}
