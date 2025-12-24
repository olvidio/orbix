<?php

use core\ConfigGlobal;
use src\ubis\domain\entity\TelecoUbi;
use function core\urlsafe_b64decode;

/**
 * Para asegurar que inicia la sesion, y poder acceder a los permisos
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oMiUsuario = ConfigGlobal::MiUsuario();
$miSfsv = ConfigGlobal::mi_sfsv();

$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');
$Qmod = (string)filter_input(INPUT_POST, 'mod');
$Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');
$Qcampos_chk = (string)filter_input(INPUT_POST, 'campos_chk');

switch ($Qobj_pau) {
    case 'CentroDl':
        $repo = 'src\\ubis\\application\\repositories\\TelecoCtrDlRepository';
        break;
    case 'CentroEx':
        $repo = 'src\\ubis\\application\\repositories\\TelecoCtrExRepository';
        break;
    case 'CasaDl':
        $repo = 'src\\ubis\\application\\repositories\\TelecoCdcDlRepository';
        break;
    case 'CasaEx':
        $repo = 'src\\ubis\\application\\repositories\\TelecoCdcExRepository';
        break;
}

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    $s_pkey = explode('#', $a_sel[0]);
    // he cambiado las comillas dobles por simples. Deshago el cambio.
    $s_pkey = str_replace("'", '"', $s_pkey[0]);
    $a_pkey = json_decode(urlsafe_b64decode($s_pkey));
} else {
    $s_pkey = (string)filter_input(INPUT_POST, 's_pkey');
    $a_pkey = json_decode(urlsafe_b64decode($s_pkey));

}

switch ($Qmod) {
    case 'eliminar_teleco':
        $Repository = new $repo();
        $TelecoUbi = $Repository->findById($a_pkey);
        $Repository->Eliminar($TelecoUbi);
        die();
        break;
    case 'teleco':
        $Qid_tipo_teleco = (integer)filter_input(INPUT_POST, 'id_tipo_teleco');
        $Qdesc_teleco = (integer)filter_input(INPUT_POST, 'id_desc_teleco');
        $Qnum_teleco = (string)filter_input(INPUT_POST, 'num_teleco');
        $Qobserv = (string)filter_input(INPUT_POST, 'observ');

        $Repository = new $repo();
        if (empty($a_pkey)) {
            // es nuevo
            $newId = $Repository->getNewId();
            $TelecoUbi = new TelecoUbi();
            $TelecoUbi->setId_item($newId);
            $TelecoUbi->setId_ubi($Qid_ubi);
        } else {
            $TelecoUbi = $Repository->findById($a_pkey);
        }
        $TelecoUbi->setId_tipo_teleco($Qid_tipo_teleco);
        $TelecoUbi->setId_desc_teleco($Qdesc_teleco);
        $TelecoUbi->setNum_teleco($Qnum_teleco);
        $TelecoUbi->setObserv($Qobserv);
        $Repository->Guardar($TelecoUbi);

        break;
}
