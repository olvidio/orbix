<?php

use frontend\shared\helpers\AjaxJsonSupport;
use frontend\ubis\helpers\UbisPayload;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$Qid_tipo_teleco = (int)filter_input(INPUT_POST, 'id_tipo_teleco');
$data = UbisPayload::telecoFromPayload(UbisPayload::postData(PostRequest::getDataFromUrl('/src/ubis/teleco_desc_lista', ['id_tipo_teleco' => $Qid_tipo_teleco])));

$oDesplegableDescTeleco = new Desplegable();
$oDesplegableDescTeleco->setOpciones($data['a_desc']);
$oDesplegableDescTeleco->setNombre('id_desc_teleco');
$oDesplegableDescTeleco->setBlanco(true);

AjaxJsonSupport::html($oDesplegableDescTeleco->desplegable());
