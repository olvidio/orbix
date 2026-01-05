<?php
/**
 * Página del formulario para listados particulares de sr
 *
 */

use core\ConfigGlobal;
use core\ViewTwig;
use src\usuarios\domain\contracts\PreferenciaRepositoryInterface;
use web\Hash;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qyear = (string)filter_input(INPUT_POST, 'year');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');

$aOpciones = array(
    'tot_any' => _("todo el año"),
    'trimestre_1' => _("primer trimestre"),
    'trimestre_2' => _("segundo trimestre"),
    'trimestre_3' => _("tercer trimestre"),
    'trimestre_4' => _("cuarto trimestre"),
    'separador' => '---------',
    'curso_ca' => _("curso ca"),
    'curso_crt' => _("curso crt"),
    'separador1' => '---------',
    'otro' => _("otro")
);

// por defecto
$id_usuario = ConfigGlobal::mi_id_usuario();
$tipo = 'busqueda_activ_sr';
$PreferenciaRepository = $GLOBALS['container']->get(PreferenciaRepositoryInterface::class);
$oPreferencia = $PreferenciaRepository->findById($id_usuario, $tipo);
if ($oPreferencia !== null) {
    $json_busqueda = $oPreferencia->getPreferencia();
} else {
    $json_busqueda = '';
}
$oBusqueda = json_decode($json_busqueda);
if (is_object($oBusqueda)) {
    $a_status = json_decode($oBusqueda->status);
    $Qperiodo = $oBusqueda->periodo;
    $a_tipo_activ = json_decode($oBusqueda->tipo_activ);
    $a_ubis = json_decode($oBusqueda->ubis_compartidos);
    $sel_ubis = implode(',', $a_ubis);
} else {
    $a_status = [1, 2];
    $Qperiodo = 'curso_ca';
    $a_tipo_activ = [1, 3];
    $a_ubis = [];
    $sel_ubis = '';
}

$chk_status_1 = '';
$chk_status_2 = '';
foreach ($a_status as $val) {
    if ($val == 1) {
        $chk_status_1 = 'checked';
    }
    if ($val == 2) {
        $chk_status_2 = 'checked';
    }
}

$chk_activ_crt = '';
$chk_activ_cv = '';
foreach ($a_tipo_activ as $tipo_activ) {
    if ($tipo_activ == 1) {
        $chk_activ_crt = 'checked';
    }
    if ($tipo_activ == 3) {
        $chk_activ_cv = 'checked';
    }
}

$oFormP = new web\PeriodoQue();
$oFormP->setFormName('modifica');
$oFormP->setAntes('Periodo');
$oFormP->setPosiblesPeriodos($aOpciones);
$oFormP->setDesplPeriodosOpcion_sel($Qperiodo);
$oFormP->setDesplAnysOpcion_sel($Qyear);

$oFormP->setEmpiezaMin($Qempiezamin);
$oFormP->setEmpiezaMax($Qempiezamax);

$oForm = new web\CasasQue();
$oForm->setTitulo(core\strtoupper_dlb(_("ocupación de casas compartidas")));
// miro que rol tengo. Si soy casa, sólo veo la mía
$sDonde = '';
//formulario para casas cuyo calendario de actividades interesa
$donde = "WHERE active='t' $sDonde";
$oForm->setCasas('casa');
$oForm->setPosiblesCasas($donde);
$oForm->setSeleccionados($sel_ubis);

$oHash = new Hash();
$oHash->setCamposForm('empiezamin!empiezamax!c_activ!id_cdc_mas!id_cdc_num!periodo!status!year');
$oHash->setcamposNo('que!id_cdc!cdc_sel');

$Qque = '';
$a_camposHidden = array(
    'que' => $Qque,
);
$oHash->setArraycamposHidden($a_camposHidden);

$titulo = _("selección de actividades de san rafael");
$fullUrl = ConfigGlobal::getWeb() . '/apps/actividades/controller/lista_sr_csv.php';

$a_campos = ['oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'titulo' => $titulo,
    'oForm' => $oForm,
    'oFormP' => $oFormP,
    'chk_status_1' => $chk_status_1,
    'chk_status_2' => $chk_status_2,
    'chk_activ_crt' => $chk_activ_crt,
    'chk_activ_cv' => $chk_activ_cv,
    // para el download
    'fullUrl' => $fullUrl,
];

$oView = new ViewTwig('actividades/controller');
$oView->renderizar('lista_sr_csv_que.html.twig', $a_campos);
