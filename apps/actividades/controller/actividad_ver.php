<?php 
/**
 * Muestra un formulario para poder seleccionar un rgupo de actividades
 * 
 */
use actividades\model\entity\ActividadAll;
use ubis\model\entity\GestorDelegacion;
use ubis\model\entity\Ubi;

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");

// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$a_sel = (array)  \filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
	$Qid_activ = (integer) strtok($a_sel[0],"#");
	// el scroll id es de la página anterior, hay que guardarlo allí
	$oPosicion->addParametro('id_sel',$a_sel,1);
	$scroll_id = (integer) \filter_input(INPUT_POST, 'scroll_id');
	$oPosicion->addParametro('scroll_id',$scroll_id,1);
} else {
	$Qid_activ = (integer)  \filter_input(INPUT_POST, 'id_activ');
}

$Qmod = (string)  \filter_input(INPUT_POST, 'mod');
$Qtipo = (string)  \filter_input(INPUT_POST, 'tipo');
$Qobj_pau = (string)  \filter_input(INPUT_POST, 'obj_pau');

$obj = 'actividades\\model\\entity\\ActividadAll';

$aQuery = array ('pau'=>'a',
				'id_pau'=>$Qid_activ,
				'obj_pau'=>$Qobj_pau);
// el hppt_build_query no pasa los valores null
if (is_array($aQuery)) { array_walk($aQuery, 'core\poner_empty_on_null'); }
$godossiers = web\Hash::link('apps/dossiers/controller/dossiers_ver.php?'.http_build_query($aQuery));

$a_status = ActividadAll::ARRAY_STATUS_TXT;

$permiso_des = FALSE;
if (($_SESSION['oPerm']->have_perm("vcsd")) or ($_SESSION['oPerm']->have_perm("des"))) {
    $permiso_des = TRUE;
}

$alt = '';
$dos = '';
if (!empty($Qid_activ)) { // caso de modificar
	$alt=_("ver dossiers");
	$dos=_("dossiers");
	$Qmod = 'editar';

	$oActividad = new actividades\model\entity\Actividad($Qid_activ);
	$id_tipo_activ = $oActividad->getId_tipo_activ();
	$dl_org = $oActividad->getDl_org();
	$nom_activ = $oActividad->getNom_activ();
	$id_ubi = $oActividad->getId_ubi();
	//$desc_activ = $oActividad->['desc_activ'];
	$f_ini = $oActividad->getF_ini()->getFromLocal();
	$h_ini = $oActividad->getH_ini();
	$f_fin = $oActividad->getF_fin()->getFromLocal();
	$h_fin = $oActividad->getH_fin();
	//$tipo_horario = $oActividad->['tipo_horario'];
	$precio = $oActividad->getPrecio();
	//$num_asistentes = $oActividad->['num_asistentes'];
	$status = $oActividad->getStatus();
	$observ = $oActividad->getObserv();
	$nivel_stgr = $oActividad->getNivel_stgr();
	//$observ_material = $oActividad->['observ_material'];
	$lugar_esp = $oActividad->getLugar_esp();
	$tarifa = $oActividad->getTarifa();
	$id_repeticion = $oActividad->getId_repeticion();
	$publicado = $oActividad->getPublicado();
	$plazas = $oActividad->getPlazas();
			
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

    // Para incluir o no la dl 
    $Bdl = 't';
    if(core\ConfigGlobal::is_app_installed('procesos')) {
        if ($oPermActiv->have_perm('ver')) {
            $Bdl = 't';
        } else {
            $Bdl = 'f';
        }
    }
	
} else { // caso de nueva actividad
	$Qmod = 'nuevo';
	$isfsv=core\ConfigGlobal::mi_sfsv();
	
	// Valores por defecto	
	$dl_org = core\ConfigGlobal::mi_delef(); 
	// si es nueva, obligatorio estado: proyecto (14.X.2011)
	$status = 1;
	$id_ubi = 0;
	$lugar_esp = '';
	$tarifa = '';
	$nivel_stgr = 'r';
	$id_repeticion = 0;
	$id_tipo_activ = (string)  \filter_input(INPUT_POST, 'id_tipo_activ');
	$id_activ = '';

	if ( $permiso_des == TRUE ) {
        $ssfsv = '';
	} else {
        if ($isfsv == 1) $ssfsv = 'sv';
        if ($isfsv == 2) $ssfsv = 'sf';
	}
	$sasistentes = (string) \filter_input(INPUT_POST, 'sasistentes');
	$sactividad = (string) \filter_input(INPUT_POST, 'sactividad');
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
	
    // Para incluir o no la dl
    $Bdl = 't';
    if(core\ConfigGlobal::is_app_installed('procesos')) {
        // Depende del proceso, para dl u otra
        // primera fase de los posibles procesos.
        // si no permiso para ninguno de los dos => die
        // si para dl, incluir la dl org
        // si dl_ex, idem.
        $_SESSION['oPermActividades']->setId_tipo_activ($id_tipo_activ);
        
        $crearPropia = $_SESSION['oPermActividades']->getPermisoCrear(TRUE);
        $of_responsable = $crearPropia['of_responsable'];
        if ($_SESSION['oPerm']->have_perm($of_responsable)) {
            $Bdl = 't';
            $status = $crearPropia['status'];
        } else {
            $Bdl = 'f';
            $crearEx = $_SESSION['oPermActividades']->getPermisoCrear(FALSE);
            $of_responsable = $crearEx['of_responsable'];
            $status = $crearEx['status'];
            if (!$_SESSION['oPerm']->have_perm($of_responsable)) {
                die (_("No tiene permiso para crear una actividad de este tipo"));
            }
        }
        
    }
    // Para el permiso del botón guardar, en el caso de editar. Cuando es nuevo
    // no se utiliza. Se inicializa para que no dé error.
	$oPermActiv = array();
}
	

if (!empty($id_ubi) && $id_ubi != 1) {
	$oCasa = Ubi::newUbi($id_ubi);
	$nombre_ubi=$oCasa->getNombre_ubi();
	$delegacion=$oCasa->getDl();
	$region=$oCasa->getRegion();
	$sv=$oCasa->getSv();
	$sf=$oCasa->getSf();
} else {
	if ($id_ubi==1 && $lugar_esp) $nombre_ubi=$lugar_esp;
	if (!$id_ubi && !$lugar_esp) $nombre_ubi=_("sin determinar");
}

$oGesDl = new GestorDelegacion();
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
$oActividadTipo->setPerm_jefe($permiso_des);
$oActividadTipo->setId_tipo_activ($id_tipo_activ);
$oActividadTipo->setSfsv($ssfsv);
$oActividadTipo->setAsistentes($sasistentes);
$oActividadTipo->setActividad($sactividad);
$oActividadTipo->setNom_tipo($snom_tipo);

$procesos_installed = core\ConfigGlobal::is_app_installed('procesos');

$status_txt = $a_status[$status];
$accion = '';
$a_campos = ['oPosicion' => $oPosicion,
			'oHash' => $oHash,
			'h' => $h,
			'obj' => addslashes($obj),
			'godossiers' => $godossiers,
			'alt' => $alt,
			'dos' => $dos,
			'oPermActiv' => $oPermActiv,
            'permiso_des'=> $permiso_des,
			'accion' => $accion,
			'sasistentes' => $sasistentes,
			'sactividad' => $sactividad,
			'snom_tipo' => $snom_tipo,
			'ssfsv' => $ssfsv,
			'status' => $status,
			'status_txt' => $status_txt,
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
            'web' => core\ConfigGlobal::getWeb(),
            'web_icons' => core\ConfigGlobal::$web_icons,
            'procesos_installed' => $procesos_installed,
			];

$oView = new core\ViewTwig('actividades/controller');
echo $oView->render('actividad_form.html.twig',$a_campos);
//$oView = new core\View('actividades/controller');
//echo $oView->render('actividad_form.phtml',$a_campos);