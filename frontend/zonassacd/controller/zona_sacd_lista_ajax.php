<?php

use frontend\shared\PostRequest;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/ajax_json_support.php';
require_once 'frontend/zonassacd/helpers/zonassacd_support.php';

FrontBootstrap::boot();
$Qid_zona = (string)filter_input(INPUT_POST, 'id_zona');
$lista = zonassacd_lista_from_payload(PostRequest::getDataFromUrl('/src/zonassacd/zona_sacd_lista', ['id_zona' => $Qid_zona]));

$oTabla = new Lista();
$oTabla->setId_tabla($lista['id_tabla']);
$oTabla->setCabeceras($lista['a_cabeceras']);
$oTabla->setBotones($lista['a_botones']);
$oTabla->setConSel($lista['con_sel']);
$oTabla->setDatos($lista['a_valores']);
ajax_json_html($oTabla->mostrar_tabla());
