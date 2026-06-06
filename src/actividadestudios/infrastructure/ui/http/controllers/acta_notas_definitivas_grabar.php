<?php

use src\actividadestudios\application\ActaNotasDefinitivasGrabar;
use src\shared\infrastructure\DependencyResolver;

/**
 * Convierte las matriculas/notas borrador en `PersonaNota` definitivas
 * (rama `que=3` del legacy `apps/actividadestudios/controller/acta_notas_update.php`).
 *
 * Devuelve JSON `{success, mensaje}` directamente para no romper los
 * consumidores actuales.
 */
/** @var ActaNotasDefinitivasGrabar $useCase */
$useCase = DependencyResolver::get(ActaNotasDefinitivasGrabar::class);
$response = $useCase->execute($_POST);
header('Content-type: application/json; charset=utf-8');
echo json_encode($response, JSON_THROW_ON_ERROR);
exit();
