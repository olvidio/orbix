<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FuncTablasSupport;

use src\usuarios\domain\contracts\GrupoRepositoryInterface;
use src\shared\web\ContestarJson;

$Qid_usuario = FuncTablasSupport::inputInt($_POST, 'id_usuario');

$GrupoRepository = DependencyResolver::get(GrupoRepositoryInterface::class);
$oGrupo = $GrupoRepository->findById($Qid_usuario);
if ($oGrupo === null) {
    ContestarJson::enviar(_('Grupo no encontrado'), []);
    return;
}
$nombre = $oGrupo->getUsuarioAsString();

$error_txt = '';
$data['nombre'] = $nombre;

ContestarJson::enviar($error_txt, $data);
