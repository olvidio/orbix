<?php

use src\shared\web\ContestarJson;
use src\menus\application\MenusLegacyLayoutItemsUseCase;

$error_txt = '';
$data = [];
try {
    $id_grupmenu = (string)($_POST['id_grupmenu'] ?? '1');
    $useCase = new MenusLegacyLayoutItemsUseCase();
    $data = ['items' => $useCase($id_grupmenu)];
} catch (\Throwable $e) {
    $error_txt = $e->getMessage();
}

ContestarJson::enviar($error_txt, $data);
