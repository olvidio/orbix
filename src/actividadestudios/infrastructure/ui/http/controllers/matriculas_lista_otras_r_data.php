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
} catch (\RuntimeException $e) {
    if (MatriculasListaOtrasRData::esAvisoRegionStgr($e)) {
        $data = [
            'titulo' => '',
            'titulo_busqueda_por_apellidos' => _('búsqueda por apellidos'),
            'msg_err' => '',
            'aviso' => $e->getMessage(),
            'a_valores' => [],
        ];
    } else {
        $error = $e->getMessage();
    }
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
