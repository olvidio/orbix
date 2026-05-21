<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;
use src\shared\config\ConfigGlobal;
require_once __DIR__ . '/../global_header_front.inc';

/**
 * @return array{href: string, full_url: string, parametros: string}
 */
function ayudaEnlace(string $path): array
{
    $fullUrl = AppUrlConfig::getPublicAppBaseUrl() . $path;
    return [
        'href' => HashFront::link($fullUrl),
        'full_url' => $fullUrl,
        'parametros' => HashFront::add_hash('', $fullUrl),
    ];
}

$pruebas = (ConfigGlobal::$web_path === '/pruebas' || ConfigGlobal::$web_path === '/pruebassf') ? 1 : 0;
$webPublic = ConfigGlobal::getWeb_public();

$enlaceManual = ayudaEnlace('/frontend/shared/controller/manual.php');
$enlaceTraducciones = ayudaEnlace('/public/ayuda/traducciones.php');

$urlContactos = HashFront::cmdSinParametros(
    ConfigGlobal::getWeb() . 'frontend/usuarios/controller/mails_contactos_region.php'
);
$oHashRegion = new HashFront();
$oHashRegion->setUrl($urlContactos);
$oHashRegion->setCamposForm('region');
$hashParamsRegiones = $oHashRegion->getParamAjaxEnArray();

$a_campos = [
    'oPosicion' => $oPosicion,
    'pruebas' => $pruebas,
    'webPublic' => $webPublic,
    'enlaceManual' => $enlaceManual,
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
    <?php include_once ConfigGlobal::$dir_estilos . '/todo_en_uno.css.php'; ?>
</head>
<body class="otro">
<?php
}
echo $html;
if ($standalone) {
    echo '</body></html>';
}
