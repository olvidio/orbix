<?php

use src\menus\application\GrupMenuListaUseCase;
use web\ContestarJson;

$error_txt = '';

$ListaGrupMenus = new GrupMenuListaUseCase();
$data = $ListaGrupMenus();

// envía una Response
ContestarJson::enviar($error_txt, $data);