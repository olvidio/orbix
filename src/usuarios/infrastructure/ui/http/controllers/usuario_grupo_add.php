<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;
use src\shared\domain\helpers\FuncTablasSupport;

use src\shared\config\ConfigGlobal;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\usuarios\domain\contracts\UsuarioGrupoRepositoryInterface;
use src\usuarios\domain\entity\UsuarioGrupo;
use src\shared\web\ContestarJson;
$sfsv = ConfigGlobal::mi_sfsv();
$error_txt = '';

$ctxRaw = (string)FilterPostGet::post('ctx');
try {
    $opened = HashB::open($ctxRaw, 'usuario_grupo_add');
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}
$Qid_usuario = FuncTablasSupport::inputInt($opened, 'id_usuario');
$Qid_grupo = FuncTablasSupport::inputInt($opened, 'id_grupo');
// añado el grupo de permisos al usuario.
$UsuarioGrupoRepository = DependencyResolver::get(UsuarioGrupoRepositoryInterface::class);
$oUsuarioGrupo = new UsuarioGrupo();
$oUsuarioGrupo->setId_usuario($Qid_usuario);
$oUsuarioGrupo->setId_grupo($Qid_grupo);
if ($UsuarioGrupoRepository->Guardar($oUsuarioGrupo) === false) {
    $error_txt .= _("hay un error, no se ha guardado");
    $error_txt .= "\n" . $UsuarioGrupoRepository->getErrorTxt();
}

ContestarJson::enviar($error_txt, 'ok');