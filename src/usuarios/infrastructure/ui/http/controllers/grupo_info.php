<?php
use src\shared\infrastructure\DependencyResolver;

use src\usuarios\domain\contracts\GrupoRepositoryInterface;
use src\shared\security\HashB;
use src\shared\web\ContestarJson;

$Qid_usuario = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_usuario');

$nombre = '';
$que_user = 'nuevo';
if ($Qid_usuario > 0) {
    $GrupoRepository = DependencyResolver::get(GrupoRepositoryInterface::class);
    $oGrupo = $GrupoRepository->findById($Qid_usuario);
    if ($oGrupo === null) {
        ContestarJson::enviar(_('Grupo no encontrado'), []);
        return;
    }
    $nombre = $oGrupo->getUsuarioAsString();
    $que_user = 'guardar';
}

$data = [];
$data['nombre'] = $nombre;
$data['ctx_guardar'] = HashB::sign('grupo_guardar', [
    'que_user' => $que_user,
    'id_usuario' => $que_user === 'guardar' ? $Qid_usuario : 0,
]);

ContestarJson::enviar('', $data);
