<?php
use actividades\model\entity\GestorTipoDeActividad;
use procesos\model\entity\GestorActividadFase;

// INICIO Cabecera global de URL de controlador *********************************

require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
// Crea los objectos para esta url  **********************************************

// FIN de  Cabecera global de URL de controlador ********************************

$Qsalida = (string) \filter_input(INPUT_POST, 'salida');
$Qdl_propia = (string) \filter_input(INPUT_POST, 'dl_propia');
$Qid_tipo_activ = (string) \filter_input(INPUT_POST, 'id_tipo_activ');

// buscar las fases para estos procesos
switch($Qsalida) {
	case 'desde':
		// buscar los procesos posibles para estos tipos de actividad
		$GesTiposActiv = new GestorTipoDeActividad();
		$aTiposDeProcesos = $GesTiposActiv->getTiposDeProcesos($Qid_tipo_activ,$Qdl_propia);
		$oGesFases= new GestorActividadFase();
		$oDesplFasesIni = $oGesFases->getListaActividadFases($aTiposDeProcesos);
		$oDesplFasesIni->setNombre('fase_ini');
		echo $oDesplFasesIni->desplegable();
		break;
	case 'hasta':
		// buscar los procesos posibles para estos tipos de actividad
		$GesTiposActiv = new GestorTipoDeActividad();
		$aTiposDeProcesos = $GesTiposActiv->getTiposDeProcesos($Qid_tipo_activ,$Qdl_propia);
		$oGesFases2= new GestorActividadFase();
		$oDesplFasesFin = $oGesFases2->getListaActividadFases($aTiposDeProcesos);
		$oDesplFasesFin->setNombre('fase_fin');
		echo $oDesplFasesFin->desplegable();
		break;
}
