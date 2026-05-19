<?php

use frontend\shared\config\OrbixRuntime;
use frontend\shared\model\ViewNewPhtml;

/**
 * Popup estatico con la leyenda de colores/estilos del planning.
 *
 * Antes vivia en `apps/planning/controller/leyenda.php` y usaba Twig;
 * ahora se renderiza como PHTML canonico segun `refactor.md`.
 */

// INICIO Cabecera global de URL de controlador *********************************
require_once("frontend/shared/global_header_front.inc");

// FIN de  Cabecera global de URL de controlador ********************************

include_once(OrbixRuntime::dirEstilos() . '/calendario_color_cols.css.php');
include_once(OrbixRuntime::dirEstilos() . '/calendario.css.php');

$oView = new ViewNewPhtml('frontend\\planning\\controller');
$oView->renderizar('leyenda.phtml', []);
