<?php
use src\shared\infrastructure\DependencyResolver;

use Illuminate\Http\JsonResponse;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\usuarios\domain\PasswordHasher;
use src\usuarios\domain\value_objects\Username;

$Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');
$Qusuario = (string)filter_input(INPUT_POST, 'usuario');
$Qpassword = (string)filter_input(INPUT_POST, 'password');

$usuario = null;
if (!empty($Qusuario)) { // si es nuevo no tiene id
    $usuario = new Username($Qusuario);
} elseif (!empty($Qid_usuario)) {
    $UsuarioReposiroty = DependencyResolver::get(UsuarioRepositoryInterface::class);
    $oUsuario = $UsuarioReposiroty->findById($Qid_usuario);
    if ($oUsuario !== null) {
        $usuario = $oUsuario->getUsuarioVo();
    }
}

if (!empty($Qpassword) && $usuario !== null) {
    $oCrypt = new PasswordHasher();
    $jsondata = $oCrypt->is_valid_password($usuario, $Qpassword);
    (new JsonResponse($jsondata))->send();
    exit();
}