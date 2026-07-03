<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\usuarios\domain\contracts\GrupoRepositoryInterface;
use src\usuarios\domain\entity\Grupo;
use src\usuarios\domain\value_objects\Username;
use src\shared\web\ContestarJson;

$Qusuario = (string)FilterPostGet::post('usuario');

$error_txt = '';
if (empty($Qusuario)) {
    $error_txt .= _("debe poner un nombre");
}
$Qid_usuario = (integer)FilterPostGet::post('id_usuario');

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