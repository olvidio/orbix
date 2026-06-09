<?php

use frontend\shared\PostRequest;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$Qid_zona = (string)filter_input(INPUT_POST, 'id_zona');
$data = PostRequest::getDataFromUrl('/src/zonassacd/zona_ctr_lista', ['id_zona' => $Qid_zona]);

$oTabla = new Lista();
$oTabla->setId_tabla($data['id_tabla']);
$oTabla->setCabeceras($data['a_cabeceras']);
$oTabla->setBotones($data['a_botones']);
$oTabla->setConSel(!empty($data['con_sel']));
$oTabla->setDatos($data['a_valores']);
echo $oTabla->mostrar_tabla();
