<?php

use frontend\shared\FrontBootstrap;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;

require_once __DIR__ . '/../helpers/encargossacd_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();

$Qsel = encargossacd_post_string('sel');

$datos = PostRequest::getDataFromUrl('/src/encargossacd/propuestas_lista_sacd_data', ['sel' => $Qsel]);

$a_campos = [
    'oPosicion' => $oPosicion,
    'array_modo' => is_array($datos['array_modo'] ?? null) ? $datos['array_modo'] : [],
    'Qsel' => $Qsel,
];

$oView = new ViewNewPhtml('frontend\\encargossacd\\controller');
$oView->renderizar('propuestas_lista_sacd.phtml', $a_campos);
