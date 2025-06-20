<?php


// INICIO Cabecera global de URL de controlador *********************************
use core\ConfigGlobal;
use src\usuarios\application\repositories\PreferenciaRepository;
use src\usuarios\domain\entity\Preferencia;
use src\usuarios\domain\value_objects\TipoPreferencia;
use src\usuarios\domain\value_objects\ValorPreferencia;
use web\ContestarJson;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos por esta url  **********************************************

// FIN de  Cabecera global de URL de controlador ********************************

$id_usuario = ConfigGlobal::mi_id_usuario();

$Qque = (string)filter_input(INPUT_POST, 'que');

$PreferenciaRepository = new PreferenciaRepository();

$error_txt = '';
if ($Qque === "slickGrid") {
    $Qtabla = (string)filter_input(INPUT_POST, 'tabla');
    $QsPrefs = (string)filter_input(INPUT_POST, 'sPrefs');
    $idioma = ConfigGlobal::mi_Idioma();
    $tipo = 'slickGrid_' . $Qtabla . '_' . $idioma;
    $oPreferencia = $PreferenciaRepository->findById($id_usuario, $tipo);
    if ($oPreferencia === null) {
        $oPreferencia = new Preferencia();
        $oPreferencia->setId_usuario($id_usuario);
        $oPreferencia->setTipo(new TipoPreferencia($tipo));
    }
    // si no se han cambiado las columnas visibles, pongo las actuales (sino las borra).
    $aPrefs = json_decode($QsPrefs, true, 512, JSON_THROW_ON_ERROR);
    if ($aPrefs['colVisible'] === 'noCambia') {
        $sPrefs_old = $oPreferencia->getPreferencia();
        $aPrefs_old = json_decode($sPrefs_old, true);
        $aPrefs['colVisible'] = empty($aPrefs_old['colVisible']) ? '' : $aPrefs_old['colVisible'];
        $QsPrefs = json_encode($aPrefs, true);
    }

    $oPreferencia->setPreferencia(new ValorPreferencia($QsPrefs));
    if ($PreferenciaRepository->Guardar($oPreferencia) === false) {
        $error_txt .= _("hay un error, no se ha guardado");
        $error_txt .= "\n" . $PreferenciaRepository->getErrorTxt();
    }
} else {
    // Guardar Layout:
    $Qlayout = (string)filter_input(INPUT_POST, 'layout');
    $oPreferencia = $PreferenciaRepository->findById($id_usuario, 'layout');
    if ($oPreferencia === null) {
        $oPreferencia = new Preferencia();
        $oPreferencia->setId_usuario($id_usuario);
        $oPreferencia->setTipo(new TipoPreferencia('layout'));
    }
    $oPreferencia->setPreferencia(new ValorPreferencia($Qlayout));
    if ($PreferenciaRepository->Guardar($oPreferencia) === false) {
        $error_txt .= _("hay un error, no se ha guardado");
        $error_txt .= "\n" . $PreferenciaRepository->getErrorTxt();
    }

    $Qoficina = (string)filter_input(INPUT_POST, 'oficina');
    $Qinicio = (string)filter_input(INPUT_POST, 'inicio');

    $Qoficina = empty($Qoficina) ? 'exterior' : $Qoficina;
    $Qinicio = empty($Qinicio) ? 'exterior' : $Qinicio;

    // Guardar página de inicio:
    $inicio = $Qinicio . "#" . $Qoficina;
    $oPreferencia = $PreferenciaRepository->findById($id_usuario, 'inicio');
    if ($oPreferencia === null) {
        $oPreferencia = new Preferencia();
        $oPreferencia->setId_usuario($id_usuario);
        $oPreferencia->setTipo(new TipoPreferencia('inicio'));
    }
    $oPreferencia->setPreferencia(new ValorPreferencia($inicio));
    if ($PreferenciaRepository->Guardar($oPreferencia) === false) {
        $error_txt .= _("hay un error, no se ha guardado");
        $error_txt .= "\n" . $PreferenciaRepository->getErrorTxt();
    }

    // Guardar estilo:
    $Qestilo_color = (string)filter_input(INPUT_POST, 'estilo_color');
    $Qtipo_menu = (string)filter_input(INPUT_POST, 'tipo_menu');
    $estilo = $Qestilo_color . "#" . $Qtipo_menu;
    $oPreferencia = $PreferenciaRepository->findById($id_usuario, 'estilo');
    if ($oPreferencia === null) {
        $oPreferencia = new Preferencia();
        $oPreferencia->setId_usuario($id_usuario);
        $oPreferencia->setTipo(new TipoPreferencia('estilo'));
    }
    $oPreferencia->setPreferencia(new ValorPreferencia($estilo));
    if ($PreferenciaRepository->Guardar($oPreferencia) === false) {
        $error_txt .= _("hay un error, no se ha guardado");
        $error_txt .= "\n" . $PreferenciaRepository->getErrorTxt();
    }

    // Guardar presentación tablas:
    $Qtipo_tabla = (string)filter_input(INPUT_POST, 'tipo_tabla');
    $oPreferencia = $PreferenciaRepository->findById($id_usuario, 'tabla_presentacion');
    if ($oPreferencia === null) {
        $oPreferencia = new Preferencia();
        $oPreferencia->setId_usuario($id_usuario);
        $oPreferencia->setTipo(new TipoPreferencia('tabla_presentacion'));
    }
    $oPreferencia->setPreferencia(new ValorPreferencia($Qtipo_tabla));
    if ($PreferenciaRepository->Guardar($oPreferencia) === false) {
        $error_txt .= _("hay un error, no se ha guardado");
        $error_txt .= "\n" . $PreferenciaRepository->getErrorTxt();
    }

    // Guardar presentación nombre Apellidos:
    $QordenApellidos = (string)filter_input(INPUT_POST, 'ordenApellidos');
    $oPreferencia = $PreferenciaRepository->findById($id_usuario, 'ordenApellidos');
    if ($oPreferencia === null) {
        $oPreferencia = new Preferencia();
        $oPreferencia->setId_usuario($id_usuario);
        $oPreferencia->setTipo(new TipoPreferencia('ordenApellidos'));
    }
    $oPreferencia->setPreferencia(new ValorPreferencia($QordenApellidos));
    if ($PreferenciaRepository->Guardar($oPreferencia) === false) {
        $error_txt .= _("hay un error, no se ha guardado");
        $error_txt .= "\n" . $PreferenciaRepository->getErrorTxt();
    }
    $_SESSION['session_auth']['ordenApellidos'] = $QordenApellidos;

    // Guardar idioma:
    $Qidioma_nou = (string)filter_input(INPUT_POST, 'idioma_nou');
    $oPreferencia = $PreferenciaRepository->findById($id_usuario, 'idioma');
    if ($oPreferencia === null) {
        $oPreferencia = new Preferencia();
        $oPreferencia->setId_usuario($id_usuario);
        $oPreferencia->setTipo(new TipoPreferencia('idioma'));
    }
    $oPreferencia->setPreferencia(new ValorPreferencia($Qidioma_nou));
    if ($PreferenciaRepository->Guardar($oPreferencia) === false) {
        $error_txt .= _("hay un error, no se ha guardado");
        $error_txt .= "\n" . $PreferenciaRepository->getErrorTxt();
    }

    // Guardar zona_horaria:
    $Qzona_horaria_nou = (string)filter_input(INPUT_POST, 'zona_horaria_nou');
    // mejor guardar la zona en text, porque el identificador cambia según la versión de php
    $a_zonas_horarias = DateTimeZone::listIdentifiers();
    $zona_horaria_txt = $a_zonas_horarias[$Qzona_horaria_nou];
    $oPreferencia = $PreferenciaRepository->findById($id_usuario, 'zona_horaria');
    if ($oPreferencia === null) {
        $oPreferencia = new Preferencia();
        $oPreferencia->setId_usuario($id_usuario);
        $oPreferencia->setTipo(new TipoPreferencia('zona_horaria'));
    }
    $oPreferencia->setPreferencia(new ValorPreferencia($zona_horaria_txt));
    if ($PreferenciaRepository->Guardar($oPreferencia) === false) {
        $error_txt .= _("hay un error, no se ha guardado");
        $error_txt .= "\n" . $PreferenciaRepository->getErrorTxt();
    }


    ContestarJson::enviar($error_txt, 'ok');
}
