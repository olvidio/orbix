<?php
use src\shared\domain\criteria\Filter;
use src\shared\domain\criteria\Criteria;
use src\usuarios\domain\criteria\UsuarioGrupoField;
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\shared\config\ConfigGlobal;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\usuarios\domain\contracts\UsuarioGrupoRepositoryInterface;
use src\shared\web\ContestarJson;

// Administración de usuarios, grupos y permisos: solo los roles de administración.
if (!\src\usuarios\infrastructure\ui\http\PermisoAdministrarUsuariosGuard::permite()) {
    \src\shared\web\ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}
$sfsv = ConfigGlobal::mi_sfsv();
$error_txt = '';

$ctxRaw = (string)\src\shared\domain\helpers\FilterPostGet::post('ctx');
try {
    $opened = HashB::open($ctxRaw, 'usuario_grupo_del');
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}
$Qid_usuario = \src\shared\domain\helpers\FuncTablasSupport::inputInt($opened, 'id_usuario');
$Qid_grupo = \src\shared\domain\helpers\FuncTablasSupport::inputInt($opened, 'id_grupo');

// elimino el grupo de permisos al usuario.
$UsuarioGrupoRepository = DependencyResolver::get(UsuarioGrupoRepositoryInterface::class);
$cUsuarioGrupo = $UsuarioGrupoRepository->matching(Criteria::create()->where(Filter::eq(UsuarioGrupoField::IdUsuario, $Qid_usuario))->andWhere(Filter::eq(UsuarioGrupoField::IdGrupo, $Qid_grupo)));
if (!empty($cUsuarioGrupo)) {
    $oUsuarioGrupo = $cUsuarioGrupo[0];
    if ($UsuarioGrupoRepository->Eliminar($oUsuarioGrupo) === false) {
        $error_txt .= _("hay un error, no se ha eliminado");
        $error_txt .= "\n" . $UsuarioGrupoRepository->getErrorTxt();
    }
}

ContestarJson::enviar($error_txt, 'ok');