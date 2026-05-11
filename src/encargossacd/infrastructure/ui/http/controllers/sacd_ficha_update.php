<?php

use src\encargossacd\application\SacdFichaUpdate;
use src\shared\web\ContestarJson;

$resultado = SacdFichaUpdate::execute($_POST);

ContestarJson::enviar(
    (string)$resultado['error'],
    (string)($resultado['mensajes'] ?? ''),
);
