<?php
/**
* Esta página sirve para seleccionar una casa o varias para ver sus actividades etc.
*
*@package	delegacion
*@subpackage	actividades
*@author	Daniel Serrabou
*@since		15/5/02.
*		
*/

// INICIO Cabecera global de URL de controlador *********************************

use core\ConfigGlobal;
use function core\strtoupper_dlb;
use usuarios\model\entity\Usuario;
use web\CasasQue;
use web\DesplegableArray;
use web\PeriodoQue;
use usuarios\model\entity\Role;

require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$Qtipo_lista = (string) \filter_input(INPUT_POST, 'tipo_lista');

$Qperiodo = (string) \filter_input(INPUT_POST, 'periodo');
$Qyear = (integer) \filter_input(INPUT_POST, 'year');
// Cuando vengo de la página del resumen, tengo el id de la casa.
$Qid_ubi = (integer) \filter_input(INPUT_POST, 'id_ubi');

$tipo_lista = empty($Qtipo_lista)? '' : $Qtipo_lista;
if ($tipo_lista == 'datosEcGastos' ) { $Qperiodo = 'ninguno'; }

$oForm = new CasasQue();
// miro que rol tengo. Si soy casa, sólo veo la mía
$oMiUsuario = new Usuario(ConfigGlobal::mi_id_usuario());
$miRolePau = ConfigGlobal::mi_role_pau();
if ($miRolePau == Role::PAU_CDC) { //casa
	$id_pau=$oMiUsuario->getId_pau();
	$sDonde=str_replace(",", " OR id_ubi=", $id_pau);
	//formulario para casas cuyo calendario de actividades interesa 
	$donde="WHERE status='t' AND (id_ubi=$sDonde)";
	$oForm->setCasas('casa');
} else {
	// Sólo quiero ver las casas comunes.
	//$donde="WHERE status='t' AND sf='t' AND sv='t'";
	// o (ara) no:
	if ($_SESSION['oPerm']->have_perm("des") or $_SESSION['oPerm']->have_perm("vcsd")) {
		$oForm->setCasas('all');
		$donde="WHERE status='t'";
	} else {
		if ($oMiUsuario->getSfsv() == 1) {
		  	$oForm->setCasas('sv');
			$donde="WHERE status='t' AND sv='t'";
		} elseif ($oMiUsuario->getSfsv() == 2) {
			$oForm->setCasas('sf');
			$donde="WHERE status='t' AND sf='t'";
		}
	}
}
$oForm->setPosiblesCasas($donde);
$oForm->setAction('');
// para seleccionar más de una casa
$aOpcionesCasas = $oForm->getPosiblesCasas();
$oSelects = new DesplegableArray('',$aOpcionesCasas,'id_cdc');
$oSelects->setBlanco('t');
$oSelects->setAccionConjunto('fnjs_mas_casas(event)');

if ($Qperiodo == 'no') {
	if ($tipo_lista == 'datosEc') $oForm->setTitulo(strtoupper_dlb(_("resumen económico")));
	$oForm->setBoton("<input type=button name=\"buscar\" value=\""._('buscar')."\" onclick=\"fnjs_ver();\">");
} else {
	if ($Qperiodo == 'ninguno') { // sólo el año
		$aOpciones =  array('ninguno' => _('ninguno'));
	} else {
		$aOpciones =  array(
						'tot_any' => _('todo el año'),
						'trimestre_1'=>_('primer trimestre'),
						'trimestre_2'=>_('segundo trimestre'),
						'trimestre_3'=>_('tercer trimestre'),
						'trimestre_4'=>_('cuarto trimestre'),
						'separador'=>'---------',
						'otro'=>_('otro')
						);
	}
	$oFormP = new PeriodoQue();
	$oFormP->setFormName('seleccion');
	$oFormP->setTitulo(strtoupper_dlb(_("seleccionar una casa y un período")));
	$oFormP->setPosiblesPeriodos($aOpciones);
	if (!empty($Qyear)) {
		$oFormP->setDesplAnysOpcion_sel($Qyear);
	}

	$oFormP->setAntes($oSelects->ListaSelects());
	$oFormP->setBoton("<input type=button name=\"buscar\" value=\""._('buscar')."\" onclick=\"fnjs_ver();\">");
}

$oHash = new web\Hash();
$sCamposForm = 'que!id_cdc!id_cdc_mas!id_cdc_num!empiezamax!empiezamin!iactividad_val!iasistentes_val!year';
//$oHash->setcamposNo('id_cdc!modelo');

switch ($tipo_lista) {
    case 'lista_activ':
        $url_ajax = 'apps/casas/controller/casa_ajax.php';
        $param = 'que=lista_activ';
        $sCamposForm .= '!periodo';
        break;
    case 'datosEc':
        //echo "var url='".ConfigGlobal::$web."/programas/casa_ec_ajax.php';\n";
        echo "var url='apps/casas/controller/casa_resumen_ajax.php';\n";
        echo "var parametros=pata+'&que=get&PHPSESSID=".session_id()."';\n";
        break;
    case 'ctrsEncargados':
        $Qver_ctr = (string) \filter_input(INPUT_POST, 'ver_ctr');
        $url_ajax = 'apps/actividades/controller/calendario_listas.php';
        $param = "que=lista_cdc&ver_ctr=$Qver_ctr";
        $sCamposForm .= '!periodo!ver_ctr';
        break;
    case 'datosEcGastos':
        $url_ajax = 'apps/casas/controller/casa_ec_ajax.php';
        $param = 'que=getGastos';
        break;
    default:
        $url_ajax = 'apps/casas/controller/casa_ajax.php';
        $param = 'que=get';
        $sCamposForm .= '!periodo';
}
$oHash->setcamposForm($sCamposForm);

$oHashEdit = new web\Hash();
$oHashEdit->setUrl($url_ajax);
$oHashEdit->setcamposForm('que!id_activ');
$h_edit_a = $oHashEdit->linkSinVal();

$oHashEditU = new web\Hash();
$oHashEditU->setUrl($url_ajax);
$oHashEditU->setcamposForm('que!id_ubi');
$h_edit_u = $oHashEditU->linkSinVal();

$a_campos = ['oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'url_ajax' => $url_ajax,
    'h_edit_a' => $h_edit_a,
    'h_edit_u' => $h_edit_u,
    'param' => $param,
    'oForm' => $oForm,
    'oFormP' => $oFormP,
    'oSelects' => $oSelects,
    'periodo' => $Qperiodo,
    'id_ubi' => $Qid_ubi,
];

$oView = new core\ViewTwig('casas/controller');
echo $oView->render('casa_que.html.twig',$a_campos);
