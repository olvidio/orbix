<?php

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

use src\shared\web\ContestarJson;
use src\menus\application\MenusLegacyLayoutItemsUseCase;
use src\shared\infrastructure\DependencyResolver;

$error_txt = '';
$data = [];
try {
    $id_grupmenu = input_string($_POST, 'id_grupmenu', '1');
    /** @var MenusLegacyLayoutItemsUseCase $useCase */
    $useCase = DependencyResolver::get(MenusLegacyLayoutItemsUseCase::class);
    $data = ['items' => $useCase($id_grupmenu)];
} catch (\Throwable $e) {
    $error_txt = $e->getMessage();
}

ContestarJson::enviar($error_txt, $data);
