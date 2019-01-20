<?php
use actividades\model\entity\TipoTarifa;
use actividades\model\entity\GestorTipoActivTarifa;
use actividades\model\entity\TipoActivTarifa;
use core\ConfigGlobal;
use web\Lista;
use web\TiposActividades;

/**
* Esta página sirve para ejecutar las operaciones de guardar, eliminar, listar...
* que se piden desde: act_tipo_tarifas.php y act_tipo_tarifa_form.php
*
*@package	delegacion
*@subpackage	actividades
*@author	Daniel Serrabou
*@since		22/12/2010.
*		
*/
// INICIO Cabecera global de URL de controlador *********************************
require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qque = (string)  \filter_input(INPUT_POST, 'que');

switch ($Qque) {
	case "get":
		$miSfsv = ConfigGlobal::mi_sfsv();
		// listado de tarifas asociadas a tipos de actividad.
		$oGesTipoActivTarifas = new GestorTipoActivTarifa();
		$cTipoActivTarifas = $oGesTipoActivTarifas->getTipoActivTarifas(array('_ordre'=>'substring(id_tipo_activ::text,1)'));
		$i=0;
		$a_cabeceras = [];
		$a_valores = [];
		foreach ($cTipoActivTarifas as $oTipoActivTarifa) {
			$i++;
			$id_item = $oTipoActivTarifa->getId_item();
			$id_tarifa = $oTipoActivTarifa->getId_tarifa();
			$id_tipo_activ = $oTipoActivTarifa->getId_tipo_activ();
			$temporada = $oTipoActivTarifa->getTemporada();
			
			$oTipoActividad = new TiposActividades($id_tipo_activ);
			$nom_tipo=$oTipoActividad->getNom();
			if ($temporada == 'B') {
				$aTipoTemporada = $oTipoActivTarifa->getDatosTemporada()->getArgument();
				$nom_tipo.= " (".$aTipoTemporada[$temporada].")";
			}
			$isfsv=$oTipoActividad->getSfsvId();
			$oTipoTarifa = new TipoTarifa(array('id_tarifa'=>$id_tarifa));

			$modo = $oTipoTarifa->getModo();
			if (!empty($modo)) { $modo_txt=_("total"); } else { $modo_txt=_("por dia"); }
			$tar=$oTipoTarifa->getLetra()."  ($modo_txt)";

			$a_valores[$i][1]=$nom_tipo;
			$a_valores[$i][2]=$tar;
			// permiso
			if ($miSfsv == $isfsv && $_SESSION['oPerm']->have_perm('adl')) {
				$script="fnjs_modificar($id_item)";
				$a_valores[$i][3]=array( 'script'=>$script, 'valor'=> _("modificar"));
			}
		}
		$a_cabeceras[]=_('tipo actividad');
		$a_cabeceras[]=_('tarifa');
		$oLista = new Lista();
		$oLista->setCabeceras($a_cabeceras);
		$oLista->setDatos($a_valores);
		echo $oLista->lista();
		// sólo pueden añadir: adl, pr i actividades
		if (($_SESSION['oPerm']->have_perm("adl")) || ($_SESSION['oPerm']->have_perm("pr")) || ($_SESSION['oPerm']->have_perm("actividades"))) {
		    echo '<br><span class="link" onclick="fnjs_modificar(\'nuevo\');">'._('añadir tarifa tipo').'</span>';
		}
		break;
	case "update":
        $Qid_item = (string)  \filter_input(INPUT_POST, 'id_item');
        $Qid_tarifa = (string)  \filter_input(INPUT_POST, 'id_tarifa');
        $Qtemporada = (string)  \filter_input(INPUT_POST, 'temporada');
        $Qid_tipo_activ = (string)  \filter_input(INPUT_POST, 'id_tipo_activ');

		if ($Qid_item == 'nuevo') {
			$oTipoActivTarifa = new TipoActivTarifa();
		} else {
			$oTipoActivTarifa = new TipoActivTarifa($Qid_item);
			$oTipoActivTarifa->DBCarregar();
		}
		$oTipoActivTarifa->setId_tarifa($Qid_tarifa);
		$oTipoActivTarifa->setTemporada($Qtemporada);
		$oTipoActivTarifa->setId_tipo_activ($Qid_tipo_activ);
		if ($oTipoActivTarifa->DBGuardar() === false) {
			echo _("Hay un error, no se ha guardado");
		}
		break;
	case "eliminar":
        $Qid_item = (string)  \filter_input(INPUT_POST, 'id_item');
		$oTipoActivTarifa = new TipoActivTarifa();
		$oTipoActivTarifa->setId_item($Qid_item);
		if ($oTipoActivTarifa->DBEliminar() === false) {
			echo _("Hay un error, no se ha borrado");
		}
		break;
}

