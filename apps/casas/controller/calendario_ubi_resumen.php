<?php

// INICIO Cabecera global de URL de controlador *********************************

use core\ConfigGlobal;
use core\ViewTwig;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use web\Desplegable;
use web\Hash;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$QG = (integer)filter_input(INPUT_POST, 'G');
$Qinc_t = (integer)filter_input(INPUT_POST, 'inc_t');
$Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');

// miro que rol tengo.
$oMiUsuario = ConfigGlobal::MiUsuario();
// selecciono la lista de casas comunes: sf y sv.
// o (ara) no:
$CasaDlRepository = $GLOBALS['container']->get(CasaDlRepositoryInterface::class);

if ($_SESSION['oPerm']->have_perm_oficina('des') || $_SESSION['oPerm']->have_perm_oficina('vcsd')) {
    $donde = "WHERE active='t'";
} else {
    if (ConfigGlobal::mi_sfsv() == 1) {
        $donde = "WHERE active='t' AND sv='t'";
    } elseif (ConfigGlobal::mi_sfsv() == 2) {
        $donde = "WHERE active='t' AND sf='t'";
    }
}

$aCasas = $CasaDlRepository->getArrayCasas($donde);
$oDesplCasas = new Desplegable();
$oDesplCasas->setNombre('id_ubi');
$oDesplCasas->setOpciones($aCasas);
$oDesplCasas->setOpcion_sel($Qid_ubi);

$url_ajax = 'apps/casas/controller/calendario_ubi_resumen_ajax.php';

// El form `frm_tarifas` (dentro del resumen ajax) postea las cantidades
// incrementadas al endpoint JSON `/src/actividadtarifas/tarifa_ubi_update_inc`.
// Lo firmamos con `Hash::linkSinVal` para incluir `hnov`+`h`; el submit
// devuelve JSON (`ContestarJson`), el frontend ignora `rta_txt` vacio.
$web = rtrim(ConfigGlobal::getWeb(), '/');
$oHashTarifas = new Hash();
$oHashTarifas->setUrl($web . '/src/actividadtarifas/tarifa_ubi_update_inc');
$oHashTarifas->setCamposForm('id_ubi!year!inc_cantidad');
$url_tarifas = $web . '/src/actividadtarifas/tarifa_ubi_update_inc' . $oHashTarifas->linkSinVal();

$param = 'que=get';

$oHash = new Hash();
$sCamposForm = 'id_ubi!que!G!inc_t!seccion';
$oHash->setCamposForm($sCamposForm);

$a_campos = ['oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'url_ajax' => $url_ajax,
    'url_tarifas' => $url_tarifas,
    'param' => $param,
    'QG' => $QG,
    'Qinc_t' => $Qinc_t,
    'oDesplCasas' => $oDesplCasas,
];

$oView = new ViewTwig('casas/controller');
$oView->renderizar('ubi_resumen.html.twig', $a_campos);
