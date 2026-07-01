<?php
require_once __DIR__ . '/../helpers/encargossacd_support.php';

use frontend\shared\FrontBootstrap;
use frontend\shared\model\ViewNewTwig;
use frontend\shared\security\HashFront;

// INICIO Cabecera global de URL de controlador (frontend) *********************************
require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

/**
 * @param array<string, int|string|null> $params
 */
$lnk = static function (string $script, array $params = []): string {
    array_walk($params, 'src\shared\domain\helpers\poner_empty_on_null');
    $url = 'frontend/encargossacd/controller/' . $script;
    if ($params !== []) {
        $url .= '?' . http_build_query($params);
    }

    return HashFront::link($url);
};

$a_campos = [
    'oPosicion' => $oPosicion,
    'url_propuestas' => $lnk('propuestas_lista.php', ['sf' => 0]),
    'url_new_tabla' => $lnk('propuestas_ajax.php', ['que' => 'crear_tabla']),
    'url_aprobar' => $lnk('propuestas_aprobar.php', ['sf' => 0]),
    'url_lista_sacd' => $lnk('propuestas_lista_sacd.php', ['sel' => 'nagd']),
    'url_lista_enc' => $lnk('propuestas_lista_enc.php', ['sel' => 'nagd']),
];

$oView = new ViewNewTwig('frontend/encargossacd/controller');
$oView->renderizar('propuestas_menu.html.twig', $a_campos);
