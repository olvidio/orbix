<?php 
use actividades\model as actividades;
use actividadcargos\model as actividadcargos;
use asistentes\model as asistentes;
use personas\model as personas;
use ubis\model as ubis;

/**
* Lista los asistentes de una relación de actividades seleccionada
*
* 
*
*@package	delegacion
*@subpackage	actividades
*@author	Daniel Serrabou
*@since		15/5/02.
*		
*/
/**
* Funciones más comunes de la aplicación
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qstatus = empty($_POST['status'])? 2 : $_POST['status'];
$Qid_tipo_activ = empty($_POST['id_tipo_activ'])? '' : $_POST['id_tipo_activ'];
$Qid_ubi = empty($_POST['id_ubi'])? '' : $_POST['id_ubi'];
$Qperiodo = empty($_POST['periodo'])? '' : $_POST['periodo'];
$Qinicio = empty($_POST['inicio'])? '' : $_POST['inicio'];
$Qfin = empty($_POST['fin'])? '' : $_POST['fin'];
$Qyear = empty($_POST['year'])? (integer) date('Y') : $_POST['year'];
$Qdl_org = empty($_POST['dl_org'])? '' : $_POST['dl_org'];
$Qempiezamin = empty($_POST['empiezamin'])? date('d/m/Y',mktime(0, 0, 0, date('m'), date('d')-40, date('Y'))) : $_POST['empiezamin'];
$Qempiezamax = empty($_POST['empiezamax'])? date('d/m/Y',mktime(0, 0, 0, date('m')+9, 0, date('Y'))) : $_POST['empiezamax'];
	
// Condiciones de búsqueda.
$aWhere = array();
// Status
if ($Qstatus!=5) {
	$aWhere['status'] = $Qstatus;
}
// Id tipo actividad
if (empty($Qid_tipo_activ)) {
	if (empty($_POST['ssfsv'])) {
		if ($mi_sfsv == 1) $_POST['ssfsv'] = 'sv';
		if ($mi_sfsv == 2) $_POST['ssfsv'] = 'sf';
	}
	$ssfsv = $_POST['ssfsv'];
	$sasistentes = empty($_POST['sasistentes'])? '.' : $_POST['sasistentes'];
	$sactividad = empty($_POST['sactividad'])? '.' : $_POST['sactividad'];
	$snom_tipo = empty($_POST['snom_tipo'])? '...' : $_POST['snom_tipo'];
	$oTipoActiv= new web\TiposActividades();
	$oTipoActiv->setSfsvText($ssfsv);
	$oTipoActiv->setAsistentesText($sasistentes);
	$oTipoActiv->setActividadText($sactividad);
	$Qid_tipo_activ=$oTipoActiv->getId_tipo_activ();
} else {
	$oTipoActiv= new web\TiposActividades($Qid_tipo_activ);
	$ssfsv=$oTipoActiv->getSfsvText();
	$sasistentes=$oTipoActiv->getAsistentesText();
	$sactividad=$oTipoActiv->getActividadText();
	$nom_tipo=$oTipoActiv->getNom_tipoText();
}
if ($Qid_tipo_activ!='......') {
	$aWhere['id_tipo_activ'] = "^$Qid_tipo_activ";
	$aOperador['id_tipo_activ'] = '~';
} 
// Lugar
if (!empty($Qid_ubi)) {
	$aWhere['id_ubi']=$Qid_ubi;
}
// periodo.
if (empty($Qperiodo) || $Qperiodo == 'otro') {
	$Qinicio = empty($Qinicio)? $Qempiezamin : $Qinicio;
	$Qfin = empty($Qfin)? $Qempiezamax : $Qfin;
} else {
	$oPeriodo = new web\Periodo();
	$any=empty($Qyear)? date('Y')+1 : $Qyear;
	$oPeriodo->setAny($any);
	$oPeriodo->setPeriodo($Qperiodo);
	$Qinicio = $oPeriodo->getF_ini();
	$Qfin = $oPeriodo->getF_fin();
}
if (!empty($Qperiodo) && $Qperiodo == 'desdeHoy') {
	$aWhere['f_fin'] = "'$Qinicio','$Qfin'";
	$aOperador['f_fin'] = 'BETWEEN';
} else {
	$aWhere['f_ini'] = "'$Qinicio','$Qfin'";
	$aOperador['f_ini'] = 'BETWEEN';
}
// dl Organizadora.
if (!empty($Qdl_org)) {
   $aWhere['dl_org'] = $Qdl_org; 
}
// Publicar
if (!empty($Qmodo) && $Qmodo == 'publicar') {
   $aWhere['publicado'] = 'f'; 
}

$aWhere['_ordre'] = 'f_ini';
$GesActividades = new actividades\GestorActividad();
$cActividades = $GesActividades->getActividades($aWhere,$aOperador);

if (is_array($cActividades) && count($cActividades) < 1) { echo core\strtoupper_dlb(_("No existe ninguna actividad con esta condición.")); exit;}

if (($sasistentes=="s") AND ($sactividad=="cv")) { 
	$titulo = core\strtoupper_dlb(_("relación de cargos en las cv de s"));
} else {
	if (($sasistentes=="sss+") AND ($sactividad=="cv")) {
		$titulo = strtoupper(_("propuesta de cl en cv de sss+"));
	} else {
		$titulo = core\strtoupper_dlb(_("relación de asistentes a las actividades seleccionadas"));
	} 
}

$mi_dele = core\ConfigGlobal::mi_dele();
$k=0;
$centros='';
$aGrupos=array();
$msg_err = '';
foreach ($cActividades as $oActividad) {
	$k++;  // recorro todas las actividades seleccionadas, utilizo el contador k
	$id_activ=$oActividad->getId_activ();
	$nom_activ=$oActividad->getNom_activ();
	$observ=$oActividad->getObserv();
	$dl_org_activ=$oActividad->getDl_org();
	$id_ubi_casa=$oActividad->getId_ubi();
	$plazas=$oActividad->getPlazas();

	// Plazas
	$plazas_max = '';
	$plazas_min = '';
	$plazas_txt = '';
	if (!empty($id_ubi_casa)) {
		$oCasaDl = new ubis\Casa($id_ubi_casa);
		$plazas_max = !empty($plazas)? $plazas : $oCasaDl->getPlazas();
		$plazas_min = $oCasaDl->getPlazas_min();
		$plazas_txt = _("Plazas").": ";
		$plazas_txt .= !empty($plazas_max)? $plazas_max : '';
		$plazas_txt .= !empty($plazas_min)? ' - '.$plazas_min : '';
	}


	$id_pau=$id_activ;
	$txt_ctr='';
	if (core\configGlobal::is_app_installed('actividadcentros')) {
		if ((($sasistentes=="s") OR ($sasistentes=="sss+")) AND ($sactividad=="cv")) {
			// para las cv de s y de sss+ consulto los ctr que organizan 
			$oGesEncargados = new GestorCentroEncargado();
			$cCtrsEncargados = $oGesEncargados->getCentrosEncargados(array('id_activ'=>$id_activ,'_ordre'=>'num_orden'));

			$c=0;
			foreach ($cCtrsEncargados as $oCentroEncargado) {
				$c++;
				$num_orden=$oCentroEncargado->getNum_orden();
				$id_ubi=$oCentroEncargado->getId_ubi();
				$Centro = new ubis\Ubi($id_ubi);
				$ctr=$Centro->getNombre_ubi();
				if ($c > 1) $txt_ctr .= '; ';
				$txt_ctr.= $ctr;
			}
			//$a_activ[$id_activ]['ctr_encargados']=$txt_ctr;
		}
	}
	$nom_activ = empty($txt_ctr)? $nom_activ : "$nom_activ [$txt_ctr]";
	$nom_activ = empty($observ)? $nom_activ : "$nom_activ $observ";

	if (!($sasistentes=="sss+" AND $sactividad=="cv")) {
		if (core\configGlobal::is_app_installed('actividadcargos')) {
			//selecciono el cl
			$oGesActividadCargos = new actividadcargos\GestorActividadCargo();
			$cActividadCargos = $oGesActividadCargos->getActividadCargos(array('id_activ'=>$id_pau));
			$cl=0;
			$num=0; //número total de asistentes
			$aIdCargos=array(); // id_nom de los cargos para no ponerlos como asistentes.
			foreach ($cActividadCargos as $oActividadCargo) {
				$cl++;
				$num++;
				$id_nom = $oActividadCargo->getId_nom();
				$aIdCargos[] = $id_nom;
				$id_cargo = $oActividadCargo->getId_cargo();
				$oCargo = new actividadcargos\Cargo($id_cargo);
				$cargo_cl = $oCargo->getCargo();
				$oPersona = personas\Persona::NewPersona($id_nom);
				if (!is_object($oPersona)) {
					$msg_err .= "<br>$oPersona con id_nom: $id_nom";
					continue;
				}
				$id_tabla = $oPersona->getId_tabla();
				$ap_nom = $oPersona->getApellidosNombre();
				$ctr_dl = $oPersona->getCentro_o_dl();

				// ahora miro si también asiste:
				$oGesAsistentes = new asistentes\GestorAsistente();
				$cAsistentes  = $oGesAsistentes->getAsistentes(array('id_activ'=>$id_pau,'id_nom'=>$id_nom));
				
				if (is_array($cAsistentes) && count($cAsistentes) > 0) {
					$asis="t";
					$texto="";
				} else {
					$texto="No asiste";
					$asis="f";
				}
				$a_activ[$id_activ][$num]['cargo']=$cargo_cl;
				$a_activ[$id_activ][$num]['ap_nom']="$ap_nom ($ctr_dl)";
				$a_activ[$id_activ][$num]['texto']=$texto;
			}
		}

		$oGesAsistentes = new asistentes\GestorAsistente();
		$cAsistentes = $oGesAsistentes->getAsistentesDeActividad($id_pau);
		foreach ($cAsistentes as $oAsistente) {
			$id_nom=$oAsistente->getId_nom();
			if (in_array($id_nom,$aIdCargos)) continue; // si ya está como cargo, no lo pongo.
			// Sólo apunto los asignados/confirmados
			if (core\configGlobal::is_app_installed('actividadplazas')) {
				if($oAsistente->getPlaza() < asistentes\Asistente::PLAZA_ASIGNADA) {
					continue;
				}
			}
			$oPersona = personas\Persona::NewPersona($id_nom);
			if (!is_object($oPersona)) {
				$msg_err .= "<br>$oPersona con id_nom: $id_nom";
				continue;
			}
			$id_tabla = $oPersona->getId_tabla();
			$ap_nom = $oPersona->getApellidosNombre();
			$ctr_dl = $oPersona->getCentro_o_dl();
			$dl = $oPersona->getDl();

			// Si no organizo, no veo a los que no son de la dl.
			if ($dl_org_activ != $mi_dele) {
				if ($dl != $mi_dele) {
					continue;
				}
			}
			$num++;
			$a_activ[$id_activ][$num]['cargo']=$num;
			$a_activ[$id_activ][$num]['ap_nom']="$ap_nom ($ctr_dl)";
		}
	}
	if (!empty($plazas_max) && $num > $plazas_max) {
		$num_txt = "<span style=\"color: red;\">$num</span>";
	} else {
		$num_txt = $num;
	}
	$aGrupos[$id_activ]= empty($plazas_max)? $nom_activ : "$nom_activ; $plazas_txt, "._("ocupadas").": $num_txt";
}

$a_cabeceras[]= _("num");
$a_cabeceras[]= _("nombre");

$oLista = new web\Lista();
$oLista->setGrupos($aGrupos);
$oLista->setCabeceras($a_cabeceras);
$oLista->setDatos($a_activ);
echo $oLista->listaPaginada();
if (!empty($msg_err)) { echo $msg_err; }
?>
