<?php 
use ubis\model\entity as ubis;
/**
* Formulario para ctr de los listados de profesión y de los asistentes a actividades
*
* Debe pasársele, mediante menú, el contenido de $lista para que haga el link
* correspondiente
*
*@package	delegacion
*@subpackage	personas
*@author	Josep Companys
*@since		15/5/02.
*		
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$tipo = (string) \filter_input(INPUT_POST, 'tipo');
$ssfsv = (string) \filter_input(INPUT_POST, 'ssfsv');
$Qlista = (string) \filter_input(INPUT_POST, 'lista');
$Qsasistentes = (string) \filter_input(INPUT_POST, 'sasistentes');
$Qsactividad = (string) \filter_input(INPUT_POST, 'sactividad');
$Qn_agd = (string) \filter_input(INPUT_POST, 'n_agd');
$Qid_ubi = (integer) \filter_input(INPUT_POST, 'id_ubi');
$Qyear = (integer) \filter_input(INPUT_POST, 'year');
$Qperiodo = (string) \filter_input(INPUT_POST, 'periodo');

switch ($Qlista) {
	case "profesion" :
		$tituloGros=ucfirst(_("listado de profesiones por centros"));
		$titulo=ucfirst(_("buscar en uno o varios centros"));
		$nomUbi=ucfirst(_("nombre del centro"));
		$action="programas/sm-agd/lista_profesion.php";
		$a_camposHidden = array(
			'tipo' => $tipo
		);
		break;
	case "ctrex" :
	case "list_activ" :
		$titulo=ucfirst(_("actividades de personas por centros de la delegación"));
		$tituloGros=ucfirst(_("¿qué centro interesa?"));
		$nomUbi=ucfirst(_("nombre del centro"));
		$action="apps/asistentes/controller/lista_activ_ctr.php";
		
		$a_camposHidden = array(
			'tipo' => $tipo,
			'ssfsv' => $ssfsv,
			'sasistentes' => $Qsasistentes,
			'sactividad' => $Qsactividad
		);
		break;
	case "list_est" :
		$titulo=ucfirst(_("estudios en actividades de personas por centros de la delegación"));
		$tituloGros=ucfirst(_("¿qué centro interesa?"));
		$nomUbi=ucfirst(_("nombre del centro"));
		$action="apps/asistentes/controller/lista_est_ctr.php";
		$a_camposHidden = array(
			'tipo' => $tipo,
			'ssfsv' => $ssfsv,
			'sasistentes' => $Qsasistentes,
			'sactividad' => $Qsactividad
		);
		break;
}


$n='';
$nj='';
$nm='';
$a='';
$sssc='';
$nax='';
$c='';

switch ($Qn_agd) {
	case "n":
		$n="checked";
		break;
	case "nj":
		$nj="checked";
		break;
	case "nm":
		$nm="checked";
		break;
	case "a":
		$a="checked";
		break;
	case "sssc":
		$sssc="checked";
		break;
	case "nax":
		$nax="checked";
		break;
	case "c":
		$c="checked";
		break;
}

$oGesCentros= new ubis\GestorCentroDl();
$oDesplCentros = $oGesCentros->getListaCentros("WHERE status = 't' AND tipo_ctr ~ '^a|^n' ");
$oDesplCentros->setNombre('id_ubi');
$oDesplCentros->setBlanco(true);
$oDesplCentros->setOpcion_sel($Qid_ubi);
$oDesplCentros->setAction('fnjs_otro(1)');

$oHash = new web\Hash();
$oHash->setcamposForm('n_agd!empiezamax!empiezamin!periodo!year!iactividad_val!iasistentes_val');
$oHash->setcamposNo('id_ubi');
$oHash->setArraycamposHidden($a_camposHidden);

$oFormP = array();
if ($Qlista=="list_activ" || $Qlista=="list_est") {
	$aOpciones =  array(
						'curso_ca'=>_("curso ca"),
						'curso_crt'=>_("curso crt"),
						'tot_any' => _("todo el año"),
						'separador'=>'---------',
						'otro'=>_("otro")
						);
	$oFormP = new web\PeriodoQue();
	$oFormP->setFormName('modifica');
	$oFormP->setTitulo(core\strtoupper_dlb(_("periodo de inicio o finalización de las actividades")));
	$oFormP->setPosiblesPeriodos($aOpciones);
	switch ($Qsactividad) {
		case 'ca':
			$oFormP->setDesplPeriodosOpcion_sel('curso_ca');
			break;
		case 'crt':
			$oFormP->setDesplPeriodosOpcion_sel('curso_crt');
			break;
		default:
			$oFormP->setDesplPeriodosOpcion_sel('tot_any');
			break;
	}
	if (!empty($Qperiodo)) {
		$oFormP->setDesplPeriodosOpcion_sel($Qperiodo);
	}
	if (!empty($Qyear)) {
		$oFormP->setDesplAnysOpcion_sel($Qyear);
	} else {
		$oFormP->setDesplAnysOpcion_sel(date('Y'));
	}
}

$a_campos = [
			'tituloGros' => $tituloGros,
			'action' => $action,
			'oHash' => $oHash,
			'titulo' => $titulo,
			'n' => $n,
			'nj' => $nj,
			'nm' => $nm,
			'a' => $a,
			'sssc' => $sssc,
			'nax' => $nax,
			'c' => $c,
			'oDesplCentros' => $oDesplCentros,
			'oFormP' => $oFormP,
			];

$oView = new core\View('asistentes/controller');
echo $oView->render('que_ctr_lista.phtml',$a_campos);
