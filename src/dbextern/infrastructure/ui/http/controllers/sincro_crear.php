<?php

use src\dbextern\application\CrearPersonaDesdeListasUseCase;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

$id_nom_listas = input_int($_POST, 'id_nom_listas');
$tipo_persona = input_string($_POST, 'tipo_persona');

$error_txt = DependencyResolver::get(CrearPersonaDesdeListasUseCase::class)($id_nom_listas, $tipo_persona);

$id = input_int($_POST, 'id');
$dbListas = $_SESSION['DBListas'] ?? null;
if ($id > 0 && is_array($dbListas) && isset($dbListas[$id])) {
    session_start();
    /** @var array<int, mixed> $sessionListas */
    $sessionListas = $_SESSION['DBListas'];
    unset($sessionListas[$id]);
    $_SESSION['DBListas'] = array_values(array_filter($sessionListas));
    session_write_close();
}

ContestarJson::enviar($error_txt, 'ok');
