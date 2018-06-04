<?php
/**
 * Muestra el cuadro de calendario: Plazas que tiene cada dl del grupo por actividad.
 * 
 * @param integer $id_tipo_activ
 * o bien 
 * @param string $ssfsv 
 * @param string $sasistentes 
 * @param string $ssctividad 
 */

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_tipo_activ = (string)  filter_input(INPUT_POST, 'id_tipo_activ');
// Id tipo actividad
if (empty($Qid_tipo_activ)) {
	$Qssfsv = (string)  filter_input(INPUT_POST, 'ssvsf');
	if (empty($Qssfsv)) {
		$mi_sfsv = core\ConfigGlobal::mi_sfsv();
		if ($mi_sfsv == 1) $Qssfsv = 'sv';
		if ($mi_sfsv == 2) $Qssfsv = 'sf';
	}
	$Qsasistentes = (string) \filter_input(INPUT_POST, 'sasistentes');
	$Qsactividad = (string) \filter_input(INPUT_POST, 'sactividad');
	$oTipoActiv= new web\TiposActividades();
	$oTipoActiv->setSfsvText($Qssfsv);
	$oTipoActiv->setAsistentesText($Qsasistentes);
	$oTipoActiv->setActividadText($Qsactividad);
	$Qid_tipo_activ=$oTipoActiv->getId_tipo_activ();
} else {
	$oTipoActiv= new web\TiposActividades($Qid_tipo_activ);
	$Qsactividad = $oTipoActiv->getActividadText();
}
$Qid_tipo_activ =  '^'.$Qid_tipo_activ;

//periodo
switch ($Qsactividad) {
	case 'ca':
	case 'cv':
		$any=  core\ConfigGlobal::any_final_curs('est');
		$inicurs=core\curso_est("inicio",$any,"est");
		$fincurs=core\curso_est("fin",$any,"est");
		break;
	case 'crt':
		$any=  core\ConfigGlobal::any_final_curs('crt');
		$inicurs=core\curso_est("inicio",$any,"crt");
		$fincurs=core\curso_est("fin",$any,"crt");
		break;
}

$status = \actividades\model\entity\ActividadAll::STATUS_ACTUAL; //actual

// Seleccionar los id_dl del mismo grupo de estudios
$esquema = core\ConfigGlobal::mi_region_dl();
$a_reg = explode('-',$esquema);
$mi_dl = substr($a_reg[1],0,-1); // quito la v o la f.
$aWhere =array('region'=>$a_reg[0],'dl'=>$mi_dl);
$oMiDelegacion = new ubis\model\entity\Delegacion($aWhere);
$grupo_estudios = $oMiDelegacion->getGrupo_estudios();

$gesDelegacion = new ubis\model\entity\GestorDelegacion();
$cDelegaciones = $gesDelegacion->getDelegaciones(array('grupo_estudios'=>$grupo_estudios,'_ordre'=>'region,dl'));

$gesActividadPlazas = new \actividadplazas\model\entity\GestorActividadPlazas();
// Seleccionar actividades exportadas de los id_dl

$a_grupo = array();
$cActividades = array();
$gesActividades = new actividades\model\entity\GestorActividad();
$k = 0;
foreach ($cDelegaciones as $oDelegacion) {
	$k++;
	$dl = $oDelegacion->getDl();
	$id_dl = $oDelegacion->getId_dl();
	$a_grupo[$dl] = $id_dl;
	$aWhere =array('dl_org'			=>$dl,
					'id_tipo_activ'	=>$Qid_tipo_activ,
					'status' 		=> $status,
					'publicado' 		=> 't',
					'f_ini' 		=> "'$inicurs','$fincurs'",
					'_ordre'		=>'f_ini');
	$aOperador = array('id_tipo_activ'=>'~', 'f_ini'=>'BETWEEN');
	$cActividades1 = $gesActividades->getActividades($aWhere,$aOperador);
	$cActividades =  array_merge($cActividades,$cActividades1);

}

// Dibujar tabla de plazas por actividad
$i = 0;
$a_valores=array();
foreach ($cActividades as $oActividad) {
	$i++;
	$id_tipo_activ = $oActividad->getId_tipo_activ();
	$id_activ = $oActividad->getId_activ();
	$nom = $oActividad->getNom_activ();
	$dl_org = $oActividad->getDl_org();
	$plazas_totales = $oActividad->getPlazas();
	if (empty($plazas_totales)) {
		$id_ubi = $oActividad->getId_ubi();
		$oCasa = ubis\model\entity\Ubi::NewUbi($id_ubi);
		// Si la casa es un ctr de otra dl, no sé las plazas
		if(method_exists($oCasa, 'getPlazas')){
			$plazas_totales = $oCasa->getPlazas();
		} else {
			$plazas_totales = '';
		}
		if (empty($plazas_totales)) {
			$plazas_totales = '?';
		}
	}
	// para estilos
	if ($mi_dl == $dl_org) {
		$a_valores[$i]['clase'] = 'tono2';
	}
	//echo "$nom     $id_tipo_activ       $dl_org".'<br>';
	$a_valores[$i]['id'] = $id_activ;
	$a_valores[$i]['actividad'] = $nom;
	$a_valores[$i]['dlorg'] = $dl_org;
	$a_valores[$i]['tot'] = $plazas_totales;
	if ($mi_dl == $dl_org) {
		$a_valores[$i]['tot'] = array('editable' => 'true', 'valor' => $plazas_totales);
	} else {
		$a_valores[$i]['tot'] = array('editable' => 'false', 'valor' => $plazas_totales);
	}
	foreach ($a_grupo as $dl => $id_dl) {
		$pedidas = '-';
		$concedidas = '-';
		$cActividadPlazas = $gesActividadPlazas->getActividadesPlazas(array('id_dl'=>$id_dl,'id_activ'=>$id_activ));
		foreach ($cActividadPlazas as $oActividadPlazas) {
			$dl_tabla = $oActividadPlazas->getDl_tabla();
			if ($dl_org == $dl_tabla) {
				$concedidas = $oActividadPlazas->getPlazas();
			} else {
				$pedidas = $oActividadPlazas->getPlazas();
			}
		}
		$dl_c = $dl.'-c';
		$dl_p = $dl.'-p';
		if ($mi_dl == $dl) {
			if ($mi_dl == $dl_org) {
				$a_valores[$i][$dl_c] = array('editable' => 'true', 'valor' => $concedidas);
				$a_valores[$i][$dl_p] = array('editable' => 'false', 'valor' => $pedidas);
			} else {
				$a_valores[$i][$dl_c] = array('editable' => 'false', 'valor' => $concedidas);
				$a_valores[$i][$dl_p] = array('editable' => 'true', 'valor' => $pedidas);
			}
		} else {
			if ($mi_dl == $dl_org) {
				$a_valores[$i][$dl_c] = array('editable' => 'true', 'valor' => $concedidas);
			} else {
				$a_valores[$i][$dl_c] = array('editable' => 'false', 'valor' => $concedidas);
			}
			$a_valores[$i][$dl_p] = array('editable' => 'false', 'valor' => $pedidas);
		}
		//$a_valores[$i][$c] = $pedidas;
	}

}
$a_cabeceras=array( 
		array('name'=>_("id_activ"),'field'=>'id','visible'=>'no'),
		array('name'=>_("actividad"),'field'=>'actividad','width'=>200,'formatter'=>'clickFormatter'),
		array('name'=>_("org"),'title'=>_("organiza"),'field'=>"dlorg",'width'=>40),
		array('name'=>_("tot"),'title'=>_("totales actividad"),'field'=>"tot",'width'=>40,'editor'=>'Slick.Editors.Integer'),
		);
foreach ($a_grupo as $dl => $id_dl) {
	$sub_cabecera = array(
		array('name'=>_("c"),'title'=>_("concedidas"),'field'=>$dl."-c",'width'=>15,'editor'=>'Slick.Editors.Integer'),
		array('name'=>_("p"),'title'=>_("pedidas"),'field'=>$dl."-p",'width'=>15,'editor'=>'Slick.Editors.Integer')
	);
	$a_cabeceras[] = array('name'=>$dl,'children'=>$sub_cabecera);
}
$a_botones =array();

$oTabla = new web\TablaEditable();
$oTabla->setId_tabla('gestion_plazas');
$UpdateUrl = core\ConfigGlobal::getWeb().'/apps/actividadplazas/controller/gestion_plazas_ajax.php';
$oTabla->setUpdateUrl($UpdateUrl);
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);

$a_campos = ['oTabla' => $oTabla,
			];

$oView = new core\View('actividadplazas/controller');
echo $oView->render('gestion_plazas.phtml',$a_campos);