<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

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

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$data = PostRequest::getDataFromUrl('/src/notas/asignaturas_pendientes_resumen_data', []);
$aPendientes = $data['pendientes'] ?? [];

$oView = new ViewNewPhtml('frontend\\notas\\controller');
$oView->renderizar('asignaturas_pendientes_resumen.phtml', [
    'aPendientes' => $aPendientes,
]);
