<?php 
/**
 * Muestra un formulario para poder seleccionar un rgupo de actividades
 * 
 */
use ubis\model\entity as ubis;
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");

// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$a_sel = (array)  \filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
	$Qid_activ = strtok($a_sel[0],"#");
	// el scroll id es de la página anterior, hay que guardarlo allí
	$oPosicion->addParametro('id_sel',$a_sel,1);
	$scroll_id = empty($_POST['scroll_id'])? 0 : $_POST['scroll_id'];
	$oPosicion->addParametro('scroll_id',$scroll_id,1);
} else {
	$Qid_activ = (integer)  \filter_input(INPUT_POST, 'id_activ');
}

$Qmod = (string)  \filter_input(INPUT_POST, 'mod');
$Qtipo = (string)  \filter_input(INPUT_POST, 'tipo');
$Qobj_pau = (string)  \filter_input(INPUT_POST, 'obj_pau');

$obj = 'actividades\\model\\entity\\ActividadAll';

$sQuery = array ('pau'=>'a',
				'id_pau'=>$Qid_activ,
				'obj_pau'=>$Qobj_pau);
$godossiers = web\Hash::link('apps/dossiers/controller/dossiers_ver.php?'.http_build_query($sQuery));

$a_status = array( 1 => _("proyecto"), 2 => _("actual"), 3 => _("terminada"), 4 => _("borrable"));

$alt = '';
$dos = '';
if (!empty($Qid_activ)) { // caso de modificar
	$alt=_("ver dossiers");
	$dos=_("dossiers");
	$Qmod = 'editar';

	$oActividad = new actividades\model\entity\Actividad($Qid_activ);
	$aDades = $oActividad->getTot();
	$id_tipo_activ = $aDades['id_tipo_activ'];
	$dl_org = $aDades['dl_org'];
	$nom_activ = $aDades['nom_activ'];
	$id_ubi = $aDades['id_ubi'];
	//$desc_activ = $aDades['desc_activ'];
	$f_ini = $aDades['f_ini'];
	$h_ini = $aDades['h_ini'];
	$f_fin = $aDades['f_fin'];
	$h_fin = $aDades['h_fin'];
	//$tipo_horario = $aDades['tipo_horario'];
	$precio = $aDades['precio'];
	//$num_asistentes = $aDades['num_asistentes'];
	$status = $aDades['status'];
	$observ = $aDades['observ'];
	$nivel_stgr = $aDades['nivel_stgr'];
	//$observ_material = $aDades['observ_material'];
	$lugar_esp = $aDades['lugar_esp'];
	$tarifa = $aDades['tarifa'];
	$id_repeticion = $aDades['id_repeticion'];
	$publicado = $aDades['publicado'];
	$plazas = $aDades['plazas'];
			
	// mirar permisos.
	//if(core\ConfigGlobal::is_app_installed('procesos')) {
		$_SESSION['oPermActividades']->setActividad($Qid_activ,$id_tipo_activ,$dl_org);
		$oPermActiv = $_SESSION['oPermActividades']->getPermisoActual('datos');

		if ($oPermActiv->only_perm('ocupado')) { die(); }
	//}

	$oTipoActiv= new web\TiposActividades($id_tipo_activ);
	$ssfsv=$oTipoActiv->getSfsvText();
	$sasistentes=$oTipoActiv->getAsistentesText();
	$sactividad=$oTipoActiv->getActividadText();
	$snom_tipo=$oTipoActiv->getNom_tipoText();
	$isfsv=$oTipoActiv->getSfsvId();

	
} else { // caso de nueva actividad
	$Qmod = 'nuevo';
	$isfsv=core\ConfigGlobal::mi_sfsv();
	
	// Valores por defecto	
	$dl_org = core\ConfigGlobal::mi_dele(); 
	$dl_org .= ($isfsv == 2)? 'f' : ''; 
	// si es nueva, obligatorio estado: proyecto (14.X.2011)
	$status = 1;
	$id_ubi = 0;
	$lugar_esp = '';
	$tarifa = '';
	$nivel_stgr = 'r';
	$id_repeticion = 0;
	$id_tipo_activ = '';
	$id_activ = '';
	$ssfsv = '';
	$sasistentes='';
	$sactividad='';
	$snom_tipo='';
	
	$nom_activ='';
	$f_ini='';
	$h_ini='';
	$f_fin='';
	$h_fin='';
	$plazas='';
	$precio='';
	$observ='';
	$publicado='';
	
	$oPermActiv = array();
}
	

if (!empty($id_ubi) && $id_ubi != 1) {
	$oCasa = ubis\Ubi::newUbi($id_ubi);
	$nombre_ubi=$oCasa->getNombre_ubi();
	$delegacion=$oCasa->getDl();
	$region=$oCasa->getRegion();
	$sv=$oCasa->getSv();
	$sf=$oCasa->getSf();
} else {
	if ($id_ubi==1 && $lugar_esp) $nombre_ubi=$lugar_esp;
	if (!$id_ubi && !$lugar_esp) $nombre_ubi=_("sin determinar");
}

// Para incluir o no la dl (core\ConfigGlobal::mi_dele()).
$Bdl="t";
if(core\ConfigGlobal::is_app_installed('procesos')) {
	if ($oPermActiv->have_perm('ver')) {
		$Bdl="t";
	} else {
		$Bdl="f";
	}
}
$oGesDl = new ubis\GestorDelegacion();
$oDesplDelegacionesOrg = $oGesDl->getListaDelegacionesURegiones($Bdl);
$oDesplDelegacionesOrg->setNombre('dl_org');
$oDesplDelegacionesOrg->setOpcion_sel($dl_org);

$oGesTipoTarifa = new actividades\model\entity\GestorTipoTarifa();
$oDesplPosiblesTipoTarifas = $oGesTipoTarifa->getListaTipoTarifas($isfsv);
$oDesplPosiblesTipoTarifas->setNombre('tarifa');
$oDesplPosiblesTipoTarifas->setOpcion_sel($tarifa);

$oGesNivelStgr = new actividades\model\entity\GestorNivelStgr();
$oDesplNivelStgr = $oGesNivelStgr->getListaNivelesStgr();
$oDesplNivelStgr->setNombre('nivel_stgr');
$oDesplNivelStgr->setOpcion_sel($nivel_stgr);

$oGesRepeticion = new actividades\model\entity\GestorRepeticion();
$oDesplRepeticion = $oGesRepeticion->getListaRepeticion();
$oDesplRepeticion->setNombre('id_repeticion');
$oDesplRepeticion->setOpcion_sel($id_repeticion);

$oHash = new web\Hash();
$camposForm = 'status!dl_org!f_fin!f_ini!h_fin!h_ini!id_repeticion!id_ubi!lugar_esp!mod!nivel_stgr!nom_activ!nombre_ubi!observ!precio!tarifa!publicado!plazas';
$camposNo = 'mod';
if ($Qmod == 'nuevo') {
	$camposForm .= '!iactividad_val!iasistentes_val!inom_tipo_val!isfsv_val';
	$camposNo .= '!id_tipo_activ';
} else {
	$camposForm .= '!sactividad!sasistentes!snom_tipo';
	
}
$oHash->setcamposForm($camposForm);
$oHash->setCamposNo($camposNo);
$a_camposHidden = array(
		'id_tipo_activ' => $id_tipo_activ,
		'id_activ' => $Qid_activ,
		'ssfsv' => $ssfsv,
//		'mod' => $Qmod,
		);
$oHash->setArraycamposHidden($a_camposHidden);

$oHash1 = new web\Hash();
$oHash1->setUrl(core\ConfigGlobal::getWeb().'/apps/actividades/controller/actividad_select_ubi.php');
$oHash1->setCamposForm('dl_org!ssfsv'); 
$h = $oHash1->linkSinVal();

$oActividadTipo = new actividades\model\ActividadTipo();
$oActividadTipo->setId_tipo_activ($id_tipo_activ);
$oActividadTipo->setAsistentes($sasistentes);
$oActividadTipo->setActividad($sactividad);
$oActividadTipo->setNom_tipo($snom_tipo);


$accion = '';
$a_campos = ['oPosicion' => $oPosicion,
			'oHash' => $oHash,
			'h' => $h,
			'obj' => $obj,
			'godossiers' => $godossiers,
			'alt' => $alt,
			'dos' => $dos,
			'oPermActiv' => $oPermActiv,
			'accion' => $accion,
			'sasistentes' => $sasistentes,
			'sactividad' => $sactividad,
			'snom_tipo' => $snom_tipo,
			'ssfsv' => $ssfsv,
			'status' => $status,
			'a_status' => $a_status,
			'nom_activ' => $nom_activ,
			'f_ini' => $f_ini,
			'h_ini' => $h_ini,
			'f_fin' => $f_fin,
			'h_fin' => $h_fin,
			'oDesplDelegacionesOrg' => $oDesplDelegacionesOrg,
			'plazas' => $plazas,
			'nombre_ubi' => $nombre_ubi,
			'id_ubi' => $id_ubi,
			'lugar_esp' => $lugar_esp,
			'oDesplPosiblesTipoTarifas' => $oDesplPosiblesTipoTarifas,
			'precio' => $precio,
			'observ' => $observ,
			'oDesplRepeticion' => $oDesplRepeticion,
			'oDesplNivelStgr' => $oDesplNivelStgr,
			'publicado' => $publicado,
			'mod' => $Qmod,
			'oActividadTipo' => $oActividadTipo,
			'id_tipo_activ' => $id_tipo_activ,
			];

$oView = new core\View('actividades/controller');
echo $oView->render('actividad_form.phtml',$a_campos);