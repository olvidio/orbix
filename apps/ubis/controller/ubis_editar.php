<?php
use usuarios\model\entity as usuarios;
/**
* Es el frame inferior. Muestra la ficha de los ubis
*
* Se incluye la página ficha.php que contiene la función ficha.
* Esta página sirve para definir los parámetros que se le pasan a la función ficha.
*
*@package	delegacion
*@subpackage	ubis
*@author	Daniel Serrabou
*@since		15/5/02.
*		
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qrefresh = (integer)  \filter_input(INPUT_POST, 'refresh');
$oPosicion->recordar($Qrefresh);

$Qid_ubi = (integer) \filter_input(INPUT_POST, 'id_ubi');
$Qobj_pau = (string) \filter_input(INPUT_POST, 'obj_pau');
$Qnuevo = (string) \filter_input(INPUT_POST, 'nuevo');

if (!empty($Qnuevo)) {
	$tipo_ubi = (string) \filter_input(INPUT_POST, 'tipo_ubi');
	$QsGestor = (string) \filter_input(INPUT_POST, 'sGestor');
	$Gestor = unserialize(core\urlsafe_b64decode($QsGestor));
	$obj = str_replace('Gestor','',$Gestor);
	$oUbi = new $obj();
	$Qobj_pau = str_replace('ubis\\model\\entity\\','',$obj);
	$cDatosCampo = $oUbi->getDatosCampos();
	$oDbl = $oUbi->getoDbl();
	foreach ($cDatosCampo as $oDatosCampo) {
		$camp = $oDatosCampo->getNom_camp();
		$valor_predeterminado=$oDatosCampo->datos_campo($oDbl,'valor');
		$a_campos[$camp] = $valor_predeterminado;
	}
	$dl = (string) \filter_input(INPUT_POST, 'dl');
	$region = (string) \filter_input(INPUT_POST, 'region');
	$nombre_ubi = (string) \filter_input(INPUT_POST, 'nombre_ubi');
	$nombre_ubi = urldecode($nombre_ubi);
	$oUbi->setNombre_ubi($nombre_ubi);
	$oUbi->setTipo_ubi($tipo_ubi);
	$oUbi->setStatus(true);
	$Qid_ubi = '';
	$id_direccion = '';
	$status = true;
	//print_r($a_campos);
} else {
	$obj = 'ubis\\model\\entity\\'.$Qobj_pau;
	$oUbi = new $obj($Qid_ubi);

	$tipo_ubi = $oUbi->getTipo_ubi();
	$dl = $oUbi->getDl();
    // para el caso de sf, podria ser que en el campo dl, se ponga 'dlbf' y no 'dlb'
	if (substr($dl, -1) == 'f') {
	   $dl = substr($dl,0,-1); // quito la f.
	}
	
	$region = $oUbi->getRegion();
	$nombre_ubi = $oUbi->getNombre_ubi();
	$status = $oUbi->getStatus();
	$id_direccion = '';

	// Aunque el tipo sea ctrdl, si es diferente a la mia, lo trato como ctrex.
	if ($dl != core\ConfigGlobal::mi_dele()) {
		if ($tipo_ubi == 'ctrdl') $tipo_ubi = 'ctrex';
		if ($tipo_ubi == 'cdcdl') $tipo_ubi = 'cdcex';
	}
}

$sf = $oUbi->getSf();

//----------------------------------Permisos según el usuario
$oMiUsuario = new usuarios\Usuario(core\ConfigGlobal::mi_id_usuario());
$miSfsv=core\ConfigGlobal::mi_sfsv();

$botones = 0;
/*
1: guardar cambios
2: eliminar
4: quitar direccion
*/
if (strstr($Qobj_pau,'Dl')) {
	if (!empty($Qnuevo) OR $dl == core\ConfigGlobal::mi_dele()) {
		// ----- sv sólo a scl -----------------
		if ($_SESSION['oPerm']->have_perm("scdl")) {
					$botones= "1,2";
		}
	}
} else if (strstr($Qobj_pau,'Ex')) {
	// ----- sv sólo a scl -----------------
	if ($_SESSION['oPerm']->have_perm("scdl")) {
				$botones= "1,2";
	}
}

$oPermActiv=new ubis\model\CuadrosLabor();

$chk = ($status=="t" OR $status=="true")? 'checked' :''; 
$campos_chk = 'status!sv!sf';

$camposForm = 'que!dl!tipo_ubi!status!region!nombre_ubi';
if ($tipo_ubi=="ctrdl") { 
	$camposForm .= '!num_pi!num_cartas!num_cartas_mensuales!plazas!num_habit_indiv!n_buzon!observ';
}
if ($tipo_ubi=="ctrdl" or $tipo_ubi=="ctrex" or $tipo_ubi=="ctrsf") {
	$camposForm .= '!id_ctr_padre!tipo_ctr';
	$campos_chk .= '!cdc!tipo_labor';
}
if ($tipo_ubi=="cdcdl" or $tipo_ubi=="cdcex") {
	$camposForm .= '!tipo_casa!plazas!plazas_min!num_sacd!sf!sv';
}
$oHash = new web\Hash();
$oHash->setcamposNo('que!'.$campos_chk);
$oHash->setcamposForm($camposForm);
$a_camposHidden = array(
		'campos_chk'=>$campos_chk,
		'obj_pau'=>$Qobj_pau,
		'id_ubi'=>$Qid_ubi,
		'id_direccion'=>$id_direccion
		);
$oHash->setArraycamposHidden($a_camposHidden);




$oView = new core\View('ubis/controller');

if ($tipo_ubi=="ctrdl") { 

	$cdc = $oUbi->getCdc();
	$chk_cdc = ($cdc=="t" OR $cdc=="true")? 'checked' :'';
	$tipo_labor = $oUbi->getTipo_labor();
	$id_ctr_padre = $oUbi->getId_ctr_padre();
	$tipo_ctr = $oUbi->getTipo_ctr();
	$num_pi = $oUbi->getNum_pi();
	$num_cartas = $oUbi->getNum_cartas();
	$num_cartas_mensuales = $oUbi->getNum_cartas_mensuales();
	$num_habit_indiv = $oUbi->getNum_habit_indiv();
	$plazas = $oUbi->getPlazas();
	$n_buzon = $oUbi->getN_buzon();
	$observ = $oUbi->getObserv();
	
	$dl = empty($dl)? core\ConfigGlobal::mi_dele() : $dl;
	$region = empty($region)? core\ConfigGlobal::mi_region() : $region;
	
	$GesCentro = new ubis\model\entity\GestorCentro();
	if (!empty($dl)) {
		$sWhere = "WHERE dl = '$dl'";
	} else if (!empty($region)) {
		$sWhere = "WHERE region = '$region'"; //probar con la region
	} else {
		$sWhere = ''; // Hay muchos ctr que no tienen puesta la dl.
	}
	$oDesplCentros=$GesCentro->getListaCentros($sWhere);
	$nnom = "id_ctr_padre";
	$oDesplCentros->setNombre($nnom);
	$oDesplCentros->setOpcion_sel($id_ctr_padre);
	
	$oTiposCentro = new ubis\model\entity\GestorTipoCentro();
	$oTiposCentroOpciones=$oTiposCentro->getListaTiposCentro();
	$oDesplegableTiposCentro=new web\Desplegable('tipo_ctr',$oTiposCentroOpciones,$tipo_ctr,true);

	$a_campos = ['botones' => $botones,
			'oPosicion' => $oPosicion,
			'obj' => $obj,
			'oHash' => $oHash,
			'tipo_ubi' => $tipo_ubi,
			'dl' => $dl,
			'chk' => $chk,
			'region' => $region,
			'nombre_ubi' => $nombre_ubi,
			'tipo_ctr' => $tipo_ctr,
			'num_pi' => $num_pi,
			'num_cartas' => $num_cartas,
			'num_cartas_mensuales' => $num_cartas_mensuales,
			'oPermActiv' => $oPermActiv,
			'tipo_labor' => $tipo_labor,
			'num_habit_indiv' => $num_habit_indiv,
			'plazas' => $plazas,
			'n_buzon' => $n_buzon,
			'observ' => $observ,
			'chk_cdc' => $chk_cdc,
			'oDesplCentros' => $oDesplCentros,
			'oDesplegableTiposCentro' => $oDesplegableTiposCentro,
			];

	echo $oView->render('ctrdl_form.phtml',$a_campos);
}
	
if ($tipo_ubi=="ctrex" or $tipo_ubi=="ctrsf") {
	
	$cdc = $oUbi->getCdc();
	$chk_cdc = ($cdc=="t" OR $cdc=="true")? 'checked' :'';
	$tipo_labor = $oUbi->getTipo_labor();
	$id_ctr_padre = $oUbi->getId_ctr_padre();
	$tipo_ctr = $oUbi->getTipo_ctr();
	
	$GesCentro = new ubis\model\entity\GestorCentro();
	if (!empty($dl)) {
		$sWhere = "WHERE dl = '$dl'";
	} else if (!empty($region)) {
		$sWhere = "WHERE region = '$region'"; //probar con la region
	} else {
		$sWhere = ''; // Hay muchos ctr que no tienen puesta la dl.
	}
	$oDesplCentros=$GesCentro->getListaCentros($sWhere);
	$nnom = "id_ctr_padre";
	$oDesplCentros->setNombre($nnom);
	$oDesplCentros->setOpcion_sel($id_ctr_padre);
	
	$oTiposCentro = new ubis\model\entity\GestorTipoCentro();
	$oTiposCentroOpciones=$oTiposCentro->getListaTiposCentro();
	$oDesplegableTiposCentro=new web\Desplegable('tipo_ctr',$oTiposCentroOpciones,$tipo_ctr,true);

	$a_campos = ['botones' => $botones,
			'oPosicion' => $oPosicion,
			'obj' => $obj,
			'oHash' => $oHash,
			'tipo_ubi' => $tipo_ubi,
			'dl' => $dl,
			'chk' => $chk,
			'region' => $region,
			'nombre_ubi' => $nombre_ubi,
			'tipo_ctr' => $tipo_ctr,
			'chk_cdc' => $chk_cdc,
			'oDesplCentros' => $oDesplCentros,
			'tipo_labor' => $tipo_labor,
			'oPermActiv' => $oPermActiv,
			'oDesplegableTiposCentro' => $oDesplegableTiposCentro,
			];

	echo $oView->render('ctrex_form.phtml',$a_campos);
}

if ($tipo_ubi=="cdcdl" or $tipo_ubi=="cdcex") {

	if ($tipo_ubi=="cdcdl") {
		$dl = empty($dl)? core\ConfigGlobal::mi_dele() : $dl;
		$region = empty($region)? core\ConfigGlobal::mi_region() : $region;
	}

	$tipo_casa = $oUbi->getTipo_casa();
	$plazas = $oUbi->getPlazas();
	$plazas_min = $oUbi->getPlazas_min();
	$num_sacd = $oUbi->getNum_sacd();
	$sv = $oUbi->getSv();
	$sf = $oUbi->getSf();

	$sv_chk = ($sv=="t" OR $sv=="true")? 'checked': ''; 
	$sf_chk = ($sf=="t" OR $sf=="true")? 'checked' :''; 
	$oTiposCasa=new ubis\model\entity\GestorTipoCasa();
	$oTiposCasaOpciones=$oTiposCasa->getListaTiposCasa();
	$oDesplegableTiposCasa=new web\Desplegable('tipo_casa',$oTiposCasaOpciones,$tipo_casa,true);

	$a_campos = ['botones' => $botones,
			'oPosicion' => $oPosicion,
			'obj' => $obj,
			'oHash' => $oHash,
			'tipo_ubi' => $tipo_ubi,
			'dl' => $dl,
			'chk' => $chk,
			'region' => $region,
			'nombre_ubi' => $nombre_ubi,
			'plazas' => $plazas,
			'plazas_min' => $plazas_min,
			'num_sacd' => $num_sacd,
			'sv_chk' => $sv_chk,
			'sf_chk' => $sf_chk,
			'oDesplegableTiposCasa' => $oDesplegableTiposCasa,
			];
	
	echo $oView->render('cdc_form.phtml',$a_campos);
}