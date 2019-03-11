<?php
use core\ConfigGlobal;
use personas\model\entity\GestorPersona;
use personas\model\entity\PersonaDl;
use web\Lista;
use zonassacd\model\entity\GestorZonaSacd;
use zonassacd\model\entity\Zona;
use zonassacd\model\entity\ZonaSacd;

/**
* Esta página sirve ver todos los sacd con sus zonas. También los sacd de paso.
* Ejecuta las operaciones (get_lista,update) que le pide zona_sacd.php
* También Se llama desde el menu des->zonas/colatios->lista sacd-zona
*
*@package	delegacion
*@subpackage	des
*@author	Daniel Serrabou
*@since		22/9/09.
*		
*/

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
$Qacumular = (integer) \filter_input(INPUT_POST, 'acumular');

$QAsel = filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

switch($Qque) {
	case 'get_lista':
		if ($Qid_zona == "no") { // los que no tienen ninguna zona asignada.
			$GesSacd = new GestorPersona();
            // Para los de la dl y de_paso:
            $aClases = [];
            $aClases[] = array('clase'=>'PersonaDl','get'=>'getPersonasDl');
            $aClases[] = array('clase'=>'PersonaEx','get'=>'getPersonasEx');
            $GesSacd->setClases($aClases);
            
			$mi_dl = ConfigGlobal::mi_dele();
            $aWhere = [];
            $aWhere['sacd'] = 't';
            $aWhere['situacion'] = 'A';
            $aWhere['dl'] = $mi_dl;
            $aWhere['_ordre'] = 'apellido1,apellido2,nom';
			$cSacds = $GesSacd->getPersonas($aWhere);
			$i=0;
			$a_valores = [];
			foreach ($cSacds as $oPersona) {
				$id_nom = $oPersona->getId_nom();
				$ap_nom = $oPersona->getApellidosNombre();
				$GesZonasSacd = new GestorZonaSacd();
				$cZonaSacd = $GesZonasSacd->getZonasSacds(array('id_nom'=>$id_nom));
				$a_zonas=array();
				if (is_array($cZonaSacd) && count($cZonaSacd) < 1) {
					$a_valores[$i]['sel']=$id_nom;
					$a_valores[$i][1]=$ap_nom;
					$a_valores[$i][2]=$oPersona->getId_tabla();
					$i++;
				} else {
					continue;
				}
			}
		} else {
		    $aWhere = [];
			$aWhere['id_zona']=$Qid_zona;
			$aOperador=array();
			$oZona = new Zona($Qid_zona);
			$nombre_zona = $oZona->getNombre_zona();
			$GesZonasSacd = new GestorZonaSacd();
			$cZonaSacd = $GesZonasSacd->getZonasSacds($aWhere,$aOperador);
			$a_sacds=array();
			foreach ($cZonaSacd as $oZonaSacd) {
				$id_nom = $oZonaSacd->getId_nom();
				$oPersona = new PersonaDl($id_nom);
				if ($oPersona->getSituacion() != 'A') continue;
				if ($oPersona->getDl() != ConfigGlobal::mi_dele()) continue;
				$ap_nom = $oPersona->getApellidosNombre();

				$a_sacds[$ap_nom]=$id_nom;
			}
			$a_valores=array();
			if (is_array($a_sacds) && count($a_sacds)>=1) {
				ksort($a_sacds);
				$i=0;
				foreach ($a_sacds as $ap_nom=>$id_nom) {
					$a_valores[$i]['sel']=$id_nom;
					$a_valores[$i][1]=$ap_nom;
					$a_valores[$i][2]=$nombre_zona;
					$a_valores[$i][3]=$oZonaSacd->getPropia();
					$i++;
				}
			}
		}
		$a_cabeceras=array( _("sacd"), _("zona") , _("propia"));
		$a_botones='ninguno';
		/* ---------------------------------- html --------------------------------------- */
		$oTabla = new Lista();
		$oTabla->setId_tabla('zona_sacd_ajax');
		$oTabla->setCabeceras($a_cabeceras);
		$oTabla->setBotones($a_botones);
		$oTabla->setDatos($a_valores);
		echo $oTabla->mostrar_tabla();
		break;
	case 'get_lista_tot':
		$GesSacd = new GestorPersona();
		// Para los de la dl y de_paso:
		$aClases = [];
		$aClases[] = array('clase'=>'PersonaDl','get'=>'getPersonasDl');
		$aClases[] = array('clase'=>'PersonaEx','get'=>'getPersonasEx');
		$GesSacd->setClases($aClases);
		
        $mi_dl = ConfigGlobal::mi_dele();
        $aWhere = [];
        $aWhere['sacd'] = 't';
        $aWhere['dl'] = $mi_dl;
        $aWhere['_ordre'] = 'apellido1,apellido2,nom';
		$cSacds = $GesSacd->getPersonas($aWhere);
		$i=0;
		foreach ($cSacds as $oPersona) {
			$id_nom = $oPersona->getId_nom();
			$ap_nom = $oPersona->getApellidosNombre();
			$GesZonasSacd = new GestorZonaSacd();
			$cZonaSacd = $GesZonasSacd->getZonasSacds(array('id_nom'=>$id_nom));
			$a_zonas=array();
			foreach ($cZonaSacd as $oZonaSacd) {
				$id_zona = $oZonaSacd->getId_zona();
				$propia = $oZonaSacd->getPropia();
				$oZona = new Zona($id_zona);
				$nombre_zona = $oZona->getNombre_zona();
				if ($propia === true) {
					$orden = 0;
				} else {
					$orden = $oZona->getOrden();
				}
				$a_zonas[$orden]=array($nombre_zona,$propia);
			}
			if (count($a_zonas)>=1) {
				ksort($a_zonas);
				foreach ($a_zonas as $a_zona) {
					$a_valores[$i][1]=$ap_nom;
					$a_valores[$i][2]=$a_zona[0];
					$a_valores[$i][3]=empty($a_zona[1])? _("no") : _("si");
					$i++;
				}
			} else {
					$a_valores[$i][1]=$ap_nom;
					$a_valores[$i][2]='';
					$a_valores[$i][3]='';
			}
			$i++;
		}
		$a_cabeceras=array( _("sacd"), _("zona") , _("propia"));

		/* ---------------------------------- html --------------------------------------- */
		$oTabla = new Lista();
		$oTabla->setCabeceras($a_cabeceras);
		$oTabla->setDatos($a_valores);
		echo $oTabla->lista();
		break;
	case 'update':
		if (!empty($Qid_zona_new)) {
			if ($Qid_zona_new == "no") { $id_zona_new=""; } else { $id_zona_new=$Qid_zona_new; }
			foreach($QAsel as $id_nom) {
				if ($Qacumular == 2) {
					if (empty($id_zona_new)) {
						$oZonaSacd = new ZonaSacd();
						$oZonaSacd->setId_nom($id_nom);
						$oZonaSacd->setId_zona($Qid_zona);
						$oZonaSacd->DBCarregar(); // para coger el id_item.
						if ($oZonaSacd->DBEliminar() === false) {
							echo _("hay un error, no se ha eliminado");
						}
					} else {
						$oZonaSacd = new ZonaSacd();
						$oZonaSacd->setId_nom($id_nom);
						$oZonaSacd->setId_zona($id_zona_new);
						$oZonaSacd->setPropia('f');
						$oZonaSacd->DBCarregar(); // para coger el id_item.
						if ($oZonaSacd->DBGuardar() === false) {
							echo _("hay un error, no se ha guardado");
						}
					}
				} else {
					// Si el id_zona es 0, son nuevos: hay que hacer insert.
					if ($Qid_zona==0) {
						$oZonaSacd = new ZonaSacd();
						$oZonaSacd->setId_nom($id_nom);
						$oZonaSacd->setId_zona($id_zona_new);
						$oZonaSacd->setPropia('t');
						if ($oZonaSacd->DBGuardar() === false) {
							echo _("hay un error, no se ha guardado");
						}
					} elseif (empty($id_zona_new)) {
						$oZonaSacd = new ZonaSacd();
						$oZonaSacd->setId_nom($id_nom);
						$oZonaSacd->setId_zona($Qid_zona);
						$oZonaSacd->DBCarregar(); // para coger el id_item.
						if ($oZonaSacd->DBEliminar() === false) {
							echo _("hay un error, no se ha eliminado");
						}
					} else {
						$oZonaSacd = new ZonaSacd();
						$oZonaSacd->setId_nom($id_nom);
						$oZonaSacd->setId_zona($Qid_zona);
						$oZonaSacd->DBCarregar();
						$oZonaSacd->setId_zona($id_zona_new);
						$oZonaSacd->setPropia('t');
						if ($oZonaSacd->DBGuardar() === false) {
							echo _("hay un error, no se ha guardado");
						}
					}
				}
			}
		}
		break;
}
