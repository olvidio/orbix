<?php

use permisos\model\MyCrypt;
use usuarios\model\entity\Role;
use usuarios\model\entity\Usuario;
use web\ContestarJson;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos por esta url  **********************************************

// FIN de  Cabecera global de URL de controlador ********************************

$Qusuario = (string)filter_input(INPUT_POST, 'usuario');

$error_txt = '';
if (empty($Qusuario)) {
    $error_txt .= _("debe poner un nombre");
}

$Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');
$Qperm_activ = (array)filter_input(INPUT_POST, 'perm_activ', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$Qid_role = (integer)filter_input(INPUT_POST, 'id_role');
$Qemail = (string)filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

$Qnom_usuario = (string)filter_input(INPUT_POST, 'nom_usuario');
$Qpassword = (string)filter_input(INPUT_POST, 'password');
$Qpass = (string)filter_input(INPUT_POST, 'pass');
$Qid_nom = (integer)filter_input(INPUT_POST, 'id_nom');
$Qid_ctr = (integer)filter_input(INPUT_POST, 'id_ctr');
$Qcasas = (array)filter_input(INPUT_POST, 'casas', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

$oUsuario = new Usuario(array('id_usuario' => $Qid_usuario));
$oUsuario->setUsuario($Qusuario);
$oUsuario->setid_role($Qid_role);
$oUsuario->setEmail($Qemail);
$oUsuario->setNom_usuario($Qnom_usuario);
if (!empty($Qpassword)) {
    $oCrypt = new MyCrypt();
    $my_passwd = $oCrypt->encode($Qpassword);
    $oUsuario->setPassword($my_passwd);
} else {
    $oUsuario->setPassword($Qpass);
}
$oRole = new Role($Qid_role);
$pau = $oRole->getPau();
// sacd
if (($pau === 'sacd' || $pau === 'nom') && !empty($Qid_nom)) {
    $oUsuario->setId_pau($Qid_nom);
}
// centros (sv o sf)
if (($pau === 'ctr') && !empty($Qid_ctr)) {
    $oUsuario->setId_pau($Qid_ctr);
}
// casas
if ($pau === 'cdc' && !empty($Qcasas)) {
    $txt_casa = '';
    $i = 0;
    foreach ($Qcasas as $id_ubi) {
        if (empty($id_ubi)) continue;
        $i++;
        if ($i > 1) $txt_casa .= ',';
        $txt_casa .= $id_ubi;
    }
    $oUsuario->setId_pau($txt_casa);
}

if ($oUsuario->DBGuardar() === false) {
    $error_txt .= _("hay un error, no se ha guardado");
    $error_txt .= "\n" . $oUsuario->getErrorTxt();
}

ContestarJson::enviar($error_txt, 'ok');