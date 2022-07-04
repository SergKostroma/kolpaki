<?php

use App\Application;

include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$man = new \App\Facades\CatalogManager();
$man->import();
