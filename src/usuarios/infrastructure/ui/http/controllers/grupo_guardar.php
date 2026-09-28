<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\usuarios\domain\contracts\GrupoRepositoryInterface;
use src\usuarios\domain\entity\Grupo;
use src\usuarios\domain\value_objects\Username;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        (string)\src\shared\domain\helpers\FilterPostGet::post('ctx_guardar'),
        'grupo_guardar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$que_user = \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'que_user');
$id_usuario_ctx = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_usuario');
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

$Qusuario = (string)\src\shared\domain\helpers\FilterPostGet::post('usuario');

$error_txt = '';
if (empty($Qusuario)) {
    $error_txt .= _("debe poner un nombre");
}
$Qid_usuario = ($que_user === 'nuevo') ? 0 : $id_usuario_ctx;

$GrupoRepository = DependencyResolver::get(GrupoRepositoryInterface::class);
if (empty($Qid_usuario)) {
    $id_usuario_new = $GrupoRepository->getNewId();
    $oGrupo = new Grupo();
    $oGrupo->setId_usuario($id_usuario_new);
} else {
    $oGrupo = $GrupoRepository->findById($Qid_usuario);
    if ($oGrupo === null) {
        ContestarJson::enviar(_('Grupo no encontrado'), 'none');
        return;
    }
}
$oGrupo->setUsuarioVo(new Username($Qusuario));

if ($GrupoRepository->Guardar($oGrupo) === false) {
    $error_txt .= _("hay un error, no se ha guardado");
    $error_txt .= "\n" . $GrupoRepository->getErrorTxt();
}

ContestarJson::enviar($error_txt, 'ok');
