<?php

// INICIO Cabecera global de URL de controlador *********************************

use core\ConfigGlobal;
use core\ViewTwig;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;
use web\DateTimeLocal;
use web\Desplegable;
use web\Hash;
use web\PeriodoQue;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_zona = (integer)filter_input(INPUT_POST, 'id_zona');

$id_nom_jefe = '';
$id_sacd = '';
$id_ubi = '';


$UsuarioRepository = $GLOBALS['container']->get(UsuarioRepositoryInterface::class);
$oMiUsuario = $UsuarioRepository->findById(ConfigGlobal::mi_id_usuario());
$id_role = $oMiUsuario->getId_role();

$RoleRepository = $GLOBALS['container']->get(RoleRepositoryInterface::class);
$aRoles = $RoleRepository->getArrayRoles();
echo $aRoles[$id_role];

if (!empty($aRoles[$id_role]) && ($aRoles[$id_role] === 'p-sacd')) {
    $id_sacd = $oMiUsuario->getId_pauAsString();
}

if (!empty($aRoles[$id_role]) && ($aRoles[$id_role] === 'Centro')) {
    $id_ubi = $oMiUsuario->getId_pauAsString();
}
echo ConfigGlobal::mi_id_usuario();
echo '-' . $id_sacd . '=' . $id_ubi;


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

$ZonaRepository = $GLOBALS['container']->get(ZonaRepositoryInterface::class);
$aOpciones = $ZonaRepository->getArrayZonas($id_nom_jefe);
if ($Qid_zona == 0) {
    $Qid_zona = array_key_first($aOpciones);
}

$oDesplZonas = new Desplegable();
$oDesplZonas->setOpciones($aOpciones);
$oDesplZonas->setBlanco(FALSE);
$oDesplZonas->setNombre('id_zona');
$oDesplZonas->setAction('fnjs_buscar_plan_ctr()');
$oDesplZonas->setOpcion_sel($Qid_zona);

$aCentros = [];
if (isset($Qid_zona)) {
    $aWhere = [];
    $aWhere['status'] = 't';
    $aWhere['id_zona'] = $Qid_zona;
    $aWhere['_ordre'] = 'nombre_ubi';
    $GesCentrosDl = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
    $cCentrosDl = $GesCentrosDl->getCentros($aWhere);
    $GesCentrosSf = $GLOBALS['container']->get(CentroEllasRepositoryInterface::class);
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
$shoy = $ohoy->format('d/m/Y');

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
    'id_ubi' => $id_ubi,
    'oFormP' => $oFormP,
    'url_buscar_plan_ctr' => $url_buscar_plan_ctr,
    'url_ver_plan_ctr' => $url_ver_plan_ctr,
    'h_plan_ctr' => $h_plan_ctr,
];

$oView = new ViewTwig('misas/controller');
if ($aRoles[$id_role] === 'p-sacd')
    echo $oView->render('buscar_plan_ctr.html.twig', $a_campos);
if ($aRoles[$id_role] === 'Centro')
    echo $oView->render('buscar_plan_un_ctr.html.twig', $a_campos);