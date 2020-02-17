<?php
/**
 * Este controlador permite seleccionar un lugar donde realizar una actividad
 * Establece 5 posibilidades de búsqueda, o sin determinar...
 * 
 */

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

// Debo incluuirlo aqui por que se abre en una página nueva
//include_once(core\ConfigGlobal::$dir_estilos.'/todo_en_uno.css.php');

$ssfsv = empty($_REQUEST['ssfsv'])? '' : $_REQUEST['ssfsv'];
switch($ssfsv){
	case "sv":
		$isfsv = 1;
		$donde_sfsv="AND sv='t'";
		break;
	case "sf":
		$isfsv = 2;
		$donde_sfsv="AND sf='t'";
		break;
	default:
		$isfsv = 0;
		$donde_sfsv='';
}

if (!empty($_REQUEST['dl_org'])) {
	$sql_freq="select distinct id_ubi,nombre_ubi from a_actividades_dl join u_cdc_dl using (id_ubi) where dl_org='".$_REQUEST['dl_org']."' $donde_sfsv ORDER by nombre_ubi";
	$oDbl = $GLOBALS['oDBC'];
	$oDBSt_q_freq=$oDbl->query($sql_freq);
	$oDesplFreq = new web\Desplegable();
	$oDesplFreq->setNombre('id_ubi_1');
	$oDesplFreq->setOpciones($oDBSt_q_freq);
}

// desplegable región
$oDbl = $GLOBALS['oDBPC'];
$sql_dl_lugar="SELECT 'dl|'||u.dl,u.nombre_dl FROM xu_dl u WHERE status='t' ";
$sql_r_lugar="SELECT 'r|'||u.region,u.nombre_region FROM xu_region u WHERE status='t' ";
$sql_u_lugar=$sql_dl_lugar." UNION ".$sql_r_lugar." ORDER BY 2";
$oDBSt_dl_r_lugar=$oDbl->query($sql_u_lugar);

$oDesplRegion = new web\Desplegable();
$oDesplRegion->setNombre('filtro_lugar');
$oDesplRegion->setAction('fnjs_lugar()');
$oDesplRegion->setOpciones($oDBSt_dl_r_lugar);
if (!empty($_REQUEST['dl_org'])) {
	$dl = 'dl|'.$_REQUEST['dl_org'];
	$oDesplRegion->setOpcion_sel($dl);
}

$oHash = new web\Hash();
$oHash->setUrl(core\ConfigGlobal::getWeb().'/apps/actividades/controller/actividad_tipo_get.php');
$oHash->setCamposForm('salida!entrada!modo!isfsv');
$h = $oHash->linkSinVal();

$oHash1 = new web\Hash();
$oHash1->setcamposForm('id_ubi_1');
$oHash2 = new web\Hash();
$oHash2->setcamposForm('filtro_lugar!lst_lugar');
$oHash3 = new web\Hash();
$oHash3->setcamposForm('nombre_ubi');
$a_camposHidden = array(
		'tipo' =>'tot',
		'loc' => 'tot'
		);
$oHash3->setArraycamposHidden($a_camposHidden);

$oHash4 = new web\Hash();
$oHash4->setcamposForm('frm_4_nombre_ubi');

$txt_alert = _("no olvides ajustar el nombre de la actividad");

$a_campos = ['oPosicion' => $oPosicion,
			'oHash' => $oHash,
			'h' => $h,
			'oHash1' => $oHash1,
			'oHash2' => $oHash2,
			'oHash3' => $oHash3,
			'oHash4' => $oHash4,
			'oDesplRegion' => $oDesplRegion,
			'oDesplFreq' => $oDesplFreq,
			'isfsv' => $isfsv,
			'txt_alert' => $txt_alert,
			];

$oView = new core\View('actividades/controller');
echo $oView->render('actividad_select_ubi.phtml',$a_campos);
