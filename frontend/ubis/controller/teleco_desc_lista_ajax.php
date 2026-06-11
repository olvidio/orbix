<?php

use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/ubis_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/ajax_json_support.php';

FrontBootstrap::boot();
$Qid_tipo_teleco = (int)filter_input(INPUT_POST, 'id_tipo_teleco');
$data = ubis_teleco_from_payload(ubis_post_data(PostRequest::getDataFromUrl('/src/ubis/teleco_desc_lista', ['id_tipo_teleco' => $Qid_tipo_teleco])));

$oDesplegableDescTeleco = new Desplegable();
$oDesplegableDescTeleco->setOpciones($data['a_desc']);
$oDesplegableDescTeleco->setNombre('id_desc_teleco');
$oDesplegableDescTeleco->setBlanco(true);

ajax_json_html($oDesplegableDescTeleco->desplegable());
