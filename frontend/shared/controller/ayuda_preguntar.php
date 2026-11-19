<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\config\OrbixRuntime;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashF;
use frontend\shared\FrontBootstrap;
use src\shared\infrastructure\ui\http\PermisoRevisarAyudaGuard;

require_once __DIR__ . '/../FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();

/**
 * @return array{href: string, full_url: string, parametros: string}
 */
function ayudaPreguntarEnlace(string $path, string $query = ''): array
{
    $fullUrl = AppUrlConfig::getPublicAppBaseUrl() . $path;
    return [
        'href' => HashF::link($query === '' ? $fullUrl : $fullUrl . '?' . $query),
        'full_url' => $fullUrl,
        'parametros' => HashF::add_hash($query, $fullUrl),
    ];
}

$urlPreguntar = AppUrlConfig::srcBrowserUrl('/src/shared/ayuda_preguntar');
$oHash = new HashF();
$oHash->setUrl($urlPreguntar);
$oHash->setCamposForm('pregunta!limite!pantalla');
$hashPreguntar = $oHash->getParamAjaxEnArray();

// Valoración de la respuesta («¿Le ha servido?»): la cápsula `ctx_valorar` llega con cada respuesta.
$urlValorar = AppUrlConfig::srcBrowserUrl('/src/shared/ayuda_consulta_valorar');
$oHashValorar = new HashF();
$oHashValorar->setUrl($urlValorar);
$oHashValorar->setCamposForm('ctx_valorar!util!comentario');
$hashValorar = $oHashValorar->getParamAjaxEnArray();

// Pantalla desde la que se abrió la ayuda (la manda el panel del interrogante): solo la ruta del controlador.
$pantallaRaw = $_POST['pantalla'] ?? $_GET['pantalla'] ?? '';
$pantalla = is_string($pantallaRaw)
    && preg_match('#frontend/[a-z][a-z0-9_]*/controller/[A-Za-z0-9_]+\.php#', $pantallaRaw, $mPantalla) === 1
    ? $mPantalla[0]
    : '';

$a_campos = [
    'oPosicion' => $oPosicion,
    'urlPreguntar' => $urlPreguntar,
    'hashPreguntar' => $hashPreguntar,
    'urlValorar' => $urlValorar,
    'hashValorar' => $hashValorar,
    'pantalla' => $pantalla,
    'enlaceRevisar' => PermisoRevisarAyudaGuard::permite()
        ? ayudaPreguntarEnlace('/frontend/shared/controller/ayuda_consultas_lista.php')
        : null,
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
    <title><?= _("Preguntar a la documentación") ?> — Orbix</title>
    <?php include_once OrbixRuntime::dirEstilos() . '/todo_en_uno.css.php'; ?>
</head>
<body class="otro">
<?php if ($pruebas === 1) { ?>
    <p><strong><?= _("Entorno de pruebas") ?></strong></p>
<?php } ?>
<?php
}
echo $html;
if ($standalone) {
    echo '</body></html>';
}
