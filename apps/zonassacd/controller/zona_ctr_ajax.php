<?php
use ubis\model\entity\CentroDl;
use ubis\model\entity\GestorCentroDl;
use web\Lista;
use zonassacd\model\entity\Zona;
use ubis\model\entity\GestorCentroEllas;
use ubis\model\entity\CentroEllas;
/**
* Esta página devuelve una tabla con los nombres de los centros que perteneces a una zona de Misas.
* Se llama desde zona_ctr.php
*
*@param string $que  	'update'
*						'get_lista'(lista de los ctrs de una zona)
*@param integer|string $id_zona  puede ser:
*										-'no': los ctr que no tiene zona asignada.
*										-'no_sf': los ctr de sf que no tiene zona asignada.
*										- id_zona: Una zona en concreto.
*
*@package	delegacion
*@subpackage	des
*@author	Daniel Serrabou
*@since		16/11/06.
*		
*/

// INICIO Cabecera global de URL de controlador *********************************

require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$Qque = (string) \filter_input(INPUT_POST, 'que');
//id_zona es string, porque admite los valores "no" y "no_sf"
$Qid_zona = (string) \filter_input(INPUT_POST, 'id_zona');
//id_zona_new es string, porque admite los valores "no"
$Qid_zona_new = (string) \filter_input(INPUT_POST, 'id_zona_new');

$QAsel = filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

switch($Qque) {
	case 'get_lista':
	    $aWhere = [];
	    $aOperador = [];
	    $cCentros = [];
		/*miro las condiciones. */
		switch($Qid_zona) {
			case "no":
				$aWhere['status']='t';
				$aWhere['id_zona']='';
				$aOperador['id_zona']='IS NULL';
				$aWhere['_ordre']='nombre_ubi';
				$GesCentros = new GestorCentroDl();
				$cCentros = $GesCentros->getCentros($aWhere,$aOperador);
				break;
			case "no_sf":
				$aWhere['status']='t';
				$aWhere['id_zona']='';
				$aOperador['id_zona']='IS NULL';
				$aWhere['_ordre']='nombre_ubi';
				$GesCentros = new GestorCentroEllas();
				$cCentros = $GesCentros->getCentros($aWhere,$aOperador);
				break;
			default:
				$aWhere['status']='t';
				$aWhere['id_zona']=$Qid_zona;
				$aWhere['_ordre']='nombre_ubi';
				$GesCentrosDl = new GestorCentroDl();
				$cCentrosDl = $GesCentrosDl->getCentros($aWhere);
				$GesCentrosSf = new GestorCentroEllas();
				$cCentrosSf = $GesCentrosSf->getCentros($aWhere);
				$cCentros = $cCentrosDl + $cCentrosSf;
		}

		$a_botones="ninguno";
		$a_cabeceras=array( _("centro"), _("zona") );

		$i=0;
		$a_valores=array();
		foreach ($cCentros as $oCentro) {
			$i++;
			$id_ubi="{$oCentro->getId_ubi()}"; // Para que lo coja como un string.
			if ($id_ubi[0]==2) {
				if (($_SESSION['oPerm']->have_perm("des")) or ($_SESSION['oPerm']->have_perm("vcsd"))) {
					$a_valores[$i]['clase']="sf";
				} else {
					continue;
				}
			}
			$id_zona=$oCentro->getId_zona();
			$oZona = new Zona($id_zona);
			$a_valores[$i]['sel']=$id_ubi;
			$a_valores[$i][1]= $oCentro->getNombre_ubi();
			$a_valores[$i][2]= $oZona->getNombre_zona();
		}

		/* ---------------------------------- html --------------------------------------- */
		$oTabla = new Lista();
		$oTabla->setId_tabla('zona_ctr_ajax');
		$oTabla->setCabeceras($a_cabeceras);
		$oTabla->setBotones($a_botones);
		$oTabla->setDatos($a_valores);
		echo $oTabla->mostrar_tabla();
		break;
	case 'update':
		if (!empty($Qid_zona_new)) {
			if ($Qid_zona_new == "no") { $id_zona_new=""; } else { $id_zona_new = $Qid_zona_new; }
			foreach($QAsel as $id_ubi) {
				$id_ubi="{$id_ubi}"; // Para asegurarme que lo toma como string.
				if ($id_ubi[0]==1) { $oCentro = new CentroDl($id_ubi); }
				if ($id_ubi[0]==2) { $oCentro = new CentroEllas($id_ubi); }
				$oCentro->DBCarregar();
				$oCentro->setId_zona($id_zona_new);
				if ($oCentro->DBGuardar() === false) {
					echo _("hay un error, no se ha guardado.");
				}
			}
		} else {
			foreach($QAsel as $id_ubi) {
				$id_ubi="{$id_ubi}"; // Para asegurarme que lo toma como string.
				if ($id_ubi[0]==1) { $oCentro = new CentroDl($id_ubi); }
				if ($id_ubi[0]==2) { $oCentro = new CentroEllas($id_ubi); }
				$oCentro->DBCarregar();
				$oCentro->setId_zona('');
				if ($oCentro->DBGuardar() === false) {
					echo _("hay un error, no se ha guardado.");
				}
			}
		}
	break;
}
