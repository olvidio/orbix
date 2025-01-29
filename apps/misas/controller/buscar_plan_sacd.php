<?php

// INICIO Cabecera global de URL de controlador *********************************

use core\ViewTwig;
use misas\domain\entity\InicialesSacd;
use personas\model\entity\GestorPersona;
use web\DateTimeLocal;
use web\Desplegable;
use web\Hash;
use web\PeriodoQue;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$aPeriodo = array(
    'esta_semana' => _("esta semana"),
    'este_mes' => _("este mes"),
    'proxima_semana' => _("próxima semana"),
    'proximo_mes' => _("próximo mes"),
    'separador' => '---------',
    'otro' => _("otro")
); 

$oDesplPeriodo = new Desplegable();
$oDesplPeriodo->setOpciones($aPeriodo);
$oDesplPeriodo->setNombre('periodo');
$oDesplPeriodo->setAction('fnjs_ver_plan_sacd()');

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
$oDesplSacd->setAction('fnjs_ver_plan_sacd()');

$url_ver_plan_sacd = 'apps/misas/controller/ver_plan_sacd.php';
$oHashPlanSacd = new Hash();
$oHashPlanSacd->setUrl($url_ver_plan_sacd);
$oHashPlanSacd->setCamposForm('id_sacd!periodo!empiezamin!empiezamax');
$h_plan_sacd = $oHashPlanSacd->linkSinVal();

$a_campos = ['oPosicion' => $oPosicion,
    'oDesplSacd' => $oDesplSacd,
//    'oDesplPeriodo' => $oDesplPeriodo,
    'oFormP' => $oFormP,
    'url_ver_plan_sacd' => $url_ver_plan_sacd,
    'h_plan_sacd' => $h_plan_sacd,
];
 
$oView = new ViewTwig('misas/controller');
echo $oView->render('buscar_plan_sacd.html.twig', $a_campos);