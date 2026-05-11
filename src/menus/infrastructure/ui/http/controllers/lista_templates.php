<?php

use src\menus\application\ListaTemplatesMenus;
use src\shared\web\ContestarJson;

$error_txt = '';

$ListaTemplatesMenus = new ListaTemplatesMenus();
$data = $ListaTemplatesMenus();

// envía una Response
ContestarJson::enviar($error_txt, $data);
