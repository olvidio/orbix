<?php

use encargossacd\model\DesplCentros;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');
$Qfiltro_ctr = (string)filter_input(INPUT_POST, 'filtro_ctr');
$Qid_zona = (integer)filter_input(INPUT_POST, 'id_zona');

if (!empty($Qid_zona)) {
    $Qfiltro_ctr = 8;
}

$oGrupoCtr = new DesplCentros();
$oGrupoCtr->setIdZona($Qid_zona);

$oDesplCtr = $oGrupoCtr->getDesplPorFiltro($Qfiltro_ctr);
$oDesplCtr->setNombre('lst_ctrs');
$oDesplCtr->setAction('fnjs_ver_ficha()');
$oDesplCtr->setOpcion_sel($Qid_ubi);

echo $oDesplCtr->desplegable();
