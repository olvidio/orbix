<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;

/**
 * Esta página sirve para generar un cuadro con el numero de alumnos que tienen
 *  pendiente cada asignatura.
 *
 *
 * @package    delegacion
 * @subpackage    estudios
 * @author    Daniel Serrabou
 * @since        13/1/17.
 *
 */

require_once 'frontend/shared/global_header_front.inc';

$data = PostRequest::getDataFromUrl('/src/notas/asignaturas_pendientes_resumen_data', []);
$aPendientes = $data['pendientes'] ?? [];

$oView = new ViewNewPhtml('frontend\\notas\\controller');
$oView->renderizar('asignaturas_pendientes_resumen.phtml', [
    'aPendientes' => $aPendientes,
]);
