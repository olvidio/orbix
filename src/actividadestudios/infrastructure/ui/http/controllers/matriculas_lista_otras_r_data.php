<?php

use src\actividadestudios\application\MatriculasListaOtrasRData;
use src\shared\web\ContestarJson;
use src\ubis\domain\RegionStgrAviso;

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
        $problemasRegionStgr = [];
        RegionStgrAviso::registrar($problemasRegionStgr, $e);
        $data = [
            'titulo' => '',
            'titulo_busqueda_por_apellidos' => _('búsqueda por apellidos'),
            'msg_err' => '',
            'aviso' => RegionStgrAviso::formatear($problemasRegionStgr),
            'a_valores' => [],
        ];
    } else {
        $error = $e->getMessage();
    }
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
