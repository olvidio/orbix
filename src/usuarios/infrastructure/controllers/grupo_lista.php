<?php

use core\ConfigGlobal;
use src\usuarios\application\GruposLista;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use web\ContestarJson;

// Se usa al buscar:
$Qusername = (string)filter_input(INPUT_POST, 'username');

$error_txt = '';

$UsuarioRepository = $GLOBALS['container']->get(UsuarioRepositoryInterface::class);
$oMiUsuario = $UsuarioRepository->findById(ConfigGlobal::mi_id_usuario());
$miRole = $oMiUsuario->getId_role();

if ($miRole > 3) {
    // no es administrador
    $error_txt = _("no tiene permisos para ver esto");
}

$GruposLista = new GruposLista();
$data = $GruposLista($Qusername);

// envía una Response
ContestarJson::enviar($error_txt, $data);