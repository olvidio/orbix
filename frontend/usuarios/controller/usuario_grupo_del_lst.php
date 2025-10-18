<?php

use core\ConfigGlobal;
use frontend\shared\PostRequest;
use web\Hash;
use web\Lista;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');

$url_backend = '/src/usuarios/infrastructure/controllers/usuario_grupo_del_lst.php';
$a_campos_backend = ['id_usuario' => $Qid_usuario];
$data = PostRequest::getDataFromUrl($url_backend, $a_campos_backend);
if (isset($data['error'])) {
    echo $data['error'];
}

$a_cabeceras = $data['a_cabeceras'];
$a_botones = $data['a_botones'];
$a_valores = $data['a_valores'];


$oTabla = new Lista();
$oTabla->setId_tabla('usuario_grupo_del_lst');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);
echo $oTabla->mostrar_tabla();