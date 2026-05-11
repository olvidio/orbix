<?php

use src\encargossacd\application\ListasComTxtGet;
use src\shared\web\ContestarJson;

$clave = (string)(filter_input(INPUT_POST, 'clave') ?? filter_input(INPUT_GET, 'clave') ?? '');
$idioma = (string)(filter_input(INPUT_POST, 'idioma') ?? filter_input(INPUT_GET, 'idioma') ?? '');

ContestarJson::enviar('', ListasComTxtGet::execute($clave, $idioma));
