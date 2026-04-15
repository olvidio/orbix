<?php

use frontend\shared\PostRequest;
use web\Lista;

require_once("frontend/shared/global_header_front.inc");

$Qid_zona = (string)filter_input(INPUT_POST, 'id_zona');
$data = PostRequest::getDataFromUrl('/src/zonassacd/zona_ctr_lista', ['id_zona' => $Qid_zona]);

$oTabla = new Lista();
$oTabla->setId_tabla($data['id_tabla']);
$oTabla->setCabeceras($data['a_cabeceras']);
$oTabla->setBotones($data['a_botones']);
$oTabla->setDatos($data['a_valores']);
echo $oTabla->mostrar_tabla();
