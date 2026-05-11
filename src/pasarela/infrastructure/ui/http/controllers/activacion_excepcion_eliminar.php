<?php

use src\shared\web\ContestarJson;
use src\pasarela\application\ActivacionExcepcionEliminar;

$id_tipo_activ = (string)filter_input(INPUT_POST, 'id_tipo_activ');

$error_txt = ActivacionExcepcionEliminar::execute($id_tipo_activ);
ContestarJson::enviar($error_txt, 'ok');
