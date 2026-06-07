<?php

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

use src\shared\web\ContestarJson;
use src\menus\application\MenusGetPageData;
use src\shared\infrastructure\DependencyResolver;

$error = '';
$data = [];
try {
    /** @var MenusGetPageData $menusGetPageData */
    $menusGetPageData = DependencyResolver::get(MenusGetPageData::class);
    $data = $menusGetPageData->execute($_POST);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
