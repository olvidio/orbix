<?php

// INICIO Cabecera global de URL de controlador *********************************

use core\ConfigGlobal;
use ubis\model\entity\GestorCasaDl;
use usuarios\model\entity\Usuario;
use web\Desplegable;

require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$QG = (integer) \filter_input(INPUT_POST, 'G');
$Qinc_t = (integer) \filter_input(INPUT_POST, 'inc_t');
$Qid_ubi = (integer) \filter_input(INPUT_POST, 'id_ubi');

// miro que rol tengo.
$oMiUsuario = new Usuario(ConfigGlobal::mi_id_usuario());
// selecciono la lista de casas comunes: sf y sv.
// o (ara) no:
$GesCasas = new GestorCasaDl();

if ($_SESSION['oPerm']->have_perm_oficina('des') or $_SESSION['oPerm']->have_perm_oficina('vcsd')) {
	$donde="WHERE status='t'";
} else {
	if (ConfigGlobal::mi_sfsv() == 1) {
		$donde="WHERE status='t' AND sv='t'";
	} elseif (ConfigGlobal::mi_sfsv() == 2) {
		$donde="WHERE status='t' AND sf='t'";
	}
}

$cCasas = $GesCasas->getPosiblesCasas($donde);
$oDesplCasas = new Desplegable();
$oDesplCasas->setNombre('id_ubi');
$oDesplCasas->setOpciones($cCasas);
$oDesplCasas->setOpcion_sel($Qid_ubi);

$url_ajax = 'apps/casas/controller/calendario_ubi_resumen_ajax.php';
$url_tarifas = 'apps/actividadtarifas/controller/tarifa_ajax.php';
$param = 'que=get';

$oHash = new web\Hash();
$sCamposForm = 'id_ubi!que!G!inc_t!seccion';
$oHash->setcamposForm($sCamposForm);

$a_campos = ['oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'url_ajax' => $url_ajax,
    'url_tarifas' => $url_tarifas,
    'param' => $param,
    'QG' => $QG,
    'Qinc_t' => $Qinc_t,
    'oDesplCasas' => $oDesplCasas,
];

$oView = new core\ViewTwig('casas/controller');
echo $oView->render('ubi_resumen.html.twig',$a_campos);
