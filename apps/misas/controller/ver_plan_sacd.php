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

$a_Clases = [];
$a_Clases[] = array('clase' => 'PersonaN', 'get' => 'getPersonas');
$a_Clases[] = array('clase' => 'PersonaAgd', 'get' => 'getPersonas');
$aWhere = [];
$aOperador = [];
$aWhere['sacd'] = 't';
$aWhere['situacion'] = 'A';
$aWhere['_ordre'] = 'apellido1,apellido2,nom';
$GesPersonas = new GestorPersona();
$GesPersonas->setClases($a_Clases);
$cPersonas = $GesPersonas->getPersonas($aWhere, $aOperador);
foreach ($cPersonas as $oPersona) {
    $id_nom = $oPersona->getId_nom();
    $InicialesSacd = new InicialesSacd();
    $sacd=$InicialesSacd->nombre_sacd($id_nom);
    $iniciales=$InicialesSacd->iniciales($id_nom);
    $key = $id_nom . '#' . $iniciales;
    $a_sacd[$key] = $sacd ?? '?';
}
$oDesplSacd = new Desplegable();
$oDesplSacd->setNombre('id_sacd');
$oDesplSacd->setOpciones($a_sacd);
$oDesplSacd->setBlanco(TRUE);

$url_buscar_plan_sacd = 'apps/misas/controller/buscar_plan_sacd.php';
$oHashPlanSacd = new Hash();
$oHashPlanSacd->setUrl($url_buscar_plan_sacd);
$oHashPlanSacd->setCamposForm('id_sacd!periodo!empiezamin!empiezamax');
$h_plan_sacd = $oHashPlanSacd->linkSinVal();

$a_campos = ['oPosicion' => $oPosicion,
    'oDesplSacd' => $oDesplSacd,
    'oFormP' => $oFormP,
    'url_buscar_plan_sacd' => $url_buscar_plan_sacd,
    'h_plan_sacd' => $h_plan_sacd,
];
 
$oView = new core\ViewTwig('misas/controller');
echo $oView->render('ver_plan_sacd.html.twig', $a_campos);