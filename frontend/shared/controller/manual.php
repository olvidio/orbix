<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\config\OrbixRuntime;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;

require_once __DIR__ . '/../global_header_front.inc';

$manualDir = realpath(OrbixRuntime::dir() . '/docs/manual');
if ($manualDir === false) {
    http_response_code(500);
    die('No se encuentra la carpeta de documentación del manual.');
}

/**
 * @return list<array{slug: string, titulo: string}>
 */
function manualListarModulos(string $dir): array
{
    $modulos = [];
    foreach (glob($dir . '/*.md') ?: [] as $path) {
        $slug = basename($path, '.md');
        if (!preg_match('/^[a-z][a-z0-9_]*$/', $slug)) {
            continue;
        }
        $contenido = (string)file_get_contents($path);
        $titulo = manualExtraerTitulo($contenido) ?? manualTituloDesdeSlug($slug);
        $modulos[] = ['slug' => $slug, 'titulo' => $titulo];
    }
    usort($modulos, static fn(array $a, array $b): int => strcasecmp($a['titulo'], $b['titulo']));
    return $modulos;
}

function manualTituloDesdeSlug(string $slug): string
{
    return ucwords(str_replace('_', ' ', $slug));
}

function manualQuitarFrontmatter(string $markdown): string
{
    if (preg_match('/\A---\r?\n.*?\r?\n---\r?\n/s', $markdown) === 1) {
        return (string)preg_replace('/\A---\r?\n.*?\r?\n---\r?\n/s', '', $markdown, 1);
    }
    return $markdown;
}

function manualExtraerTitulo(string $markdown): ?string
{
    $cuerpo = manualQuitarFrontmatter($markdown);
    if (preg_match('/^#\s+(.+)$/m', $cuerpo, $matches) === 1) {
        return trim($matches[1]);
    }
    return null;
}

/**
 * @return array{slug: string, titulo: string, html: string}|null
 */
function manualCargarModulo(string $dir, string $slug): ?array
{
    if (!preg_match('/^[a-z][a-z0-9_]*$/', $slug)) {
        return null;
    }
    $path = $dir . '/' . $slug . '.md';
    $real = realpath($path);
    if ($real === false || !is_file($real) || !str_starts_with($real, $dir . DIRECTORY_SEPARATOR)) {
        return null;
    }
    $raw = (string)file_get_contents($real);
    $markdown = manualQuitarFrontmatter($raw);
    $parsedown = new Parsedown();
    $parsedown->setSafeMode(true);
    $parsedown->setMarkupEscaped(true);
    $html = $parsedown->text($markdown);
    $titulo = manualExtraerTitulo($raw) ?? manualTituloDesdeSlug($slug);
    return ['slug' => $slug, 'titulo' => $titulo, 'html' => $html];
}

/**
 * @return array{href: string, full_url: string, parametros: string}
 */
function manualEnlaceModulo(string $manualBase, string $slug): array
{
    $query = 'modulo=' . rawurlencode($slug);
    $fullUrl = $manualBase;
    return [
        'href' => HashFront::link($manualBase . '?' . $query),
        'full_url' => $fullUrl,
        'parametros' => HashFront::add_hash($query, $fullUrl),
    ];
}

$modulo = '';
if (isset($_POST['modulo']) && is_string($_POST['modulo'])) {
    $modulo = strtolower(trim($_POST['modulo']));
} elseif (isset($_GET['modulo']) && is_string($_GET['modulo'])) {
    $modulo = strtolower(trim($_GET['modulo']));
}

$modulos = manualListarModulos($manualDir);
$documento = $modulo !== '' ? manualCargarModulo($manualDir, $modulo) : null;

$manualBase = AppUrlConfig::getPublicAppBaseUrl() . '/frontend/shared/controller/manual.php';
$ayudaIndexUrl = AppUrlConfig::getPublicAppBaseUrl() . '/frontend/shared/controller/ayuda_index.php';

$enlacesModulos = [];
foreach ($modulos as $item) {
    $enlacesModulos[$item['slug']] = manualEnlaceModulo($manualBase, $item['slug']);
}

$enlaceIndice = [
    'href' => HashFront::link($manualBase),
    'full_url' => $manualBase,
    'parametros' => HashFront::add_hash('', $manualBase),
];
$enlaceAyuda = [
    'href' => HashFront::link($ayudaIndexUrl),
    'full_url' => $ayudaIndexUrl,
    'parametros' => HashFront::add_hash('', $ayudaIndexUrl),
];

$a_campos = [
    'oPosicion' => $oPosicion,
    'modulo' => $modulo,
    'modulos' => $modulos,
    'documento' => $documento,
    'manualBase' => $manualBase,
    'enlacesModulos' => $enlacesModulos,
    'enlaceIndice' => $enlaceIndice,
    'enlaceAyuda' => $enlaceAyuda,
];

$oView = new ViewNewPhtml('frontend\shared\controller');
$html = $oView->renderizar('manual.phtml', $a_campos, false);

$standalone = !empty($GLOBALS['manual_standalone_shell']);
if ($standalone) {
    $pruebas = OrbixRuntime::isPruebasWebPath() ? 1 : 0;
    ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title><?= $documento !== null ? htmlspecialchars($documento['titulo'], ENT_QUOTES, 'UTF-8') : 'Manual de usuario' ?> — Ayuda Orbix</title>
    <?php include_once OrbixRuntime::dirEstilos() . '/todo_en_uno.css.php'; ?>
</head>
<body class="otro">
<?php if ($pruebas === 1) { ?>
    <p><strong>Entorno de pruebas</strong></p>
<?php } ?>
<?php
}
echo $html;
if ($standalone) {
    echo '</body></html>';
}
