<?php

use zonassacd\model\entity\GestorZona;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_zona = (integer)filter_input(INPUT_POST, 'id_zona');

$oGestorZona = new GestorZona();
$oDesplZonas = $oGestorZona->getListaZonas();
$oDesplZonas->setNombre('id_zona_sel');
$oDesplZonas->setAction('fnjs_lista_ctrs_por_zona()');
$oDesplZonas->setOpcion_sel($Qid_zona);

echo _("zona").': ';
echo $oDesplZonas->desplegable();
