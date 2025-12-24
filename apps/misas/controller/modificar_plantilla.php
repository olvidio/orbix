<?php

// INICIO Cabecera global de URL de controlador *********************************

use core\ConfigGlobal;
use core\ViewTwig;
use misas\domain\entity\EncargoDia;
use src\usuarios\domain\contracts\PreferenciaRepositoryInterface;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;
use web\Desplegable;
use web\Hash;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$id_nom_jefe = '';

$UsuarioRepository = $GLOBALS['container']->get(UsuarioRepositoryInterface::class);
$oMiUsuario = $UsuarioRepository->findById(ConfigGlobal::mi_id_usuario());
$id_role = $oMiUsuario->getId_role();

$RoleRepository = $GLOBALS['container']->get(RoleRepositoryInterface::class);
$aRoles = $RoleRepository->getArrayRoles();

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
$oDesplZonas = new Desplegable();
$oDesplZonas->setOpciones($aOpciones);
$oDesplZonas->setBlanco(FALSE);
$oDesplZonas->setNombre('id_zona');
$oDesplZonas->setAction('fnjs_ver_plantilla_zona()');

$a_TiposPlantilla = array(
    EncargoDia::PLANTILLA_SEMANAL_UNO=>'semanal una opción',
    EncargoDia::PLANTILLA_DOMINGOS_UNO=>'semanal y domingos una opción',
    EncargoDia::PLANTILLA_MENSUAL_UNO=>'mensual una opción',
    EncargoDia::PLANTILLA_SEMANAL_TRES=>'semanal tres opciones',
    EncargoDia::PLANTILLA_DOMINGOS_TRES=>'semanal y domingos tres opciones',
    EncargoDia::PLANTILLA_MENSUAL_TRES=>'mensual tres opciones',
);
$a_TiposPlantilla2 = array(
    '-'=>'',
    EncargoDia::PLANTILLA_SEMANAL_UNO=>'semanal una opción',
    EncargoDia::PLANTILLA_DOMINGOS_UNO=>'semanal y domingos una opción',
    EncargoDia::PLANTILLA_MENSUAL_UNO=>'mensual una opción',
    EncargoDia::PLANTILLA_SEMANAL_TRES=>'semanal tres opciones',
    EncargoDia::PLANTILLA_DOMINGOS_TRES=>'semanal y domingos tres opciones',
    EncargoDia::PLANTILLA_MENSUAL_TRES=>'mensual tres opciones',
);

$PreferenciaRepository = $GLOBALS['container']->get(PreferenciaRepositoryInterface::class);

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
$oDesplTipoPlantilla->setNombre('tipo_plantilla');
$oDesplTipoPlantilla->setOpcion_sel($ultima_plantilla);
$oDesplTipoPlantilla->setAction('fnjs_ver_plantilla_zona()');

$url_importar_plantilla = 'apps/misas/controller/importar_plantilla.php';
$oHashImportarPlantilla = new Hash();
$oHashImportarPlantilla->setUrl($url_importar_plantilla);
$oHashImportarPlantilla->setCamposForm('id_zona!tipo_plantilla_origen!tipo_plantilla_destino');
$h_importar_plantilla = $oHashImportarPlantilla->linkSinVal();

$oDesplImportarDePlantilla = new Desplegable();
$oDesplImportarDePlantilla->setOpciones($a_TiposPlantilla2);
$oDesplImportarDePlantilla->setNombre('importar_de_plantilla');
//$oDesplImportarDePlantilla->setAction('fnjs_importar_de_plantilla_zona()');

$a_Orden = array(
    'orden' => 'orden',
    'prioridad' => 'prioridad',
    'desc_enc' => 'alfabético',
);

$oDesplOrden = new Desplegable();
$oDesplOrden->setOpciones($a_Orden);
$oDesplOrden->setNombre('orden');
$oDesplOrden->setAction('fnjs_ver_plantilla_zona()');

$url_modificar_cuadricula_zona = 'apps/misas/controller/modificar_cuadricula_zona.php';
$oHashZonaTipo = new Hash();
$oHashZonaTipo->setUrl($url_modificar_cuadricula_zona);
$oHashZonaTipo->setCamposForm('id_zona!tipo_plantilla!orden');
$h_zona_tipo = $oHashZonaTipo->linkSinVal();

$url_crear_nuevo_periodo = 'apps/misas/controller/crear_nuevo_periodo.php';
$oHashNuevoPeriodo = new Hash();
$oHashNuevoPeriodo->setUrl($url_crear_nuevo_periodo);
$oHashNuevoPeriodo->setCamposForm('id_zona!tipoplantilla!periodo!empiezamin!empiezamax');
$h_nuevo_periodo = $oHashNuevoPeriodo->linkSinVal();

$a_campos = ['oPosicion' => $oPosicion,
    'oDesplZonas' => $oDesplZonas,
    'oDesplTipoPlantilla' => $oDesplTipoPlantilla,
    'oDesplImportarDePlantilla' => $oDesplImportarDePlantilla,
    'oDesplOrden' => $oDesplOrden,
    'url_modificar_cuadricula_zona' => $url_modificar_cuadricula_zona,
    'url_importar_plantilla' => $url_importar_plantilla,
    'h_zona_tipo' => $h_zona_tipo,
    'h_importar_plantilla' => $h_importar_plantilla,
];

$oView = new ViewTwig('misas/controller');
echo $oView->render('modificar_plantilla.html.twig', $a_campos);