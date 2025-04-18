<?php

use actividades\model\entity\GestorTipoDeActividad;
use web\Desplegable;
use procesos\model\entity\GestorActividadFase;

// INICIO Cabecera global de URL de controlador *********************************

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos para esta url  **********************************************

// FIN de  Cabecera global de URL de controlador ********************************

$Qdl_propia = (string)filter_input(INPUT_POST, 'dl_propia');
$Qid_tipo_activ = (string)filter_input(INPUT_POST, 'id_tipo_activ');

$GesTiposActiv = new GestorTipoDeActividad();
$aTiposDeProcesos = $GesTiposActiv->getTiposDeProcesos($Qid_tipo_activ, $Qdl_propia);
$oGesFases = new GestorActividadFase();
$oDesplFases = $oGesFases->getListaActividadFases($aTiposDeProcesos);


echo $oDesplFases->options();