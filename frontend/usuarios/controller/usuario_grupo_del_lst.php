<?php

use frontend\shared\helpers\AjaxJsonSupport;
use frontend\usuarios\helpers\UsuariosPayload;
use frontend\shared\PostRequest;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();

$Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');

$data = UsuariosPayload::postData(PostRequest::getDataFromUrl('/src/usuarios/usuario_grupo_del_lst', ['id_usuario' => $Qid_usuario]));
$lista = UsuariosPayload::listaFromPayload($data);

$oTabla = new Lista();
$oTabla->setId_tabla('usuario_grupo_del_lst');
$oTabla->setCabeceras($lista['cabeceras']);
$oTabla->setBotones($lista['botones']);
$oTabla->setDatos($lista['valores']);
AjaxJsonSupport::html($oTabla->mostrar_tabla());
