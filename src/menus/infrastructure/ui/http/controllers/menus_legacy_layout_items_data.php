<?php

use src\shared\web\ContestarJson;
use src\menus\application\MenusLegacyLayoutItemsUseCase;
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FuncTablasSupport;

$error_txt = '';
$data = [];
try {
    $id_grupmenu = FuncTablasSupport::inputString($_POST, 'id_grupmenu', '1');
    /** @var MenusLegacyLayoutItemsUseCase $useCase */
    $useCase = DependencyResolver::get(MenusLegacyLayoutItemsUseCase::class);
    $data = ['items' => $useCase($id_grupmenu)];
} catch (\Throwable $e) {
    $error_txt = $e->getMessage();
}

ContestarJson::enviar($error_txt, $data);
