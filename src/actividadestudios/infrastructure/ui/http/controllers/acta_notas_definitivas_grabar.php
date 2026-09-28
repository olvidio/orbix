<?php

use src\actividadestudios\application\ActaNotasDefinitivasGrabar;
use src\actividadestudios\application\ActaNotasMatriculaGuardar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

/**
 * Convierte las matriculas/notas borrador en `PersonaNota` definitivas
 * (rama `que=3` del legacy `apps/actividadestudios/controller/acta_notas_update.php`).
 *
 * Primero persiste el borrador del formulario (`id_nom[]`, notas), para que
 * un asistente de paso (id_nom negativo) o un cambio aún no enviado por
 * onchange no se pierda. Luego convierte las matrículas de BD en tessera.
 *
 * Devuelve JSON `{success, mensaje}` directamente para no romper los
 * consumidores actuales.
 */
try {
    $ctx = HashB::open(
        (string)\src\shared\domain\helpers\FilterPostGet::post('ctx_definitivas'),
        'acta_notas_definitivas_grabar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$_POST['id_activ'] = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_activ');
$_POST['id_asignatura'] = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_asignatura');
$_POST['id_schema'] = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_schema');

/** @var ActaNotasMatriculaGuardar $guardarBorrador */
$guardarBorrador = DependencyResolver::get(ActaNotasMatriculaGuardar::class);
$errorBorrador = $guardarBorrador->execute($_POST);
if ($errorBorrador !== '') {
    header('Content-type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'mensaje' => $errorBorrador], JSON_THROW_ON_ERROR);
    exit();
}

/** @var ActaNotasDefinitivasGrabar $useCase */
$useCase = DependencyResolver::get(ActaNotasDefinitivasGrabar::class);
$response = $useCase->execute($_POST);
header('Content-type: application/json; charset=utf-8');
echo json_encode($response, JSON_THROW_ON_ERROR);
exit();
