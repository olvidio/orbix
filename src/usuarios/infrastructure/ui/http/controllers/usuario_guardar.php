<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;
use src\shared\domain\helpers\FuncTablasSupport;

use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\usuarios\domain\entity\Usuario;
use src\usuarios\domain\PasswordHasher;
use src\usuarios\domain\value_objects\Username;
use src\usuarios\domain\value_objects\Email;
use src\usuarios\domain\value_objects\Password;
use src\usuarios\domain\value_objects\IdPau;
use src\usuarios\domain\value_objects\NombreUsuario;
use src\shared\web\ContestarJson;
$ctxRaw = (string)FilterPostGet::post('ctx');
try {
    $opened = HashB::open($ctxRaw, 'usuario_guardar');
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$que_user = FuncTablasSupport::inputString($opened, 'que_user');
$id_usuario_ctx = FuncTablasSupport::inputInt($opened, 'id_usuario');
if ($que_user !== 'nuevo' && $que_user !== 'guardar') {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}
if ($que_user === 'nuevo' && $id_usuario_ctx !== 0) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}
if ($que_user === 'guardar' && $id_usuario_ctx <= 0) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$Qusuario = (string)FilterPostGet::post('usuario');

$error_txt = '';
if (empty($Qusuario)) {
    $error_txt .= _("debe poner un nombre");
}

$Qid_usuario = ($que_user === 'nuevo') ? 0 : $id_usuario_ctx;
$Qperm_activ = (array)FilterPostGet::post('perm_activ', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$Qid_role = (integer)FilterPostGet::post('id_role');
$Qemail = (string)FilterPostGet::post('email', FILTER_VALIDATE_EMAIL);

$Qnom_usuario = (string)FilterPostGet::post('nom_usuario');
$Qpassword = (string)FilterPostGet::post('password');
$Qpass = (string)FilterPostGet::post('pass');
$Qid_nom = (integer)FilterPostGet::post('id_nom');
$Qid_ctr = (integer)FilterPostGet::post('id_ctr');
$Qcasas = (array)FilterPostGet::post('casas', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

$Qcambio_password = (bool)FilterPostGet::post('cambio_password');
$Qhas_2fa = (bool)FilterPostGet::post('has_2fa');

$RoleRepository = DependencyResolver::get(RoleRepositoryInterface::class);
$UsuarioRepository = DependencyResolver::get(UsuarioRepositoryInterface::class);

if (empty($Qid_usuario)) {
    $UsuarioRepository = DependencyResolver::get(UsuarioRepositoryInterface::class);
    $id_new_usuario = $UsuarioRepository->getNewId();
    $oUsuario = new Usuario();
    $oUsuario->setId_usuario($id_new_usuario);
} else {
    $oUsuario = $UsuarioRepository->findById($Qid_usuario);
    if ($oUsuario === null) {
        ContestarJson::enviar(_('Usuario no encontrado'), 'none');
        return;
    }
}
$oUsuario->setUsuarioVo(new Username($Qusuario));
$oUsuario->setid_role($Qid_role);
$oUsuario->setEmailVo(!empty($Qemail) ? new Email($Qemail) : null);
$oUsuario->setNomUsuarioVo(!empty($Qnom_usuario) ? new NombreUsuario($Qnom_usuario) : null);
$oUsuario->setCambio_password($Qcambio_password);
$oUsuario->setHas_2fa($Qhas_2fa);
if (!empty($Qpassword)) {
    $oCrypt = new PasswordHasher();
    $my_passwd = $oCrypt->encode($Qpassword);
    $oUsuario->setPasswordVo(new Password($my_passwd));
}
$oRole = $RoleRepository->findById($Qid_role);
if ($oRole === null) {
    ContestarJson::enviar(_('Rol no encontrado'), 'none');
    return;
}
$pau = $oRole->getPauAsString();
// sacd
if (($pau === 'sacd' || $pau === 'nom') && !empty($Qid_nom)) {
    $oUsuario->setCsvIdPauVo(new IdPau((string)$Qid_nom));
}
// centros (sv o sf)
if (($pau === 'ctr') && !empty($Qid_ctr)) {
    $oUsuario->setCsvIdPauVo(new IdPau((string)$Qid_ctr));
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
    $oUsuario->setCsvIdPauVo(new IdPau($txt_casa));
}

if ($UsuarioRepository->Guardar($oUsuario) === false) {
    $error_txt .= _("hay un error, no se ha guardado");
    $error_txt .= "\n" . $UsuarioRepository->getErrorTxt();
}

ContestarJson::enviar($error_txt, 'ok');
