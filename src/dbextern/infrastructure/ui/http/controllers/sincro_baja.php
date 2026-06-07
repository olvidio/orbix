<?php

use src\dbextern\application\BajaPersonaUseCase;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

$dl = input_string($_POST, 'dl');
$tipo_persona = input_string($_POST, 'tipo_persona');
$id_nom_orbix = input_int($_POST, 'id_nom_orbix');

$error_txt = DependencyResolver::get(BajaPersonaUseCase::class)($id_nom_orbix, $tipo_persona, $dl);

ContestarJson::enviar($error_txt, 'ok');
