<?php

use core\ViewTwig;
use src\ubis\application\services\DelegacionDropdown;

// INICIO Cabecera global de URL de controlador *********************************

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$oDesplDelegaciones = DelegacionDropdown::listaRegDele(FALSE, 'dl_destino');
$oDesplDelegaciones->setAction("fnjs_cmb_id_dl()");


$a_campos = [
    'oDesplDelegaciones' => $oDesplDelegaciones,
];

$oView = new ViewTwig('ubis/controller');
$oView->renderizar('delegaciones.html.twig', $a_campos);
