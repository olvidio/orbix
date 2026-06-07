<?php
use src\shared\infrastructure\DependencyResolver;

use src\usuarios\domain\contracts\GrupoRepositoryInterface;
use src\shared\web\ContestarJson;

use function src\shared\domain\helpers\input_int;

$Qid_usuario = input_int($_POST, 'id_usuario');

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
