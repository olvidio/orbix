<?php

use src\shared\web\ContestarJson;
use src\menus\application\MenusGetPageData;

$error = '';
$data = [];
try {
    $data = MenusGetPageData::execute($_POST);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
