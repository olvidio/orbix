<?php 
use web\Hash;
use web\PeriodoQue;
use core\ConfigGlobal;

/**
* Página para cambiar la fase a un grupo de actividades.
*
*@package	delegacion
*@subpackage	actividades
*@author	Daniel Serrabou
*@since		2/8/2011.
*		
*/

// INICIO Cabecera global de URL de controlador *********************************
require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

//Si vengo de vuelta y le paso la referecia del stack donde está la información.
if (isset($_POST['stack'])) {
    $stack = \filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack != '') {
        // No me sirve el de global_object, sino el de la session
        $oPosicion2 = new web\Posicion();
        if ($oPosicion2->goStack($stack)) { // devuelve false si no puede ir
            $Qid_sel=$oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack);
        }
    }
}

$Qdl_propia = (string) \filter_input(INPUT_POST, 'dl_propia');
$Qid_fase_nueva = (string) \filter_input(INPUT_POST, 'id_fase_nueva');
$Qid_tipo_activ = (string) \filter_input(INPUT_POST, 'id_tipo_activ');
$Qsasistentes = (string) \filter_input(INPUT_POST, 'sasistentes');
$Qsactividad = (string) \filter_input(INPUT_POST, 'sactividad');
$Qperiodo = (string) \filter_input(INPUT_POST, 'periodo');
$Qinicio = (string) \filter_input(INPUT_POST, 'inicio');
$Qfin = (string) \filter_input(INPUT_POST, 'fin');
$Qempiezamin = (string) \filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string) \filter_input(INPUT_POST, 'empiezamax');
$Qyear = (string) \filter_input(INPUT_POST, 'year');

$permiso_des = FALSE;
// mejor que novenga por menú. Así solo veo las de mi sección.
if (($_SESSION['oPerm']->have_perm("vcsd")) or ($_SESSION['oPerm']->have_perm("des"))) {
    $permiso_des = TRUE;
    $Qssfsv = '';
} else {
    $mi_sfsv = ConfigGlobal::mi_sfsv();
    if ($mi_sfsv == 1) $Qssfsv = 'sv';
    if ($mi_sfsv == 2) $Qssfsv = 'sf';
}

$oActividadTipo = new actividades\model\ActividadTipo();
$oActividadTipo->setPerm_jefe($permiso_des);
$oActividadTipo->setSfsv($Qssfsv);
$oActividadTipo->setId_tipo_activ($Qid_tipo_activ);
$oActividadTipo->setAsistentes($Qsasistentes);
$oActividadTipo->setActividad($Qsactividad);

$aOpciones =  array(
					'tot_any' => _("todo el año"),
					'trimestre_1'=>_("primer trimestre"),
					'trimestre_2'=>_("segundo trimestre"),
					'trimestre_3'=>_("tercer trimestre"),
					'trimestre_4'=>_("cuarto trimestre"),
					'separador'=>'---------',
					'otro'=>_("otro")
					);
$oFormP = new PeriodoQue();
$oFormP->setFormName('modifica');
$oFormP->setPosiblesPeriodos($aOpciones);
$oFormP->setDesplPeriodosOpcion_sel($Qperiodo);
$oFormP->setDesplAnysOpcion_sel($Qyear);


$url_ajax = "apps/procesos/controller/fases_activ_cambio_ajax.php";

$oHashLista = new Hash();
$oHashLista->setUrl($url_ajax);
$oHashLista->setcamposForm('que!dl_propia!id_tipo_activ!id_fase_nueva!periodo!year!empiezamax!empiezamin');
$h_lista = $oHashLista->linkSinVal();

$oHashAct = new Hash();
$oHashAct->setUrl($url_ajax);
$oHashAct->setcamposForm('que!dl_propia!id_tipo_activ!id_fase_sel');
$h_actualizar = $oHashAct->linkSinVal();

$url_tipo = "apps/actividades/controller/actividad_tipo_get.php";
$oHash1 = new web\Hash();
//$oHash1->setUrl(ConfigGlobal::getWeb().'/apps/actividades/controller/actividad_tipo_get.php');
$oHash1->setUrl($url_tipo);
$oHash1->setCamposForm('salida!entrada');
$h_tipo = $oHash1->linkSinVal();
		
$txt_eliminar = _("¿Esta seguro que desea borrar esta fase?");

if ($Qdl_propia == 'f') {
    $chk_propia = '';
    $chk_no_propia = 'checked';
} else {
    $chk_propia = 'checked';
    $chk_no_propia = '';
}

$a_campos = ['oPosicion' => $oPosicion,
    'h_lista' => $h_lista,
    'h_actualizar' => $h_actualizar,
    'h_tipo' => $h_tipo,
    'oActividadTipo' => $oActividadTipo,
    'oFormP' => $oFormP,
    'url_ajax' => $url_ajax,
    'url_tipo' => $url_tipo,
    'txt_eliminar' => $txt_eliminar,
    'chk_propia' => $chk_propia,
    'chk_no_propia' => $chk_no_propia,
    'id_fase_nueva' => $Qid_fase_nueva,
];

$oView = new core\ViewTwig('procesos/controller');
echo $oView->render('fases_activ_cambio.html.twig',$a_campos);