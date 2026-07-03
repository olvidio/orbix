<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\usuarios\domain\contracts\GrupoRepositoryInterface;
use src\shared\web\ContestarJson;

$error_txt = '';
$id_usuario = 0;

$a_sel = (array)\src\shared\domain\helpers\FilterPostGet::post('sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if ($a_sel !== []) {
    $id_usuario = (int) strtok((string) $a_sel[0], '#');
}
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
