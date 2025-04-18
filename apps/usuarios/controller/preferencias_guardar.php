<?php


// INICIO Cabecera global de URL de controlador *********************************
use core\ConfigGlobal;
use usuarios\model\entity\Preferencia;
use web\Hash;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos por esta url  **********************************************

// FIN de  Cabecera global de URL de controlador ********************************

$id_usuario = ConfigGlobal::mi_id_usuario();

$Qque = (string)filter_input(INPUT_POST, 'que');

switch ($Qque) {
    case "slickGrid":
        $Qtabla = (string)filter_input(INPUT_POST, 'tabla');
        $QsPrefs = (string)filter_input(INPUT_POST, 'sPrefs');
        $idioma = ConfigGlobal::mi_Idioma();
        $tipo = 'slickGrid_' . $Qtabla . '_' . $idioma;
        $oPref = new Preferencia(array('id_usuario' => $id_usuario, 'tipo' => $tipo));
        // si no se han cambiado las columnas visibles, pongo las actuales (sino las borra).
        $aPrefs = json_decode($QsPrefs, true, 512, JSON_THROW_ON_ERROR);
        if ($aPrefs['colVisible'] === 'noCambia') {
            $sPrefs_old = $oPref->getPreferencia();
            $aPrefs_old = json_decode($sPrefs_old, true);
            $aPrefs['colVisible'] = empty($aPrefs_old['colVisible']) ? '' : $aPrefs_old['colVisible'];
            $QsPrefs = json_encode($aPrefs, true);
        }

        $oPref->setPreferencia($QsPrefs);
        if ($oPref->DBGuardar() === false) {
            echo _("hay un error, no se ha guardado");
            echo "\n" . $oPref->getErrorTxt();
        }
        break;
    default:
        $Qoficina = (string)filter_input(INPUT_POST, 'oficina');
        $Qinicio = (string)filter_input(INPUT_POST, 'inicio');

        $Qoficina = empty($Qoficina) ? 'exterior' : $Qoficina;
        $Qinicio = empty($Qinicio) ? 'exterior' : $Qinicio;
        // Guardar página de inicio:
        $inicio = $Qinicio . "#" . $Qoficina;
        $oPref = new Preferencia(array('id_usuario' => $id_usuario, 'tipo' => 'inicio'));
        $oPref->setPreferencia($inicio);
        if ($oPref->DBGuardar() === false) {
            echo _("hay un error, no se ha guardado");
            echo "\n" . $oPref->getErrorTxt();
        }

        // Guardar estilo:
        $Qestilo_color = (string)filter_input(INPUT_POST, 'estilo_color');
        $Qtipo_menu = (string)filter_input(INPUT_POST, 'tipo_menu');
        $estilo = $Qestilo_color . "#" . $Qtipo_menu;
        $oPref = new Preferencia(array('id_usuario' => $id_usuario, 'tipo' => 'estilo'));
        $oPref->setPreferencia($estilo);
        if ($oPref->DBGuardar() === false) {
            echo _("hay un error, no se ha guardado");
            echo "\n" . $oPref->getErrorTxt();
        }

        // Guardar presentación tablas:
        $Qtipo_tabla = (string)filter_input(INPUT_POST, 'tipo_tabla');
        $oPref = new Preferencia(array('id_usuario' => $id_usuario, 'tipo' => 'tabla_presentacion'));
        $oPref->setPreferencia($Qtipo_tabla);
        if ($oPref->DBGuardar() === false) {
            echo _("hay un error, no se ha guardado");
            echo "\n" . $oPref->getErrorTxt();
        }

        // Guardar presentación nombre Apellidos:
        $QordenApellidos = (string)filter_input(INPUT_POST, 'ordenApellidos');
        $oPref = new Preferencia(array('id_usuario' => $id_usuario, 'tipo' => 'ordenApellidos'));
        $oPref->setPreferencia($QordenApellidos);
        if ($oPref->DBGuardar() === false) {
            echo _("hay un error, no se ha guardado");
            echo "\n" . $oPref->getErrorTxt();
        }
        $_SESSION['session_auth']['ordenApellidos'] = $QordenApellidos;

        // Guardar idioma:
        $Qidioma_nou = (string)filter_input(INPUT_POST, 'idioma_nou');
        $oPref = new Preferencia(array('id_usuario' => $id_usuario, 'tipo' => 'idioma'));
        $oPref->setPreferencia($Qidioma_nou);
        if ($oPref->DBGuardar() === false) {
            echo _("hay un error, no se ha guardado");
            echo "\n" . $oPref->getErrorTxt();
        }

        // Guardar zona_horaria:
        $Qzona_horaria_nou = (string)filter_input(INPUT_POST, 'zona_horaria_nou');
        // mejor guardar la zona en text, porque el identificador cambia según la versión de php
        $a_zonas_horarias = DateTimeZone::listIdentifiers();
        $zona_horaria_txt = $a_zonas_horarias[$Qzona_horaria_nou];
        $oPref = new Preferencia(array('id_usuario' => $id_usuario, 'tipo' => 'zona_horaria'));
        $oPref->setPreferencia($zona_horaria_txt);
        if ($oPref->DBGuardar() === false) {
            echo _("hay un error, no se ha guardado");
            echo "\n" . $oPref->getErrorTxt();
        }

        // volver a la página de configuración
        $location = Hash::link(ConfigGlobal::getWeb() . '/index.php?' . http_build_query(array('PHPSESSID' => session_id())));
        echo "<body onload=\"$location\"></body>";
}
