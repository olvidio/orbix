<?php

use src\encargossacd\application\ListasComTxtUpdate;
use src\shared\web\ContestarJson;

$clave = (string)(filter_input(INPUT_POST, 'clave') ?? filter_input(INPUT_GET, 'clave') ?? '');
$idioma = (string)(filter_input(INPUT_POST, 'idioma') ?? filter_input(INPUT_GET, 'idioma') ?? '');
$comunicacion = (string)(filter_input(INPUT_POST, 'comunicacion') ?? '');

ContestarJson::enviar('', ListasComTxtUpdate::execute($clave, $idioma, $comunicacion));
