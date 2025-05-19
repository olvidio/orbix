<?php

// INICIO Cabecera global de URL de controlador *********************************

use core\ConfigGlobal;
use core\ViewTwig;
use misas\domain\entity\EncargoDia;
use src\usuarios\application\repositories\PreferenciaRepository;
use web\Desplegable;
use web\Hash;
use zonassacd\model\entity\GestorZona;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oGestorZona = new GestorZona();
$oDesplZonas = $oGestorZona->getListaZonas();
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
$oDesplTipoPlantilla->setNombre('tipo_plantilla');
$oDesplTipoPlantilla->setOpcion_sel($ultima_plantilla);
$oDesplTipoPlantilla->setAction('fnjs_ver_plantilla_zona()');

$url_importar_plantilla = 'apps/misas/controller/importar_plantilla.php';
$oHashImportarPlantilla = new Hash();
$oHashImportarPlantilla->setUrl($url_importar_plantilla);
$oHashImportarPlantilla->setCamposForm('id_zona!tipo_plantilla_origen!tipo_plantilla_destino');
$h_importar_plantilla = $oHashImportarPlantilla->linkSinVal();

$oDesplImportarDePlantilla = new Desplegable();
$oDesplImportarDePlantilla->setOpciones($a_TiposPlantilla);
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

$url_ver_cuadricula_zona = 'apps/misas/controller/ver_cuadricula_zona.php';
$oHashZonaTipo = new Hash();
$oHashZonaTipo->setUrl($url_ver_cuadricula_zona);
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
    'url_ver_cuadricula_zona' => $url_ver_cuadricula_zona,
    'url_importar_plantilla' => $url_importar_plantilla,
    'h_zona_tipo' => $h_zona_tipo,
    'h_importar_plantilla' => $h_importar_plantilla,
];

$oView = new ViewTwig('misas/controller');
echo $oView->render('modificar_plantilla.html.twig', $a_campos);