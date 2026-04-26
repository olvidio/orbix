<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Lista;

require_once 'frontend/shared/global_header_front.inc';

$oPosicion->recordar();

$aGoBack = ['mod' => ''];
$oPosicion->setParametros($aGoBack, 1);

$campos = array_merge($_GET, $_POST);
$data = PostRequest::getDataFromUrl('/src/configuracion/modulos_select_data', $campos);
$payload = is_array($data) ? $data : [];

$a_cabeceras = (array)($payload['a_cabeceras'] ?? []);
$a_botones = (array)($payload['a_botones'] ?? []);
$a_valores = (array)($payload['a_valores'] ?? []);

$oTabla = new Lista();
$oTabla->setId_tabla('modulos_select');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);

$a_campos = [
    'oPosicion' => $oPosicion,
    'hash_lista_html' => (string)($payload['hash_lista_html'] ?? ''),
    'oTabla' => $oTabla,
    'txt_eliminar' => (string)($payload['txt_eliminar'] ?? ''),
    'txt_anadir_modulo' => (string)($payload['txt_anadir_modulo'] ?? ''),
];

$oView = new ViewNewPhtml('frontend\\configuracion\\controller');
$oView->renderizar('modulos_select.phtml', $a_campos);
