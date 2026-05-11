<?php

declare(strict_types=1);

/**
 * Ejecuta {@see CrearUsuarios} (POST: region, dl). JSON `data`: mapa con esquemas y passwords.
 */

use src\shared\web\ContestarJson;
use src\devel_db_admin\application\CrearUsuarios;

require_once 'frontend/shared/global_header_front.inc';

$Qregion = (string) filter_input(INPUT_POST, 'region');
$Qdl = (string) filter_input(INPUT_POST, 'dl');

$sessionEsSf = isset($_SESSION['sfsv']) && $_SESSION['sfsv'] === 'sf';

$result = (new CrearUsuarios())->ejecutar($Qregion, $Qdl, $sessionEsSf);

ContestarJson::enviar('', [
    'esquema' => $result->esquema,
    'esquemaPwd' => $result->esquemaPwd,
    'esquemav' => $result->esquemav,
    'esquemavPwd' => $result->esquemavPwd,
    'esquemaf' => $result->esquemaf,
    'esquemafPwd' => $result->esquemafPwd,
]);
