<?php

use permisos\model\MyCrypt;
use src\usuarios\application\repositories\RoleRepository;
use src\usuarios\application\repositories\UsuarioRepository;
use src\usuarios\domain\entity\Usuario;
use src\usuarios\domain\value_objects\Username;
use src\usuarios\domain\value_objects\Email;
use src\usuarios\domain\value_objects\Password;
use src\usuarios\domain\value_objects\IdPau;
use src\usuarios\domain\value_objects\NombreUsuario;
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

$Qcambio_password = (bool)filter_input(INPUT_POST, 'cambio_password');
$Qhas_2fa = (bool)filter_input(INPUT_POST, 'has_2fa');

$RoleRepository = new RoleRepository();
$UsuarioRepository = new UsuarioRepository();

if (empty($Qid_usuario)) {
    $UsuarioRepository = new UsuarioRepository();
    $id_new_usuario = $UsuarioRepository->getNewId();
    $oUsuario = new Usuario();
    $oUsuario->setId_usuario($id_new_usuario);
} else {
    $oUsuario = $UsuarioRepository->findById($Qid_usuario);
}
$oUsuario->setUsuario(new Username($Qusuario));
$oUsuario->setid_role($Qid_role);
$oUsuario->setEmail(!empty($Qemail) ? new Email($Qemail) : null);
$oUsuario->setNom_usuario(!empty($Qnom_usuario) ? new NombreUsuario($Qnom_usuario) : null);
$oUsuario->setCambio_password($Qcambio_password);
$oUsuario->setHas2fa($Qhas_2fa);
if (!empty($Qpassword)) {
    $oCrypt = new MyCrypt();
    $my_passwd = $oCrypt->encode($Qpassword);
    $oUsuario->setPassword(new Password($my_passwd));
}
$oRole = $RoleRepository->findById($Qid_role);
$pau = $oRole->getPauAsString();
// sacd
if (($pau === 'sacd' || $pau === 'nom') && !empty($Qid_nom)) {
    $oUsuario->setId_pau(new IdPau((string)$Qid_nom));
}
// centros (sv o sf)
if (($pau === 'ctr') && !empty($Qid_ctr)) {
    $oUsuario->setId_pau(new IdPau((string)$Qid_ctr));
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
    $oUsuario->setId_pau(new IdPau($txt_casa));
}

if ($UsuarioRepository->Guardar($oUsuario) === false) {
    $error_txt .= _("hay un error, no se ha guardado");
    $error_txt .= "\n" . $UsuarioRepository->getErrorTxt();
}

ContestarJson::enviar($error_txt, 'ok');
