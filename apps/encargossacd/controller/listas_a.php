<?php
use core\ConfigGlobal;
use encargossacd\model\EncargoFunciones;
use encargossacd\model\entity\GestorEncargo;
use encargossacd\model\entity\GestorEncargoSacd;
use personas\model\entity\Persona;
use ubis\model\entity\GestorCentroDl;
use ubis\model\entity\GestorCentroEllas;
use web\DateTimeLocal;

/* Listado de ateción sacd. según cr 9/05, Anexo2,9.4 a) 
*
*@package	delegacion
*@subpackage	des
*@author	Dani Serrabou
*@since		11/12/06.
*		
*/

// INICIO Cabecera global de URL de controlador *********************************
require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$Qsf = (integer) \filter_input(INPUT_POST, 'sf');

$any=  $_SESSION['oConfig']->any_final_curs('crt');
$inicurs=core\curso_est("inicio",$any,"crt")->getFromLocal();
$fincurs=core\curso_est("fin",$any,"crt")->getFromLocal();

$cabecera_left  = sprintf(_("Curso:  %s - %s"),$inicurs,$fincurs);
$cabecera_right = ConfigGlobal::mi_delef();
$cabecera_right_2 = "ref. cr 1/14, 10, a)";


// ciudad de la dl
$oEncargoFunciones = new EncargoFunciones();
$poblacion = $oEncargoFunciones->getLugar_dl();
$oDateLocal = new DateTimeLocal();
$hoy_local = $oDateLocal->getFromLocal('.');
$lugar_fecha= "$poblacion, $hoy_local";

// primero selecciono los centros por tipos de ctr
if ($Qsf == 1) {
	$tipos_de_ctr = array ('n','a[jm$]','s[jm]');
} else {
	$tipos_de_ctr = array ('n','a[jm$]','s[jm]','ss');
}

$Html='';
$txt_tipo_ctr="";
foreach ($tipos_de_ctr as $tipo_ctr_que) {
	switch ($tipo_ctr_que) {
		case 'n':
			$txt_tipo_ctr=_("1. ctr de n");
			break;
		case 'a[jm$]':
			$txt_tipo_ctr=_("2. ctr de agd");
			break;
		case 's[jm]':
			$txt_tipo_ctr=_("3. ctr de sg");
			break;
		case 'ss':
			$txt_tipo_ctr=_("4. ctr de sss+");
			break;
	}
	if (!empty($txt_tipo_ctr)) $Html .= "<div class=salta_pag><table><tr><td class=grupo colspan=2>$txt_tipo_ctr</td></tr>"; 
		
	$aWhere = array();
	$aOperador = array();
	$aWhere['status'] = 't';
	$aWhere['tipo_ctr'] = "^$tipo_ctr_que";
	$aOperador['tipo_ctr'] = '~';
	$aWhere['_ordre'] = 'nombre_ubi';
	if ($Qsf == 1) {
		$GesCentros = new GestorCentroEllas();
		$cCentros = $GesCentros->getCentros($aWhere,$aOperador);
	} else {
		$GesCentros = new GestorCentroDl();
		$cCentros = $GesCentros->getCentros($aWhere,$aOperador);
	}
	// Bucle por cada centro
	$contador_ctr=0;
	$actual_orden = '';
	//print_r($cCentros);
	foreach ($cCentros as $oCentro) {
		$sacd_titular="";
		$sacd_suplente="";
		$sacd_colaborador="";
		$contador_ctr++;
		//extract($oCentro->getTot());
		$id_ubi = $oCentro->getId_ubi();
		$nombre_ubi = $oCentro->getNombre_ubi();
		$tipo_ctr = $oCentro->getTipo_ctr();
		$GesEncargo = new GestorEncargo();
		$aWhere = array();
		$aOperador = array();
		$aWhere['id_ubi'] = $id_ubi;
		$aWhere['id_tipo_enc'] = '1[0123]0.';
		$aOperador['id_tipo_enc'] = '~';
		$cEncargos = $GesEncargo->getEncargos($aWhere,$aOperador);
		foreach ($cEncargos as $oEncargo) {
			$id_enc =  $oEncargo->getId_enc();
			$id_tipo_enc =  $oEncargo->getId_tipo_enc();
			/*
			$sacd_titular="";
			$sacd_suplente="";
			$sacd_colaborador="";
			*/
			$GesTareasSacd = new GestorEncargoSacd();
			$aWhereT['id_enc'] = $id_enc;
			$aWhereT['f_fin'] = 'null';
			$aOperadorT['f_fin'] = 'IS NULL';
			$aWhereT['_ordre'] = 'modo';
			$cTareasSacd = $GesTareasSacd->getTareasSacd($aWhereT,$aOperadorT);
			$s=0;
			foreach ($cTareasSacd as $oTareaSacd) {
				$s++;
				$modo = $oTareaSacd->getModo();
				$id_nom = $oTareaSacd->getId_nom();
				$oPersona = Persona::NewPersona($id_nom);
				$nom_ap = $oPersona->getNombreApellidosCrSin();
				if ($id_tipo_enc == '1101') { // para las meditaciones, es colaborador
						$sacd_colaborador.="<br>".$nom_ap;
				} else {
					switch ($modo) {
						case 2:
							// para los centros de estudio, añado: '(dre)'
							if ($tipo_ctr=="njce") { 
								$sacd_titular=sprintf("%s (%s)",$nom_ap,_("dre"));
							} else {
								$sacd_titular=$nom_ap;
							}
							break;
						case 3:
							if ($tipo_ctr=="ss") {
								$parentesis=_("confesor");
							} else {
								$parentesis=_("no cl");
							}
							if ($Qsf == 1) {
								// para los centros de estudio, añado: '(dre)'
								if ($tipo_ctr=="njce") { 
									$sacd_titular=sprintf("%s (%s)",$nom_ap,_("dre"));
								} else {
									$sacd_titular=$nom_ap;
								}
							} else {
								$sacd_titular=sprintf("%s (%s)",$nom_ap,$parentesis);
							}
							break;
						case 4:
							$sacd_suplente=$nom_ap;
							break;
						case 5:
							if (!$sacd_suplente && !$sacd_colaborador) {
								$sacd_colaborador=$nom_ap;
							} else {
								$sacd_colaborador.="<br>".$nom_ap;
							}
							break;
					}
				}
			}
		}
		$Html .= "<tr><td class=centro>$nombre_ubi</td></tr>
			<tr><td>$sacd_titular</td><td>";
		if (!empty($sacd_suplente)) $Html .= "<span class=suplente>$sacd_suplente</span>";
		if (!empty($sacd_colaborador)) $Html .= "$sacd_colaborador";
		$Html .= "</td></tr>";
	}
	$Html .= "</table></div>";
}

/*
if (empty($Qsf)) {
	// Añadir los sacd que trabajan en la dl y en que departamento.
	$GesCargoCl = new GestorCargoCl();
	$cCargosCl = $GesCargoCl->getCargosCl(array('elencum'=>'8/6','f_cese'=>'null','_ordre'=>'cargo'),array('f_cese'=>'IS NULL'));
	$Html .= "<div class=salta_pag><table><tr><td class=grupo colspan=2>"._("5. Oficiales de dl")."</td></tr><tr><td><br></td></tr>"; 
	foreach($cCargosCl as $oCargoCl) {
		$id_nom = $oCargoCl->getId_nom();
		$cargo = $oCargoCl->getCargo();
		$oPersona = Persona::newPersona($id_nom);
		$sacd = $oPersona->getSacd();
		if ($sacd === false) continue; // sólo listo a los sacd.
		$nom_ap = $oPersona->getNombreApellidosCrSin();
		$sacd_titular="$nom_ap ($cargo)";
		$Html .= "<tr><td>$sacd_titular</td></tr>";
	}
	$Html .= "<tr><td class=izquierda></td><td class=derecha>$lugar_fecha</td></tr>";
	$Html .= "</table></div>";
}
*/

$Html .= "<table>";
$Html .= "<tr><td class=izquierda></td><td class=derecha>$lugar_fecha</td></tr>";
$Html .= "</table>";

$a_campos = ['oPosicion' => $oPosicion,
    'cabecera_left' => $cabecera_left,
    'cabecera_right' => $cabecera_right,
    'cabecera_right_2' => $cabecera_right_2,
    'Html' => $Html,
];

$oView = new core\ViewTwig('encargossacd/controller');
echo $oView->render('listas.html.twig',$a_campos);
