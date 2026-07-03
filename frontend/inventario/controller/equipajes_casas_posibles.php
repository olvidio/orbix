<?php

use frontend\shared\helpers\AjaxJsonSupport;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\FrontBootstrap;
use frontend\inventario\helpers\InventarioPayload;

require_once 'frontend/shared/FrontBootstrap.php';
FrontBootstrap::boot();

$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qyear = (int)filter_input(INPUT_POST, 'year');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');
$Qinicio = (string)filter_input(INPUT_POST, 'inicio');
$Qfin = (string)filter_input(INPUT_POST, 'fin');

$url_backend = '/src/inventario/lista_casas_posibles_periodo';
$a_campos_backend = [
    'periodo' => $Qperiodo,
    'year' => $Qyear,
    'empiezamin' => $Qempiezamin,
    'empiezamax' => $Qempiezamax,
    'incio' => $Qinicio,
    'fin' => $Qfin,
];
$data = PostRequest::getDataFromUrl($url_backend, $a_campos_backend);
$payload = InventarioPayload::postPayload($data);
$a_opciones = InventarioPayload::desplegableOpciones($payload['a_opciones'] ?? []);

$oDesplUbis = new Desplegable();
$oDesplUbis->setNombre('id_cdc');
$oDesplUbis->setOpciones($a_opciones);
$oDesplUbis->setBlanco(true);
$oDesplUbis->setAction('fnjs_ver_actividades_casa()');
AjaxJsonSupport::html($oDesplUbis->desplegable());
