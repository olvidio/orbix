<?php

use Illuminate\Http\JsonResponse;
use permisos\model\MyCrypt;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\usuarios\domain\value_objects\Username;

$Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');
$Qusuario = (string)filter_input(INPUT_POST, 'usuario');
$Qpassword = (string)filter_input(INPUT_POST, 'password');

if (!empty($Qusuario)) { // si es nuevo no tiene id
    $usuario = new Username($Qusuario);
} elseif (!empty($Qid_usuario)) {
    $UsuarioReposiroty = $GLOBALS['container']->get(UsuarioRepositoryInterface::class);
    $oUsuario = $UsuarioReposiroty->findById($Qid_usuario);
    $usuario = $oUsuario->getUsuario();
}

if (!empty($Qpassword)) {
    $oCrypt = new MyCrypt();
    $jsondata = $oCrypt->is_valid_password($usuario, $Qpassword);
    (new JsonResponse($jsondata))->send();
    exit();
}