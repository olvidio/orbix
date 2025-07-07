<?php

// INICIO Cabecera global de URL de controlador *********************************

use core\ConfigGlobal;
use core\ViewTwig;
use misas\domain\entity\EncargoDia;
use src\usuarios\application\repositories\PreferenciaRepository;
use src\usuarios\application\repositories\RoleRepository;
use src\usuarios\application\repositories\UsuarioRepository;
use web\DateTimeLocal;
use web\Desplegable;
use web\Hash;
use web\PeriodoQue;
use zonassacd\model\entity\GestorZona;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************
$aOpciones = array(
    'proxima_semana' => _("próxima semana de lunes a domingo"),
    'proximo_mes' => _("próximo mes natural"),
    'otro' => _("otro")
);

$oFormP = new PeriodoQue();
$oFormP->setFormName('frm_nuevo_periodo');
$oFormP->setTitulo(core\strtoupper_dlb(_("seleccionar un periodo")));
$oFormP->setPosiblesPeriodos($aOpciones);
$oFormP->setDesplPeriodosOpcion_sel('proxima_semana');
$oFormP->setisDesplAnysVisible(FALSE);

$ohoy = new DateTimeLocal(date('Y-m-d'));
$shoy = $ohoy ->format('d/m/Y');

$oFormP->setEmpiezaMin($shoy);
$oFormP->setEmpiezaMax($shoy);

$id_nom_jefe = '';

$UsuarioRepository = new UsuarioRepository();
$oMiUsuario = $UsuarioRepository->findById(ConfigGlobal::mi_id_usuario());
$id_role = $oMiUsuario->getId_role();

$RoleRepository = new RoleRepository();
$aRoles = $RoleRepository->getArrayRoles();

if (!empty($aRoles[$id_role]) && ($aRoles[$id_role] === 'p-sacd')) {

    if ($_SESSION['oConfig']->is_jefeCalendario()) {
        $id_nom_jefe = '';
    } else {
        $id_nom_jefe = $oMiUsuario->getId_pau();
        if (empty($id_nom_jefe)) {
            exit(_("No tiene permiso para ver esta página"));
        }
    }
}

$id_nom_jefe = '';

$UsuarioRepository = new UsuarioRepository();
$oMiUsuario = $UsuarioRepository->findById(ConfigGlobal::mi_id_usuario());
$id_role = $oMiUsuario->getId_role();

$RoleRepository = new RoleRepository();
$aRoles = $RoleRepository->getArrayRoles();

if (!empty($aRoles[$id_role]) && ($aRoles[$id_role] === 'p-sacd')) {

    if ($_SESSION['oConfig']->is_jefeCalendario()) {
        $id_nom_jefe = '';
    } else {
        $id_nom_jefe = $oMiUsuario->getId_pau();
        if (empty($id_nom_jefe)) {
            exit(_("No tiene permiso para ver esta página"));
        }
    }
}

$oGestorZona = new GestorZona();
$oDesplZonas = $oGestorZona->getListaZonas($id_nom_jefe);
$oDesplZonas->setNombre('id_zona');
$oDesplZonas->setAction('fnjs_ver_cuadricula_zona()');

$a_TiposPlantilla = array(
    EncargoDia::PLANTILLA_SEMANAL_UNO=>'semanal una opción',
    EncargoDia::PLANTILLA_DOMINGOS_UNO=>'semanal y domingos una opción',
    EncargoDia::PLANTILLA_MENSUAL_UNO=>'mensual una opción',
    EncargoDia::PLANTILLA_SEMANAL_TRES=>'semanal tres opciones',
    EncargoDia::PLANTILLA_DOMINGOS_TRES=>'semanal y domingos tres opciones',
    EncargoDia::PLANTILLA_MENSUAL_TRES=>'mensual tres opciones',
);

$PreferenciaRepository = new PreferenciaRepository();

$id_usuario = ConfigGlobal::mi_id_usuario();
$aPref = $PreferenciaRepository->getPreferencias(array('id_usuario' => $id_usuario, 'tipo' => 'ultima_plantilla'));
if (count($aPref) > 0) {
    $oPreferencia = $aPref[0];
    $ultima_plantilla = $oPreferencia->getPreferencia();
} else {
    // valores por defecto
    $ultima_plantilla=EncargoDia::PLANTILLA_SEMANAL_TRES;
}


$oDesplTipoPlantilla = new Desplegable();
$oDesplTipoPlantilla->setOpciones($a_TiposPlantilla);
$oDesplTipoPlantilla->setNombre('tipoplantilla');
$oDesplTipoPlantilla->setOpcion_sel($ultima_plantilla);
$oDesplTipoPlantilla->setAction('fnjs_ver_cuadricula_zona()');

$a_Orden = array(
    'orden' => 'orden',
    'prioridad' => 'prioridad',
    'desc_enc' => 'alfabético',
);

$oDesplOrden = new Desplegable();
$oDesplOrden->setOpciones($a_Orden);
$oDesplOrden->setNombre('orden');
$oDesplOrden->setAction('fnjs_ver_cuadricula_zona()');

$url_crear_nuevo_periodo = 'apps/misas/controller/crear_nuevo_periodo.php';
$oHashNuevoPeriodo = new Hash();
$oHashNuevoPeriodo->setUrl($url_crear_nuevo_periodo);
$oHashNuevoPeriodo->setCamposForm('id_zona!tipoplantilla!periodo!empiezamin!empiezamax');
$h_nuevo_periodo = $oHashNuevoPeriodo->linkSinVal();

$url_ver_cuadricula_zona = 'apps/misas/controller/ver_cuadricula_zona.php';
$oHashZonaPeriodo = new Hash();
$oHashZonaPeriodo->setUrl($url_ver_cuadricula_zona);
$oHashZonaPeriodo->setCamposForm('id_zona!periodo!empiezamin!empiezamax!orden!tipo_plantilla');
$h_zona_periodo = $oHashZonaPeriodo->linkSinVal();

$a_campos = ['oPosicion' => $oPosicion,
    'oDesplZonas' => $oDesplZonas,
    'oDesplTipoPlantilla' => $oDesplTipoPlantilla,
    'oDesplOrden' => $oDesplOrden,
    'oFormP' => $oFormP,
    'url_crear_nuevo_periodo' => $url_crear_nuevo_periodo,
    'h_nuevo_periodo' => $h_nuevo_periodo,
    'url_ver_cuadricula_zona' => $url_ver_cuadricula_zona,
    'h_zona_periodo' => $h_zona_periodo,
];

$oView = new ViewTwig('misas/controller');
echo $oView->render('preparar_plan_de_misas.html.twig', $a_campos);
