<?php

// INICIO Cabecera global de URL de controlador *********************************

use web\Hash;
use zonassacd\model\entity\GestorZona;
use zonassacd\model\entity\GestorZonaSacd;
use personas\model\entity\GestorPersona;
use misas\domain\entity\InicialesSacd;
use misas\domain\entity\EncargoDia;
use web\Desplegable;
use web\PeriodoQue;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************
echo 'hhhollla';
$aOpciones = array(
    'semana_next' => _("próxima semana de lunes a domingo"),
    'mes_next' => _("próximo mes natural"),
    'trimestre_2' => _("segundo trimestre"),
    'trimestre_3' => _("tercer trimestre"),
    'trimestre_4' => _("cuarto trimestre"),
    'separador' => '---------',
    'otro' => _("otro")
);
$oFormP = new PeriodoQue();
$oFormP->setFormName('frm_ver_plan_sacd');
$oFormP->setTitulo(core\strtoupper_dlb(_("seleccionar un periodo")));
$oFormP->setPosiblesPeriodos($aOpciones);

$oFormP->setBoton("<input type=button name=\"ver\" value=\"" . _("ver") . "\" onclick=\"fnjs_ver_plan_sacd();\">");

$aWhere = [];
$aWhere['status'] = 't';
$aWhere['id_zona'] = $Qid_zona;
$aWhere['_ordre'] = 'nombre_ubi';
$GesCentrosDl = new GestorCentroDl();
$cCentrosDl = $GesCentrosDl->getCentros($aWhere);
$GesCentrosSf = new GestorCentroEllas();
$cCentrosSf = $GesCentrosSf->getCentros($aWhere);
$cCentros = array_merge($cCentrosDl, $cCentrosSf);

$aCentros = [];
foreach ($cCentros as $oCentro) {
    $id_ubi = $oCentro->getId_ubi();
    $nombre_ubi = $oCentro->getNombre_ubi();

    $aCentros[$id_ubi] = $nombre_ubi;
}

$oDesplCentros = new Desplegable();
$oDesplCentros->setNombre('id_ubi');
$oDesplCentros->setOpcion_sel($id_ubi);
$oDesplCentros->setOpciones($aCentros);

$url_ver_plan_sacd = 'apps/misas/controller/ver_plan_sacd.php';
$oHashPlanSacd = new Hash();
$oHashPlanSacd->setUrl($url_ver_plan_sacd);
$oHashPlanSacd->setCamposForm('id_sacd!periodo!empiezamin!empiezamax');
$h_plan_sacd = $oHashPlanSacd->linkSinVal();

$a_campos = ['oPosicion' => $oPosicion,
    'oDesplCentros' => $oDesplCentros,
    'oFormP' => $oFormP,
    'url_ver_plan_sacd' => $url_ver_plan_sacd,
    'h_plan_sacd' => $h_plan_sacd,
];
 
$oView = new core\ViewTwig('misas/controller');
echo $oView->render('buscar_plan_sacd.html.twig', $a_campos);