<?php

use src\actividadestudios\application\ActaNotasDefinitivasGrabar;

/**
 * Convierte las matriculas/notas borrador en `PersonaNota` definitivas
 * (rama `que=3` del legacy `apps/actividadestudios/controller/acta_notas_update.php`).
 *
 * Devuelve JSON `{success, mensaje}` directamente para no romper los
 * consumidores actuales.
 */
$response = ActaNotasDefinitivasGrabar::execute($_POST);
header('Content-type: application/json; charset=utf-8');
echo json_encode($response, JSON_THROW_ON_ERROR);
exit();
