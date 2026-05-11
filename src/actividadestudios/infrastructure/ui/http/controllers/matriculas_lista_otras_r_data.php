<?php

use src\actividadestudios\application\MatriculasListaOtrasRData;
use src\shared\web\ContestarJson;

$error = '';
$data = [];
try {
    $apellido1 = (string)($_POST['apellido1'] ?? '');
    $esquema = (string)($_SESSION['session_auth']['esquema'] ?? '');
    if ($apellido1 === '' && $esquema === '') {
        throw new \InvalidArgumentException(_('Falta contexto de sesión'));
    }
    $data = MatriculasListaOtrasRData::execute($apellido1, $esquema);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
