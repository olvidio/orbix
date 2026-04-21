<?php

use src\encargossacd\application\EncargoTextoListasComAjax;
use web\ContestarJson;

$que = filter_input(INPUT_POST, 'que');
if ($que === null) {
    $que = filter_input(INPUT_GET, 'que');
}
$que = (string)$que;

$clave = (string)(filter_input(INPUT_POST, 'clave') ?? filter_input(INPUT_GET, 'clave') ?? '');
$idioma = (string)(filter_input(INPUT_POST, 'idioma') ?? filter_input(INPUT_GET, 'idioma') ?? '');

if ($que !== 'get_texto' && $que !== 'update') {
    ContestarJson::enviar(_('acción no válida'), []);
    return;
}

$comunicacion = null;
if ($que === 'update') {
    $comunicacion = (string)filter_input(INPUT_POST, 'comunicacion');
}

ContestarJson::enviar('', EncargoTextoListasComAjax::ejecutar($que, $clave, $idioma, $comunicacion));
