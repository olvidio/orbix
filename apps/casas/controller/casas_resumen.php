<?php

use core\ConfigGlobal;
use core\ViewTwig;
use src\usuarios\domain\entity\Role;
use src\usuarios\domain\value_objects\PauType;
use web\CasasQue;
use web\Hash;
use web\PeriodoQue;
use function core\strtoupper_dlb;

/**
 * Página que presentará los formularios de los distintos plannings
 * Según sea el submenú seleccionado seleccionará el formulario
 * correspondiente
 *
 * @package    delegacion
 * @subpackage    actividades
 * @author    Josep Companys
 * @since        15/5/02.
 *
 */
// INICIO Cabecera global de URL de controlador *********************************

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$Qtipo = (string)filter_input(INPUT_POST, 'tipo');
$Qsfsv = (string)filter_input(INPUT_POST, 'sfsv');


$oMiUsuario = ConfigGlobal::MiUsuario();
$miSfsv = ConfigGlobal::mi_sfsv();
$miRole = ConfigGlobal::mi_id_role();


if (date('m') > 9) {
    $periodo_txt = _('(por defecto: período desde 1/10 hasta 31/5)');
} else {
    $periodo_txt = _('(por defecto: período desde 1/6 hasta 30/9)');
}

$oForm = new CasasQue();
$oForm->setTitulo(strtoupper_dlb(_('búsqueda de casas cuyo resumen económico interesa')));
// miro que rol tengo. Si soy casa, sólo veo la mía
$miRolePau = ConfigGlobal::mi_role_pau();
if ($miRolePau === PauType::PAU_CDC) { //casa
    $id_pau = $oMiUsuario->getCsv_id_pau();
    $sDonde = str_replace(",", " OR id_ubi=", $id_pau);
    //formulario para casas cuyo calendario de actividades interesa
    $donde = "WHERE active='t' AND (id_ubi=$sDonde)";
    $oForm->setCasas('casa');
} else {
    if ($_SESSION['oPerm']->have_perm_oficina('des') || $_SESSION['oPerm']->have_perm_oficina('vcsd')) {
        $oForm->setCasas('all');
        $donde = "WHERE active='t'";
    } else {
        if ($miSfsv == 1) {
            $oForm->setCasas('sv');
            $donde = "WHERE active='t' AND sv='t'";
        }
        if ($miSfsv == 2) {
            $oForm->setCasas('sf');
            $donde = "WHERE active='t' AND sf='t'";
        }
    }
}
$oForm->setPosiblesCasas($donde);

$aOpciones = array(
    'tot_any' => _('todo el año'),
    'trimestre_1' => _('primer trimestre'),
    'trimestre_2' => _('segundo trimestre'),
    'trimestre_3' => _('tercer trimestre'),
    'trimestre_4' => _('cuarto trimestre'),
    'separador' => '---------',
    'otro' => _('otro')
);
$oFormP = new PeriodoQue();
$oFormP->setFormName('que');
$oFormP->setTitulo(strtoupper_dlb(_('periodo para el resumen económico')));
$oFormP->setPosiblesPeriodos($aOpciones);
$oFormP->setDesplAnysOpcion_sel(date('Y'));

$oHash = new Hash();
$sCamposForm = 'cdc_sel!empiezamax!empiezamin!extendida!iactividad_val!iasistentes_val!id_cdc!id_cdc_mas!id_cdc_num!periodo!sfsv!tipo!year';
$aCamposHidden = [
    'tipo' => $Qtipo,
    'sfsv' => $Qsfsv,
];
$oHash->setArrayCamposHidden($aCamposHidden);
$oHash->setCamposForm($sCamposForm);
$oHash->setCamposNo('id_cdc');

$url_ajax = 'apps/casas/controller/casas_resumen_ajax.php';

$a_campos = ['oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'url_ajax' => $url_ajax,
    'oForm' => $oForm,
    'oFormP' => $oFormP,
];

$oView = new ViewTwig('casas/controller');
$oView->renderizar('casa_resumen_que.html.twig', $a_campos);
