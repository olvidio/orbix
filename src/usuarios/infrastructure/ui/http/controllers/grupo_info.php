<?php

use src\usuarios\domain\contracts\GrupoRepositoryInterface;
use web\ContestarJson;

$Qid_usuario = (string)filter_input(INPUT_POST, 'id_usuario');

$GrupoRepository = $GLOBALS['container']->get(GrupoRepositoryInterface::class);
$oGrupo = $GrupoRepository->findById($Qid_usuario);
$nombre = $oGrupo->getUsuarioAsString();

$error_txt = '';
$data['nombre'] = $nombre;

ContestarJson::enviar($error_txt, $data);