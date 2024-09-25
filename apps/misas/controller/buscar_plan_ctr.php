<?php

// INICIO Cabecera global de URL de controlador *********************************

use web\Hash;
use zonassacd\model\entity\GestorZona;
use zonassacd\model\entity\GestorZonaSacd;
use personas\model\entity\GestorPersona;
use ubis\model\entity\GestorCentroDl;
use ubis\model\entity\GestorCentroEllas;
use misas\domain\entity\InicialesSacd;
use misas\domain\entity\EncargoDia;
use web\DateTimeLocal;
use web\Desplegable;
use web\PeriodoQue;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_zona = (integer)filter_input(INPUT_POST, 'id_zona');

$oGestorZona = new GestorZona();
$oDesplZonas = $oGestorZona->getListaZonas();
$oDesplZonas->setNombre('id_zona');
$oDesplZonas->setAction('fnjs_buscar_plan_ctr()');
$oDesplZonas->setOpcion_sel($Qid_zona);

$aCentros = [];
if (isset($Qid_zona)) {
    $aWhere = [];
    $aWhere['status'] = 't';
    $aWhere['id_zona'] = $Qid_zona;
    $aWhere['_ordre'] = 'nombre_ubi';
    $GesCentrosDl = new GestorCentroDl();
    $cCentrosDl = $GesCentrosDl->getCentros($aWhere);
    $GesCentrosSf = new GestorCentroEllas();
    $cCentrosSf = $GesCentrosSf->getCentros($aWhere);
    $cCentros = array_merge($cCentrosDl, $cCentrosSf);
    
    foreach ($cCentros as $oCentro) {
        $id_ubi = $oCentro->getId_ubi();
        $nombre_ubi = $oCentro->getNombre_ubi();
    
        $aCentros[$id_ubi] = $nombre_ubi;
    }    
}

$oDesplCentros = new Desplegable();
$oDesplCentros->setNombre('id_ubi');
$oDesplCentros->setOpciones($aCentros);
if (isset($id_ubi)) {
    $oDesplCentros->setOpcion_sel($id_ubi);
}
$oDesplCentros->setAction('fnjs_ver_plan_ctr()');

$aOpciones = array(
    'esta_semana' => _("esta semana"),
    'este_mes' => _("este mes"),
    'proxima_semana' => _("próxima semana de lunes a domingo"),
    'proximo_mes' => _("próximo mes natural"),
    'separador' => '---------',
    'otro' => _("otro")
);

$oFormP = new PeriodoQue();
$oFormP->setFormName('frm_nuevo_periodo');
$oFormP->setTitulo(core\strtoupper_dlb(_("seleccionar un periodo")));
$oFormP->setPosiblesPeriodos($aOpciones);
$oFormP->setDesplPeriodosOpcion_sel('esta_semana');
$oFormP->setisDesplAnysVisible(FALSE);

$ohoy = new DateTimeLocal(date('Y-m-d'));
$shoy = $ohoy ->format('d/m/Y');

$oFormP->setEmpiezaMin($shoy);
$oFormP->setEmpiezaMax($shoy);

$url_buscar_plan_ctr = 'apps/misas/controller/buscar_plan_ctr.php';
$oHashBuscarPlanCtr = new Hash();
$oHashBuscarPlanCtr->setUrl($url_buscar_plan_ctr);
$oHashBuscarPlanCtr->setCamposForm('id_zona');
$h_buscar_plan_ctr = $oHashBuscarPlanCtr->linkSinVal();

$url_ver_plan_ctr = 'apps/misas/controller/ver_plan_ctr.php';
$oHashPlanCtr = new Hash();
$oHashPlanCtr->setUrl($url_ver_plan_ctr);
$oHashPlanCtr->setCamposForm('id_zona!id_ubi!periodo!empiezamin!empiezamax');
$h_plan_ctr = $oHashPlanCtr->linkSinVal();

$a_campos = ['oPosicion' => $oPosicion,
    'oDesplZonas' => $oDesplZonas,
    'oDesplCentros' => $oDesplCentros,
    'oFormP' => $oFormP,
    'url_buscar_plan_ctr' => $url_buscar_plan_ctr,
    'url_ver_plan_ctr' => $url_ver_plan_ctr,
    'h_plan_ctr' => $h_plan_ctr,
];
 
$oView = new core\ViewTwig('misas/controller');
echo $oView->render('buscar_plan_ctr.html.twig', $a_campos);