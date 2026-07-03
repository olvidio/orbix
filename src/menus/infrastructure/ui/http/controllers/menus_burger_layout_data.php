<?php

use src\shared\web\ContestarJson;
use src\menus\application\MenusBurgerLayoutDataUseCase;
use src\shared\infrastructure\DependencyResolver;

$error_txt = '';
$data = [];
try {
    $rawPost = $_POST['lista_grup_menu_json'] ?? '[]';
    $raw = is_string($rawPost) ? $rawPost : '[]';
    $listaGrupMenu = json_decode($raw, true);
    if (!is_array($listaGrupMenu)) {
        $listaGrupMenu = [];
    }
    /** @var array<int|string, string> $listaGrupMenuTyped */
    $listaGrupMenuTyped = [];
    foreach ($listaGrupMenu as $key => $value) {
        if (is_scalar($value) || $value === null) {
            $listaGrupMenuTyped[$key] = (string) $value;
        }
    }
    /** @var MenusBurgerLayoutDataUseCase $useCase */
    $useCase = DependencyResolver::get(MenusBurgerLayoutDataUseCase::class);
    $data = $useCase($listaGrupMenuTyped);
} catch (\Throwable $e) {
    $error_txt = $e->getMessage();
}

ContestarJson::enviar($error_txt, $data);
