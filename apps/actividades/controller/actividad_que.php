<?php
/**
* Esta página muestra un formulario con las opciones para escoger la actividad.
*
* Se le pasan las var:
*
*@package	delegacion
*@subpackage	actividades
*@author	Daniel Serrabou
*@since		15/5/02.
*@ajax		21/8/2007.
*		
*/

use core\ConfigGlobal;
use ubis\model\entity as ubis;
use usuarios\model\entity\Usuario;
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************
	
$oPosicion->recordar();

//Si vengo de vuelta y le paso la referecia del stack donde está la información.
if (isset($_POST['stack'])) {
	$stack = \filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
	if ($stack != '') {
		// No me sirve el de global_object, sino el de la session
		$oPosicion2 = new web\Posicion();
		if ($oPosicion2->goStack($stack)) { // devuelve false si no puede ir
			$Qid_sel=$oPosicion2->getParametro('id_sel');
			$Qscroll_id = $oPosicion2->getParametro('scroll_id');
			$oPosicion2->olvidar($stack);
		}
	}
}

$Qmodo = (string) \filter_input(INPUT_POST, 'modo');
$Qque = (string) \filter_input(INPUT_POST, 'que');
$Qstatus = (integer) \filter_input(INPUT_POST, 'status');
$Qid_tipo_activ = (integer) \filter_input(INPUT_POST, 'id_tipo_activ');
$Qfiltro_lugar = (string) \filter_input(INPUT_POST, 'filtro_lugar');
$Qid_ubi = (integer) \filter_input(INPUT_POST, 'id_ubi');
$Qperiodo = (string) \filter_input(INPUT_POST, 'periodo');
//$Qinicio = (string) \filter_input(INPUT_POST, 'inicio');
//$Qfin = (string) \filter_input(INPUT_POST, 'fin');
$Qyear = (string) \filter_input(INPUT_POST, 'year');
$Qdl_org = (string) \filter_input(INPUT_POST, 'dl_org');
$Qempiezamax = (string) \filter_input(INPUT_POST, 'empiezamax');
$Qempiezamin = (string) \filter_input(INPUT_POST, 'empiezamin');
$Qlistar_asistentes = (string) \filter_input(INPUT_POST, 'listar_asistentes');

$isfsv=core\ConfigGlobal::mi_sfsv();
$permiso_des = FALSE;
if (($_SESSION['oPerm']->have_perm("vcsd")) or ($_SESSION['oPerm']->have_perm("des"))) {
    $permiso_des = TRUE;
    $ssfsv = '';
} else {
    if ($isfsv == 1) $ssfsv = 'sv';
    if ($isfsv == 2) $ssfsv = 'sf';
}

$Qsasistentes = (string) \filter_input(INPUT_POST, 'sasistentes');
$Qsactividad = (string) \filter_input(INPUT_POST, 'sactividad');
$Qsnom_tipo = (string) \filter_input(INPUT_POST, 'snom_tipo');

$oActividadTipo = new actividades\model\ActividadTipo();
$oActividadTipo->setPerm_jefe($permiso_des);
$oActividadTipo->setId_tipo_activ($Qid_tipo_activ);
$oActividadTipo->setSfsv($ssfsv);
$oActividadTipo->setAsistentes($Qsasistentes);
$oActividadTipo->setActividad($Qsactividad);
$oActividadTipo->setNom_tipo($Qsnom_tipo);

if (empty($Qstatus)) { $Qstatus = actividades\model\entity\ActividadAll::STATUS_ACTUAL; }

$oGesDl = new ubis\GestorDelegacion();
$oDesplDelegacionesOrg = $oGesDl->getListaDelegacionesURegiones();
$oDesplDelegacionesOrg->setNombre('dl_org');
$oDesplDelegacionesOrg->setOpcion_sel($Qdl_org);
if ($Qmodo == 'importar') {
	$mi_dele = core\ConfigGlobal::mi_delef();
	$oDesplDelegacionesOrg->setOpcion_no(array($mi_dele));
}
if ($Qmodo == 'publicar') {
	$mi_dele = core\ConfigGlobal::mi_delef();
	$oDesplDelegacionesOrg->setOpciones(array($mi_dele=>$mi_dele));
	$oDesplDelegacionesOrg->setBlanco(false);
}

$oDesplFiltroLugar = $oGesDl->getListaDlURegionesFiltro();
$oDesplFiltroLugar->setAction('fnjs_lugar()');
$oDesplFiltroLugar->setNombre('filtro_lugar');
$oDesplFiltroLugar->setOpcion_sel($Qfiltro_lugar);

$oDesplegableCasas = array();
if (!empty($Qfiltro_lugar)) {
	$oActividadLugar = new \actividades\model\ActividadLugar();
	$oDesplegableCasas = $oActividadLugar->getLugaresPosibles($Qfiltro_lugar); 
    if (!empty($Qid_ubi)) {
        $oDesplegableCasas->setOpcion_sel($Qid_ubi);
    }
}

$aOpciones =  array(
					'tot_any' => _("todo el año"),
					'trimestre_1'=>_("primer trimestre"),
					'trimestre_2'=>_("segundo trimestre"),
					'trimestre_3'=>_("tercer trimestre"),
					'trimestre_4'=>_("cuarto trimestre"),
					'separador'=>'---------',
					'curso_ca'=>_("curso ca"),
					'curso_crt'=>_("curso crt"),
					'separador1'=>'---------',
					'otro'=>_("otro")
					);
$oFormP = new web\PeriodoQue();
$oFormP->setFormName('modifica');
$oFormP->setPosiblesPeriodos($aOpciones);
$oFormP->setDesplPeriodosOpcion_sel($Qperiodo);
$oFormP->setDesplAnysOpcion_sel($Qyear);

$oFormP->setEmpiezaMin($Qempiezamin);
$oFormP->setEmpiezaMax($Qempiezamax);

$oHash = new web\Hash();
$oHash->setcamposForm('dl_org!empiezamax!empiezamin!filtro_lugar!iactividad_val!iasistentes_val!id_tipo_activ!inom_tipo_val!isfsv_val!periodo!status!year');
$oHash->setcamposNo('id_ubi');
$a_camposHidden = array(
		'modo' => $Qmodo,
		'listar_asistentes' => $Qlistar_asistentes,
		'que' => $Qque
		);
$oHash->setArraycamposHidden($a_camposHidden);

$oHash1 = new web\Hash();
$oHash1->setUrl(core\ConfigGlobal::getWeb().'/apps/actividades/controller/actividad_tipo_get.php');
$oHash1->setCamposForm('salida!entrada!opcion_sel!isfsv'); 
$h = $oHash1->linkSinVal();

$aQuery = array('que'=>$Qque,'sactividad'=>$Qsactividad,'sasistentes'=>$Qsasistentes);
// el hppt_build_query no pasa los valores null
if (is_array($aQuery)) { array_walk($aQuery, 'core\poner_empty_on_null'); }
$Link_borrar = web\Hash::link('apps/actividades/controller/actividad_que.php?'.http_build_query($aQuery));

switch ($Qmodo) {
	case 'importar':
		$titulo = ucfirst(_("buscar actividad de otras dl para importar"));
		break;
	case 'publicar':
		$titulo = ucfirst(_("buscar actividades de mi dl para publicar"));
		break;
	default:
		$titulo = ucfirst(_("buscar actividad"));
}

/* a continuación distinguimos el caso habitual en que 
vamos a la página actividad_select.php
de los casos particulares de algunos listados, 
en que vamos directamente a
las páginas que los generan*/
switch ($Qque) {
	case "list_activ" :
	case "list_activ_compl" :
		$accion=core\ConfigGlobal::getWeb().'/apps/actividades/controller/lista_activ.php';
		/*es el caso de querer sacar tablas 
		de un grupo de actividades*/
	break;
	case "list_cjto" :
	case "list_cjto_sacd" :
		$accion=core\ConfigGlobal::getWeb().'/apps/asistentes/controller/lista_asis_conjunto_activ.php';
		/*es el caso de querer sacar 
		los asistentes o cargos 
		de un conjunto de actividades*/
	break;
	default;
		$accion=core\ConfigGlobal::getWeb().'/apps/actividades/controller/actividad_select.php';	
		/*es el caso de todo el resto 
		de listados que pasan por un listado 
		previo con los links */
	break;		
}

$perm_jefe = FALSE;
if ($_SESSION['oConfig']->is_jefeCalendario()
    or (($_SESSION['oPerm']->have_perm("des") or $_SESSION['oPerm']->have_perm("vcsd")) && ConfigGlobal::mi_sfsv() == 1)
    ) { 
    $perm_jefe = TRUE;
}
$oActividadTipo->setPerm_jefe($perm_jefe);


$oUsuario = new Usuario(array('id_usuario'=>ConfigGlobal::mi_id_usuario()));
$perm_ctr = FALSE;
if ( !$oUsuario->isRole('CentroSv') && !$oUsuario->isRole('CentroSf') ) {
    $perm_ctr = TRUE;
}

$val_status_1 = actividades\model\entity\ActividadAll::STATUS_PROYECTO;
$chk_status_1 = ($Qstatus== $val_status_1)? "checked='true'" : '';
$val_status_2 = actividades\model\entity\ActividadAll::STATUS_ACTUAL;
$chk_status_2 = ($Qstatus== $val_status_2)? "checked='true'" : '';
$val_status_3 = actividades\model\entity\ActividadAll::STATUS_TERMINADA;
$chk_status_3 = ($Qstatus== $val_status_3)? "checked='true'" : '';
$val_status_4 = actividades\model\entity\ActividadAll::STATUS_BORRABLE;
$chk_status_4 = ($Qstatus== $val_status_4)? "checked='true'" : '';

$a_campos = ['oPosicion' => $oPosicion,
			'oHash' => $oHash,
			'accion' => $accion,
			'Qid_ubi' => $Qid_ubi,
			'h' => $h,
			'titulo' => $titulo,
			'oDesplFiltroLugar' => $oDesplFiltroLugar,
			'oDesplegableCasas' => $oDesplegableCasas,
			'oDesplDelegacionesOrg' => $oDesplDelegacionesOrg,
			'oFormP' => $oFormP,
			'oActividadTipo' => $oActividadTipo,
			'Link_borrar' => $Link_borrar,
            'perm_ctr' => $perm_ctr,
			'val_status_1' => $val_status_1,
			'chk_status_1' => $chk_status_1,
			'val_status_2' => $val_status_2,
			'chk_status_2' => $chk_status_2,
			'val_status_3' => $val_status_3,
			'chk_status_3' => $chk_status_3,
			'val_status_4' => $val_status_4,
			'chk_status_4' => $chk_status_4,
			];

$oView = new core\ViewTwig('actividades/controller');
echo $oView->render('actividad_que.html.twig',$a_campos);
