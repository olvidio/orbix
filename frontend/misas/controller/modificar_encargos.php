<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
// Si el use case devuelve error (p.ej. permiso denegado), PostRequest hace
// `exit()` con el mensaje antes de devolver el control aqui.
$data = PostRequest::getDataFromUrl('/src/misas/modificar_encargos_data');

$a_opciones_zona = $data['a_opciones_zona'] ?? [];
$a_orden = $data['a_orden'] ?? [
    'orden' => _('orden'),
    'prioridad' => _('prioridad'),
    'desc_enc' => _('alfabético'),
];

$oDesplZonas = new Desplegable();
$oDesplZonas->setOpciones($a_opciones_zona);
$oDesplZonas->setBlanco(false);
$oDesplZonas->setNombre('id_zona');
$oDesplZonas->setAction('fnjs_ver_encargos_zona()');

$oDesplOrden = new Desplegable();
$oDesplOrden->setOpciones($a_orden);
$oDesplOrden->setNombre('orden_select');
$oDesplOrden->setAction('fnjs_ver_encargos_zona()');

$url_ver_encargos_zona = 'frontend/misas/controller/ver_encargos_zona.php';
$oHashZona = new HashFront();
$oHashZona->setUrl($url_ver_encargos_zona);
$oHashZona->setCamposForm('id_zona!orden');
$h_zona = $oHashZona->linkSinValParams();

$a_campos = [
    'oDesplZonas' => $oDesplZonas,
    'oDesplOrden' => $oDesplOrden,
    'url_ver_encargos_zona' => $url_ver_encargos_zona,
    'h_zona' => $h_zona,
];

$oView = new ViewNewPhtml('frontend\\misas\\controller');
$oView->renderizar('modificar_encargos.phtml', $a_campos);
