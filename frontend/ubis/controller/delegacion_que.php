<?php

use frontend\shared\model\ViewNewPhtml;
use src\ubis\application\services\DelegacionDropdown;

// INICIO Cabecera global de URL de controlador *********************************

require_once("frontend/shared/global_header_front.inc");


$oDesplDelegaciones = DelegacionDropdown::listaRegDele(FALSE, 'dl_destino');
$oDesplDelegaciones->setAction("fnjs_cmb_id_dl()");


$a_campos = [
    'oDesplDelegaciones' => $oDesplDelegaciones,
];

$oView = new ViewNewPhtml('frontend\ubis\controller');
$oView->renderizar('delegaciones.phtml', $a_campos);
