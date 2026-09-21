<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\config\OrbixRuntime;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashF;
use frontend\shared\FrontBootstrap;
require_once __DIR__ . '/../FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
/**
 * @return array{href: string, full_url: string, parametros: string}
 */
function ayudaEnlace(string $path): array
{
    $fullUrl = AppUrlConfig::getPublicAppBaseUrl() . $path;
    return [
        'href' => HashF::link($fullUrl),
        'full_url' => $fullUrl,
        'parametros' => HashF::add_hash('', $fullUrl),
    ];
}

$pruebas = OrbixRuntime::isPruebasWebPath() ? 1 : 0;
$webPublic = OrbixRuntime::getWebPublic();

$enlaceManual = ayudaEnlace('/frontend/shared/controller/manual.php');
$enlacePreguntar = ayudaEnlace('/frontend/shared/controller/ayuda_preguntar.php');
$enlaceTraducciones = ayudaEnlace('/public/ayuda/traducciones.php');

$urlContactos = HashF::cmdSinParametros(
    OrbixRuntime::getWeb() . 'frontend/usuarios/controller/mails_contactos_region.php'
);
$oHashRegion = new HashF();
$oHashRegion->setUrl($urlContactos);
$oHashRegion->setCamposForm('region');
$hashParamsRegiones = $oHashRegion->getParamAjaxEnArray();

$a_campos = [
    'oPosicion' => $oPosicion,
    'pruebas' => $pruebas,
    'webPublic' => $webPublic,
    'enlaceManual' => $enlaceManual,
    'enlacePreguntar' => $enlacePreguntar,
    'enlaceTraducciones' => $enlaceTraducciones,
    'hashParamsRegiones' => $hashParamsRegiones,
];

$oView = new ViewNewPhtml('frontend\shared\controller');
$html = $oView->renderizar('ayuda_index.phtml', $a_campos, false);

$standalone = !empty($GLOBALS['ayuda_standalone_shell']);
if ($standalone) {
    ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title><?= _('Ayuda') ?> — Orbix</title>
    <?php include_once OrbixRuntime::dirEstilos() . '/todo_en_uno.css.php'; ?>
</head>
<body class="otro">
<?php
}
echo $html;
if ($standalone) {
    echo '</body></html>';
}
