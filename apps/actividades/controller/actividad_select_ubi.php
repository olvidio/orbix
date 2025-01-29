<?php
/**
 * Este controlador permite seleccionar un lugar donde realizar una actividad
 * Establece 5 posibilidades de búsqueda, o sin determinar...
 *
 */

// INICIO Cabecera global de URL de controlador *********************************
use core\ConfigGlobal;
use core\ViewPhtml;
use web\Desplegable;
use web\Hash;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

// Que mande el isvsf, que es el desplegable.

$isfsv = empty($_REQUEST['isfsv']) ? '' : $_REQUEST['isfsv'];
$ssfsv = empty($_REQUEST['ssfsv']) ? '' : $_REQUEST['ssfsv'];

if (empty($isfsv)) {
    if ($ssfsv == 'sv') {
        $isfsv = 1;
    }
    if ($ssfsv == 'sf') {
        $isfsv = 2;
    }
}

switch ($isfsv) {
    case 1:
        $donde_sfsv = "AND sv='t'";
        break;
    case 2:
        $donde_sfsv = "AND sf='t'";
        break;
    default:
        $isfsv = 0;
        $donde_sfsv = '';
}

if (!empty($_REQUEST['dl_org'])) {
    $sql_freq = "select distinct id_ubi,nombre_ubi from a_actividades_dl join u_cdc_dl using (id_ubi) where dl_org='" . $_REQUEST['dl_org'] . "' $donde_sfsv ORDER by nombre_ubi";
    $oDbl = $GLOBALS['oDBC'];
    $oDBSt_q_freq = $oDbl->query($sql_freq);
    $oDesplFreq = new Desplegable();
    $oDesplFreq->setNombre('id_ubi_1');
    $oDesplFreq->setOpciones($oDBSt_q_freq);
}

// desplegable región
$oDbl = $GLOBALS['oDBPC'];
// $sql_dl_lugar="SELECT 'dl|'||u.dl,u.nombre_dl FROM xu_dl u WHERE status='t' ";
// Ahora hay que quitar las cr, que se han puesto como dl:
$sql_dl_lugar = "SELECT 'dl|'||u.dl,u.nombre_dl FROM xu_dl u WHERE status='t' AND u.dl !~ '^cr' ";
$sql_r_lugar = "SELECT 'r|'||u.region,u.nombre_region FROM xu_region u WHERE status='t' ";
$sql_u_lugar = $sql_dl_lugar . " UNION " . $sql_r_lugar . " ORDER BY 2";
$oDBSt_dl_r_lugar = $oDbl->query($sql_u_lugar);
//$oDBSt_dl_r_lugar=$oDbl->query($sql_dl_lugar);

$oDesplRegion = new Desplegable();
$oDesplRegion->setNombre('filtro_lugar');
$oDesplRegion->setAction('fnjs_lugar()');
$oDesplRegion->setOpciones($oDBSt_dl_r_lugar);
if (!empty($_REQUEST['dl_org'])) {
    $dl = 'dl|' . $_REQUEST['dl_org'];
    $oDesplRegion->setOpcion_sel($dl);
}

$oHash = new Hash();
$oHash->setUrl(ConfigGlobal::getWeb() . '/apps/actividades/controller/actividad_tipo_get.php');
$oHash->setCamposForm('extendida!modo!salida!entrada!isfsv');
$h = $oHash->linkSinVal();

$oHash1 = new Hash();
$oHash1->setCamposForm('id_ubi_1');
$oHash2 = new Hash();
$oHash2->setCamposForm('filtro_lugar!lst_lugar');
$oHash3 = new Hash();
$oHash3->setCamposForm('nombre_ubi');
$a_camposHidden = array(
    'tipo' => 'tot',
    'loc' => 'tot'
);
$oHash3->setArraycamposHidden($a_camposHidden);

$oHash4 = new Hash();
$oHash4->setCamposForm('frm_4_nombre_ubi');

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

$oView = new ViewPhtml('actividades/controller');
$oView->renderizar('actividad_select_ubi.phtml', $a_campos);
