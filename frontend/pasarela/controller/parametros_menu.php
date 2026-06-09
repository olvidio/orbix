<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewTwig;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
$web = AppUrlConfig::getPublicAppBaseUrl();

$url = $web . '/frontend/pasarela/controller/activacion_lista.php';
$aQuery = ['que' => 'fecha_activacion'];
array_walk($aQuery, 'src\shared\domain\helpers\poner_empty_on_null');
$url_activacion = HashFront::link($url . '?' . http_build_query($aQuery));

$url = $web . '/frontend/pasarela/controller/nombre_lista.php';
$aQuery = ['que' => 'nombre'];
array_walk($aQuery, 'src\shared\domain\helpers\poner_empty_on_null');
$url_nombre = HashFront::link($url . '?' . http_build_query($aQuery));

$url = $web . '/frontend/pasarela/controller/contribucion_no_duerme_lista.php';
$aQuery = ['que' => 'contribucion_no_duerme'];
array_walk($aQuery, 'src\shared\domain\helpers\poner_empty_on_null');
$url_contribucion_no_duerme = HashFront::link($url . '?' . http_build_query($aQuery));

$url = $web . '/frontend/pasarela/controller/contribucion_reserva_lista.php';
$aQuery = ['que' => 'contribucion_reserva'];
array_walk($aQuery, 'src\shared\domain\helpers\poner_empty_on_null');
$url_contribucion_reserva = HashFront::link($url . '?' . http_build_query($aQuery));

$a_campos = [
    'oPosicion' => $oPosicion,
    'url_activacion' => $url_activacion,
    'url_nombre' => $url_nombre,
    'url_contribucion_no_duerme' => $url_contribucion_no_duerme,
    'url_contribucion_reserva' => $url_contribucion_reserva,
];

$oView = new ViewNewTwig('frontend\\pasarela\\controller');
$oView->renderizar('parametros_menu.html.twig', $a_campos);
