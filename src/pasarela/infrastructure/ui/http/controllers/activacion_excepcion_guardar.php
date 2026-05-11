<?php

use src\shared\web\ContestarJson;
use src\pasarela\application\ActivacionExcepcionGuardar;

$id_tipo_activ = (string)filter_input(INPUT_POST, 'id_tipo_activ');
$valor = (string)filter_input(INPUT_POST, 'valor');

$error_txt = ActivacionExcepcionGuardar::execute($id_tipo_activ, $valor);
ContestarJson::enviar($error_txt, 'ok');
