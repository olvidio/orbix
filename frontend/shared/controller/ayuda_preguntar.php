<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\config\OrbixRuntime;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();

/**
 * @return array{href: string, full_url: string, parametros: string}
 */
function ayudaPreguntarEnlace(string $path, string $query = ''): array
{
    $fullUrl = AppUrlConfig::getPublicAppBaseUrl() . $path;
    return [
        'href' => HashFront::link($query === '' ? $fullUrl : $fullUrl . '?' . $query),
        'full_url' => $fullUrl,
        'parametros' => HashFront::add_hash($query, $fullUrl),
    ];
}

$urlPreguntar = AppUrlConfig::srcBrowserUrl('/src/shared/ayuda_preguntar');
$oHash = new HashFront();
$oHash->setUrl($urlPreguntar);
$oHash->setCamposForm('pregunta!limite');
$hashPreguntar = $oHash->getParamAjaxEnArray();

$a_campos = [
    'oPosicion' => $oPosicion,
    'urlPreguntar' => $urlPreguntar,
    'hashPreguntar' => $hashPreguntar,
    'manualBase' => AppUrlConfig::getPublicAppBaseUrl() . '/frontend/shared/controller/manual.php',
    'enlaceAyuda' => ayudaPreguntarEnlace('/frontend/shared/controller/ayuda_index.php'),
    'enlaceManual' => ayudaPreguntarEnlace('/frontend/shared/controller/manual.php'),
];

$oView = new ViewNewPhtml('frontend\shared\controller');
$html = $oView->renderizar('ayuda_preguntar.phtml', $a_campos, false);

$standalone = !empty($GLOBALS['ayuda_preguntar_standalone_shell']);
if ($standalone) {
    $pruebas = OrbixRuntime::isPruebasWebPath() ? 1 : 0;
    ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title><?= _('Preguntar a la documentación') ?> — Orbix</title>
    <?php include_once OrbixRuntime::dirEstilos() . '/todo_en_uno.css.php'; ?>
</head>
<body class="otro">
<?php if ($pruebas === 1) { ?>
    <p><strong><?= _('Entorno de pruebas') ?></strong></p>
<?php } ?>
<?php
}
echo $html;
if ($standalone) {
    echo '</body></html>';
}
