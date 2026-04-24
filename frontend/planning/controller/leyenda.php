<?php

use src\shared\config\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;

/**
 * Popup estatico con la leyenda de colores/estilos del planning.
 *
 * Antes vivia en `apps/planning/controller/leyenda.php` y usaba Twig;
 * ahora se renderiza como PHTML canonico segun `refactor.md`.
 */

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

include_once(ConfigGlobal::$dir_estilos . '/calendario.css.php');

$oView = new ViewNewPhtml('frontend\\planning\\controller');
$oView->renderizar('leyenda.phtml', []);
