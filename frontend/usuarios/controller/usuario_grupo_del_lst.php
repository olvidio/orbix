<?php

use frontend\shared\PostRequest;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/usuarios_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/ajax_json_support.php';

FrontBootstrap::boot();

$Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');

$data = usuarios_post_data(PostRequest::getDataFromUrl('/src/usuarios/usuario_grupo_del_lst', ['id_usuario' => $Qid_usuario]));
$lista = usuarios_lista_from_payload($data);

$oTabla = new Lista();
$oTabla->setId_tabla('usuario_grupo_del_lst');
$oTabla->setCabeceras($lista['cabeceras']);
$oTabla->setBotones($lista['botones']);
$oTabla->setDatos($lista['valores']);
ajax_json_html($oTabla->mostrar_tabla());
