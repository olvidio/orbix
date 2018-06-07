<?php
use personas\model\entity as personas;
use ubis\model\entity as ubis;
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

$Qna = (string) \filter_input(INPUT_POST, 'na');
$Qid_ctr_n = (string) \filter_input(INPUT_POST, 'id_ctr_n');
$Qid_ctr_agd = (string) \filter_input(INPUT_POST, 'id_ctr_agd');
$Qiasistentes_val = (string) \filter_input(INPUT_POST, 'iasistentes_val');
$Qiactividad_val = (string) \filter_input(INPUT_POST, 'actividad_val');
$Qperiodo = (string) \filter_input(INPUT_POST, 'periodo');
$Qyear = (string) \filter_input(INPUT_POST, 'year');
$Qempiezamax = (string) \filter_input(INPUT_POST, 'empiezamax');
$Qempiezamin = (string) \filter_input(INPUT_POST, 'empiezamin');
$Qref = (string) \filter_input(INPUT_POST, 'ref');
$Qtodos = (string) \filter_input(INPUT_POST, 'todos');


// Grupo de estudios
$mi_dele = core\ConfigGlobal::mi_dele();
$GesGrupoEst = new ubis\GestorDelegacion();
$cMiDl = $GesGrupoEst->getDelegaciones(array('dl'=>$mi_dele));
if (is_array($cMiDl) && !empty($cMiDl)) {
	$grupo_estudios = $cMiDl[0]->getGrupo_estudios();
	$cDelegaciones = $GesGrupoEst->getDelegaciones(array('grupo_estudios'=>$grupo_estudios));
	$mi_grupo = '';
	foreach ($cDelegaciones as $oDelegacion) {
		$mi_grupo .= empty($mi_grupo)? '' : ',';
		$mi_grupo .= $oDelegacion->getDl();
	}
} else {
	$mi_grupo = _("No encuentro el grupo de estudios al que pertenece la dl");
}

// centros donde hay numerarios, aunque sean de agd
$GesPersonas = new personas\GestorPersonaN();
$aListaCtr = $GesPersonas->getListaCtr();
$aCentrosN = array();
$aCentrosOrden = array();
foreach($aListaCtr as $id_ubi) {
	$oCentro = new ubis\CentroDl(array('id_ubi'=>$id_ubi));
	$nombre_ubi = $oCentro->getNombre_ubi();
	$aCentrosOrden[$nombre_ubi] = array($id_ubi => $nombre_ubi);
}
uksort($aCentrosOrden,"core\strsinacentocmp");
// No encuentro la manera de añadir las opciones sin desordenar el array de indice numérico
$aCentrsoNExt = array();
$aCentrosNExt[1] = _("todos los ctr");
$aCentrosNExt[2] = "----------";
foreach ($aCentrosOrden as $aCentro) {
	$key = key($aCentro);
	$value = current($aCentro);
	$aCentrosNExt[$key] = $value;
}

$oDesplCtrN = new web\Desplegable();
$oDesplCtrN->setNombre('id_ctr_n');
$oDesplCtrN->setOpciones($aCentrosNExt);
$oDesplCtrN->setOpcion_sel($Qid_ctr_n);
$oDesplCtrN->setBlanco(1);
$oDesplCtrN->setAction("fnjs_n_a('n')");

// centros donde hay agregados, aunque sean de n
$GesPersonas = new personas\GestorPersonaAgd();
$aListaCtr = $GesPersonas->getListaCtr();
$aCentrosAgd = array();
$aCentrosOrden = array();
foreach($aListaCtr as $id_ubi) {
	$oCentro = new ubis\CentroDl(array('id_ubi'=>$id_ubi));
	$nombre_ubi = $oCentro->getNombre_ubi();
	$aCentrosOrden[$nombre_ubi] = array($id_ubi => $nombre_ubi);
}
uksort($aCentrosOrden,"core\strsinacentocmp");
// No encuentro la manera de añadir las opciones sin desordenar el array de indice numérico
$aCentrsoAgdExt = array();
$aCentrosAgdExt[1] = _("todos los ctr");
$aCentrosAgdExt[2] = "----------";
foreach ($aCentrosOrden as $aCentro) {
	$key = key($aCentro);
	$value = current($aCentro);
	$aCentrosAgdExt[$key] = $value;
}

$oDesplCtrAgd = new web\Desplegable();
$oDesplCtrAgd->setNombre('id_ctr_agd');
$oDesplCtrAgd->setOpciones($aCentrosAgdExt);
$oDesplCtrAgd->setOpcion_sel($Qid_ctr_agd);
$oDesplCtrAgd->setBlanco(1);
$oDesplCtrAgd->setAction("fnjs_n_a('agd')");

// Selección de periodo
$any=empty($Qyear)? date('Y') : $Qyear;
$aOpciones =  array(
					'verano'=>_('verano'),
					'curso_ca'=>_('curso'),
					'separador'=>'---------',
					'tot_any' => _('todo el año'),
					'trimestre_1'=>_('primer trimestre'),
					'trimestre_2'=>_('segundo trimestre'),
					'trimestre_3'=>_('tercer trimestre'),
					'trimestre_4'=>_('cuarto trimestre'),
					'separador'=>'---------',
					'otro'=>_('otro')
					);
$oFormP = new web\PeriodoQue();
$oFormP->setFormName('que');
$oFormP->setTitulo(core\strtoupper_dlb(_("periodo de las actividades")));
$oFormP->setPosiblesPeriodos($aOpciones);
$oFormP->setDesplPeriodosOpcion_sel($Qperiodo);
$oFormP->setDesplAnysOpcion_sel($any);
$oFormP->setEmpiezaMin($Qempiezamin);
$oFormP->setEmpiezaMax($Qempiezamax);

$oHash = new web\Hash();
$oHash->setcamposForm('id_ctr_agd!id_ctr_n!texto!empiezamax!empiezamin!periodo!ref!iactividad_val!iasistentes_val!year');
$oHash->setCamposNo('na!todos');
$a_camposHidden = array(
		'asistentes_val' => 1,
		'actividades_val' => 2
		);
$oHash->setArraycamposHidden($a_camposHidden);

if ($Qtodos == 1) {
	$chk_todos = 'checked';
	$chk_grupo = '';
} else {
	$chk_todos = '';
	$chk_grupo = 'checked';
}

$a_campos = [
			'oPosicion' => $oPosicion,
			'oHash' => $oHash,
			'grupo_estudios' => $grupo_estudios,
			'mi_grupo' => $mi_grupo,
			'oFormP' => $oFormP,
			'oDesplCtrAgd' => $oDesplCtrAgd,
			'oDesplCtrN' => $oDesplCtrN,
			'na' => $Qna,
			'ref' => $Qref,
			'chk_todos' => $chk_todos,
			'chk_grupo' => $chk_grupo,
			];

$oView = new core\View('actividadestudios/controller');
echo $oView->render('ca_posibles_que.phtml',$a_campos);