<?php

use src\dbextern\application\DesunirPersonaUseCase;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

$id_nom_listas = input_int($_POST, 'id_nom_listas');
$tipo_persona = input_string($_POST, 'tipo_persona');

$error_txt = DependencyResolver::get(DesunirPersonaUseCase::class)($id_nom_listas, $tipo_persona);

ContestarJson::enviar($error_txt, 'ok');
