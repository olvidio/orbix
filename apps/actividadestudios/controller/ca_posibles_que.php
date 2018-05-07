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
$oDesplCtrAgd->setBlanco(1);
$oDesplCtrAgd->setAction("fnjs_n_a('agd')");

// Selección de periodo
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
$oFormP->setDesplAnysOpcion_sel(date('Y'));

$oHash = new web\Hash();
$oHash->setcamposForm('id_ctr_agd!id_ctr_n!texto!empiezamax!empiezamin!periodo!ref!iactividad_val!iasistentes_val!year');
$oHash->setCamposNo('na!todos');
$a_camposHidden = array(
		'asistentes_val' => 1,
		'actividades_val' => 2
		);
$oHash->setArraycamposHidden($a_camposHidden);

$a_campos = [
			//'oPosicion' => $oPosicion,
			'oHash' => $oHash,
			'grupo_estudios' => $grupo_estudios,
			'mi_grupo' => $mi_grupo,
			'oFormP' => $oFormP,
			'oDesplCtrAgd' => $oDesplCtrAgd,
			'oDesplCtrN' => $oDesplCtrN,
			];

$oView = new core\View('actividadestudios/controller');
echo $oView->render('ca_posibles_que.phtml',$a_campos);