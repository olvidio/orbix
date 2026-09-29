<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\usuarios\domain\contracts\GrupoRepositoryInterface;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

$error_txt = '';

$a_sel = (array)\src\shared\domain\helpers\FilterPostGet::post('sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if ($a_sel === [] || !is_string($a_sel[0])) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

try {
    $ctx = HashB::open($a_sel[0], 'grupo_eliminar');
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$id_usuario = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_usuario');
$Gruporepository = DependencyResolver::get(GrupoRepositoryInterface::class);
$oGrupo = $Gruporepository->findById($id_usuario);
if ($oGrupo === null) {
    ContestarJson::enviar(_('Grupo no encontrado'), 'ok');
    return;
}
if ($Gruporepository->Eliminar($oGrupo) === false) {
    $error_txt .= _("hay un error, no se ha eliminado");
    $error_txt .= "\n" . $Gruporepository->getErrorTxt();
}

ContestarJson::enviar($error_txt, 'ok');
