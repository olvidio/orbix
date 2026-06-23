<?php
use src\shared\infrastructure\DependencyResolver;

use src\shared\config\ConfigGlobal;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\usuarios\domain\contracts\UsuarioGrupoRepositoryInterface;
use src\usuarios\domain\entity\UsuarioGrupo;
use src\shared\web\ContestarJson;
use function src\shared\domain\helpers\input_int;

$sfsv = ConfigGlobal::mi_sfsv();
$error_txt = '';

$ctxRaw = (string)filter_post('ctx');
try {
    $opened = HashB::open($ctxRaw, 'usuario_grupo_add');
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}
$Qid_usuario = input_int($opened, 'id_usuario');
$Qid_grupo = input_int($opened, 'id_grupo');
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