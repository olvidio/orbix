<?php

use src\dbextern\application\SincroPersonas;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use function src\shared\domain\helpers\input_string;

$region = input_string($_POST, 'region');
$dl_listas = input_string($_POST, 'dl_listas');
$tipo_persona = input_string($_POST, 'tipo_persona');

$result = DependencyResolver::get(SincroPersonas::class)($region, $dl_listas, $tipo_persona);

$error_txt = '';
if ($result['msg'] !== '') {
    $msg = $result['msg'];
} else {
    $msg = sprintf(_("OK. %s personas sincronizadas"), $result['count']);
}

ContestarJson::enviar($error_txt, ['mensaje' => $msg]);
