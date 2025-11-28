<?php

use actividades\model\ActividadTipo;
use core\ConfigGlobal;
use core\ViewTwig;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use web\CasasQue;
use web\Hash;
use web\PeriodoQue;
use web\Posicion;

/**
 * Página para cambiar la fase a un grupo de actividades.
 *
 * @package    delegacion
 * @subpackage    actividades
 * @author    Daniel Serrabou
 * @since        2/8/2011.
 *
 */

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

//Si vengo de vuelta y le paso la referencia del stack donde está la información.
if (isset($_POST['stack'])) {
    $stack = filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack != '') {
        // No me sirve el de global_object, sino el de la session
        $oPosicion2 = new Posicion();
        if ($oPosicion2->goStack($stack)) { // devuelve false si no puede ir
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack);
        }
    }
}

$Qid_tipo_activ = (string)filter_input(INPUT_POST, 'id_tipo_activ');
$Qsasistentes = (string)filter_input(INPUT_POST, 'sasistentes');
$Qsactividad = (string)filter_input(INPUT_POST, 'sactividad');
$Qsnom_tipo = (string)filter_input(INPUT_POST, 'snom_tipo');
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qinicio = (string)filter_input(INPUT_POST, 'inicio');
$Qfin = (string)filter_input(INPUT_POST, 'fin');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');
$Qyear = (string)filter_input(INPUT_POST, 'year');
$Qcdc_sel = (integer)filter_input(INPUT_POST, 'cdc_sel');
$Qid_cdc_mas = (string)filter_input(INPUT_POST, 'id_cdc_mas');
$Qid_cdc_num = (string)filter_input(INPUT_POST, 'id_cdc_num');

$isfsv = ConfigGlobal::mi_sfsv();
$permiso_des = FALSE;
// mejor que no venga por menú. Así solo veo las de mi sección.
// añado la opción sv para calendario...
if ($_SESSION['oPerm']->have_perm_oficina('vcsd')
    || $_SESSION['oPerm']->have_perm_oficina('des')
    || $_SESSION['oPerm']->have_perm_oficina('calendario')
) {
    $permiso_des = TRUE;
    $ssfsv = '';
} else {
    if ($isfsv === 1) {
        $ssfsv = 'sv';
    }
    if ($isfsv === 2) {
        $ssfsv = 'sf';
    }
}

$oActividadTipo = new ActividadTipo();
$oActividadTipo->setPerm_jefe($permiso_des);
$oActividadTipo->setId_tipo_activ($Qid_tipo_activ);
$oActividadTipo->setSfsv($ssfsv);
$oActividadTipo->setAsistentes($Qsasistentes);
$oActividadTipo->setActividad($Qsactividad);
$oActividadTipo->setNom_tipo($Qsnom_tipo);
$oActividadTipo->setEvitarProcesos(TRUE);

$perm_jefe = FALSE;
if ($_SESSION['oConfig']->is_jefeCalendario()
    || (($_SESSION['oPerm']->have_perm_oficina('des') || $_SESSION['oPerm']->have_perm_oficina('vcsd')) && ConfigGlobal::mi_sfsv() === 1)
    || ($_SESSION['oPerm']->have_perm_oficina('admin_sf') && ConfigGlobal::mi_sfsv() === 2)
) {
    $perm_jefe = TRUE;
}
$oActividadTipo->setPerm_jefe($perm_jefe);
$oActividadTipo->setSfsvAll(TRUE);


$aOpciones = array(
    'tot_any' => _("todo el año"),
    'trimestre_1' => _("primer trimestre"),
    'trimestre_2' => _("segundo trimestre"),
    'trimestre_3' => _("tercer trimestre"),
    'trimestre_4' => _("cuarto trimestre"),
    'separador' => '---------',
    'otro' => _("otro")
);
$oFormP = new PeriodoQue();
$oFormP->setFormName('modifica');
$oFormP->setPosiblesPeriodos($aOpciones);
$oFormP->setDesplPeriodosOpcion_sel($Qperiodo);
$oFormP->setDesplAnysOpcion_sel($Qyear);

//formulario para casas cuyo calendario de actividades interesa
$oForm = new CasasQue();
$oForm->setTitulo('');

// posible selección múltiple de casas
$CasaDlRepository = $GLOBALS['container']->get(CasaDlRepositoryInterface::class);
$aCasas = $CasaDlRepository->getArrayCasas();
$a_id_ubi = array_keys($aCasas);
$csv_id_ubi = implode(',', $a_id_ubi);
$condicion = "WHERE status='t' AND id_ubi IN($csv_id_ubi)";
$oForm->setCasas('casa');
$oForm->setPosiblesCasas($condicion);


$url_ajax = "apps/pasarela/controller/exportar_select.php";

$oHash = new Hash();
$oHash->setUrl($url_ajax);
$oHash->setCamposForm('cdc_sel!empiezamax!empiezamin!extendida!iactividad_val!iasistentes_val!id_cdc!id_cdc_mas!id_cdc_num!id_tipo_activ!inom_tipo_val!isfsv_val!periodo!year');
$oHash->setCamposNo('cdc_sel!id_cdc!id_cdc_mas!id_cdc_num');

$a_campos = ['oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'oActividadTipo' => $oActividadTipo,
    'oFormP' => $oFormP,
    'oForm' => $oForm,
    'url_ajax' => $url_ajax,
];

$oView = new ViewTwig('pasarela/controller');
$oView->renderizar('exportar_que.html.twig', $a_campos);