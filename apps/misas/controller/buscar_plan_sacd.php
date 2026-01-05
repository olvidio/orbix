<?php

// INICIO Cabecera global de URL de controlador *********************************

use core\ConfigGlobal;
use core\ViewTwig;
use misas\domain\entity\InicialesSacd;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;
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
$shoy = $ohoy->format('d/m/Y');

$oFormP->setEmpiezaMin($shoy);
$oFormP->setEmpiezaMax($shoy);

$id_nom_jefe = '';

$UsuarioRepository = $GLOBALS['container']->get(UsuarioRepositoryInterface::class);
$oMiUsuario = $UsuarioRepository->findById(ConfigGlobal::mi_id_usuario());
$id_role = $oMiUsuario->getId_role();
//echo 'id_role: '.$id_role.'<br>';


$id_usuario = ConfigGlobal::mi_id_usuario();
//echo 'id_usuario: '.$id_usuario.'<br>';
$id_sacd = $oMiUsuario->getId_pauAsString();
//echo 'id_sacd: '.$id_sacd.'<br>';

$RoleRepository = $GLOBALS['container']->get(RoleRepositoryInterface::class);
$aRoles = $RoleRepository->getArrayRoles();

//echo 'aRoles'.$aRoles[$id_role].'<br>';

if (!empty($aRoles[$id_role]) && ($aRoles[$id_role] === 'p-sacd')) {

    if ($_SESSION['oConfig']->is_jefeCalendario()) {
        $id_nom_jefe = '';
    } else {
        $id_nom_jefe = $oMiUsuario->getId_pauAsString();
        if (empty($id_nom_jefe)) {
            exit(_("No tiene permiso para ver esta página"));
        }
    }
}
//echo 'jefe: '.$id_nom_jefe.'<br>';

$ZonaRepository = $GLOBALS['container']->get(ZonaRepositoryInterface::class);
$cZonas = $ZonaRepository->getZonas(array('id_nom' => $id_sacd));
//echo 'count zonas: '.count($cZonas).'<br>';
if (is_array($cZonas) && count($cZonas) > 0) {
    $ZonaSacdRepository = $GLOBALS['container']->get(ZonaSacdRepositoryInterface::class);
    foreach ($cZonas as $oZona) {
        $id_zona = $oZona->getId_zona();
        $a_id_nom = $ZonaSacdRepository->getIdSacdsDeZona($id_zona);
        foreach ($a_id_nom as $id_nom) {
//            echo $id_nom.'<br>';
            $InicialesSacd = new InicialesSacd();
            $sacd = $InicialesSacd->nombre_sacd($id_nom);
            $iniciales = $InicialesSacd->iniciales($id_nom);
            $key = $id_nom . '#' . $iniciales;
            $a_sacd[$key] = $sacd ?? '?';
        }
    }
} else { // No soy jefe de zona
    if (!is_null($id_sacd)) {
        $InicialesSacd = new InicialesSacd();
//        echo is_null($id_sacd).'='.($id_sacd=='').'=='.$id_sacd.'<br>';
        $sacd = $InicialesSacd->nombre_sacd($id_sacd);
//        echo is_null($id_sacd).'-->'.$sacd.'<br>';
        $iniciales = $InicialesSacd->iniciales($id_sacd);
        $key = $id_sacd . '#' . $iniciales;
        $a_sacd[$key] = $sacd ?? '?';
    }
}

if ($aRoles[$id_role] === 'Oficial_dl') {
    echo 'OFICIAL DL<br>';
    $aWhere = [];
    $aOperador = [];
    $aWhere['sacd'] = 't';
    $aWhere['situacion'] = 'A';
    $aWhere['id_tabla'] = "'n','a'";
    $aOperador['id_tabla'] = 'IN';
    $aWhere['_ordre'] = 'apellido1,apellido2,nom';
    $GesPersonas = new GestorPersonaSacd();
    $cPersonas = $GesPersonas->getPersonas($aWhere, $aOperador);
    foreach ($cPersonas as $oPersona) {
        $id_nom = $oPersona->getId_nom();
        $InicialesSacd = new InicialesSacd();
        $sacd = $InicialesSacd->nombre_sacd($id_nom);
        $iniciales = $InicialesSacd->iniciales($id_nom);
        $key = $id_nom . '#' . $iniciales;
        $a_sacd[$key] = $sacd ?? '?';
    }
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