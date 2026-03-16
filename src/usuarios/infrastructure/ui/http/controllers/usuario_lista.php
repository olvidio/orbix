<?php

use src\usuarios\application\usuariosLista;
use web\ContestarJson;

// Se usa al buscar:
$Qusername = (string)filter_input(INPUT_POST, 'username');

$jsondata = usuariosLista::usuariosLista($Qusername);

// envía una Response
ContestarJson::send($jsondata);
