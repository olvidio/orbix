<?php

use src\encargossacd\application\SacdAusenciasUpdate;
use frontend\shared\web\ContestarJson;

$resultado = SacdAusenciasUpdate::execute($_POST);

ContestarJson::enviar(
    (string)$resultado['error'],
    (string)($resultado['mensajes'] ?? ''),
);
