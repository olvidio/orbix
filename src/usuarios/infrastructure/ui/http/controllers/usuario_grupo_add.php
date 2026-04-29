<?php

use src\shared\config\ConfigGlobal;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\usuarios\domain\contracts\UsuarioGrupoRepositoryInterface;
use src\usuarios\domain\entity\UsuarioGrupo;
use frontend\shared\web\ContestarJson;

$sfsv = ConfigGlobal::mi_sfsv();
$error_txt = '';

$ctxRaw = (string)filter_input(INPUT_POST, 'ctx');
try {
    $opened = HashB::open($ctxRaw, 'usuario_grupo_add');
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}
$Qid_usuario = (int)($opened['id_usuario'] ?? 0);
$Qid_grupo = (int)($opened['id_grupo'] ?? 0);
// añado el grupo de permisos al usuario.
$UsuarioGrupoRepository = $GLOBALS['container']->get(UsuarioGrupoRepositoryInterface::class);
$oUsuarioGrupo = new UsuarioGrupo();
$oUsuarioGrupo->setId_usuario($Qid_usuario);
$oUsuarioGrupo->setId_grupo($Qid_grupo);
if ($UsuarioGrupoRepository->Guardar($oUsuarioGrupo) === false) {
    $error_txt .= _("hay un error, no se ha guardado");
    $error_txt .= "\n" . $UsuarioGrupoRepository->getErrorTxt();
}

ContestarJson::enviar($error_txt, 'ok');