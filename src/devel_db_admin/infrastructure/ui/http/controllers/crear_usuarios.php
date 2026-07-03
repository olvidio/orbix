<?php

declare(strict_types=1);

use src\shared\domain\helpers\FilterPostGet;


/**
 * Ejecuta {@see CrearUsuarios} (POST: region, dl). JSON `data`: mapa con esquemas y passwords.
 */

use src\shared\web\ContestarJson;
use src\devel_db_admin\application\CrearUsuarios;


$Qregion = (string) \src\shared\domain\helpers\FilterPostGet::post('region');
$Qdl = (string) \src\shared\domain\helpers\FilterPostGet::post('dl');

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
