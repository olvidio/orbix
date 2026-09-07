<?php

use src\actividadestudios\application\ActaNotasDefinitivasGrabar;
use src\actividadestudios\application\ActaNotasMatriculaGuardar;
use src\shared\infrastructure\DependencyResolver;

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
