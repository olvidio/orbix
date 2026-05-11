<?php

use src\shared\web\ContestarJson;
use src\dbextern\application\BajaPersonaUseCase;

$dl = (string)filter_input(INPUT_POST, 'dl');
$tipo_persona = (string)filter_input(INPUT_POST, 'tipo_persona');
$id_nom_orbix = (string)filter_input(INPUT_POST, 'id_nom_orbix');

$useCase = new BajaPersonaUseCase();
$error_txt = $useCase($id_nom_orbix, $tipo_persona, $dl);

ContestarJson::enviar($error_txt, 'ok');
