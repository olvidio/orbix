<?php

use src\menus\application\ListaMetaMenus;
use src\shared\web\ContestarJson;

$error_txt = '';

$ListaMetaMenus = new ListaMetaMenus();
$data = $ListaMetaMenus();

// envía una Response
ContestarJson::enviar($error_txt, $data);
