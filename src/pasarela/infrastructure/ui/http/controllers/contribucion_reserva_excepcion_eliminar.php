<?php

use frontend\shared\web\ContestarJson;
use src\pasarela\application\ContribucionReservaExcepcionEliminar;

$id_tipo_activ = (string)filter_input(INPUT_POST, 'id_tipo_activ');

$error_txt = ContribucionReservaExcepcionEliminar::execute($id_tipo_activ);
ContestarJson::enviar($error_txt, 'ok');
