<?php

use core\ConfigGlobal;
use src\usuarios\application\GruposLista;
use src\usuarios\application\repositories\UsuarioRepository;
use web\ContestarJson;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos por esta url  **********************************************
// FIN de  Cabecera global de URL de controlador ********************************

// Se usa al buscar:
$Qusername = (string)filter_input(INPUT_POST, 'username');

$error_txt = '';

$UsuarioRepository = new UsuarioRepository();
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