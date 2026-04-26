<?php

use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;

require_once("frontend/shared/global_header_front.inc");

$Qid_tipo_teleco = (int)filter_input(INPUT_POST, 'id_tipo_teleco');
$data = PostRequest::getDataFromUrl('/src/ubis/teleco_desc_lista', ['id_tipo_teleco' => $Qid_tipo_teleco]);

$oDesplegableDescTeleco = new Desplegable();
$oDesplegableDescTeleco->setOpciones($data['a_desc']);
$oDesplegableDescTeleco->setNombre('id_desc_teleco');
$oDesplegableDescTeleco->setBlanco(true);

echo $oDesplegableDescTeleco->desplegable();
