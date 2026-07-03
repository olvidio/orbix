<?php

use src\dbextern\application\CrearPersonaDesdeListasUseCase;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FuncTablasSupport;
$id_nom_listas = FuncTablasSupport::inputInt($_POST, 'id_nom_listas');
$tipo_persona = FuncTablasSupport::inputString($_POST, 'tipo_persona');

$error_txt = DependencyResolver::get(CrearPersonaDesdeListasUseCase::class)($id_nom_listas, $tipo_persona);

$id = FuncTablasSupport::inputInt($_POST, 'id');
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
