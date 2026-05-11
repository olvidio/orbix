<?php

use src\shared\web\ContestarJson;
use src\menus\application\MenusBurgerLayoutDataUseCase;

$error_txt = '';
$data = [];
try {
    $raw = (string)($_POST['lista_grup_menu_json'] ?? '[]');
    $listaGrupMenu = json_decode($raw, true);
    if (!is_array($listaGrupMenu)) {
        $listaGrupMenu = [];
    }
    $useCase = new MenusBurgerLayoutDataUseCase();
    $data = $useCase($listaGrupMenu);
} catch (\Throwable $e) {
    $error_txt = $e->getMessage();
}

ContestarJson::enviar($error_txt, $data);
