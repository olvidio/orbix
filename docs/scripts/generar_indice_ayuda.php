#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Genera el índice pantalla → ayuda que usará la ayuda contextual de la interfaz.
 *
 * Uso (desde la raíz del repo):
 *   php docs/scripts/generar_indice_ayuda.php            # escribe docs/manual/_indice_pantallas.json
 *   php docs/scripts/generar_indice_ayuda.php --check    # no escribe; exit 1 si el JSON está desactualizado
 *   php docs/scripts/generar_indice_ayuda.php --strict   # además exit 1 si hay pantallas de menú sin ayuda
 *   php docs/scripts/generar_indice_ayuda.php --lista    # lista las pantallas de menú sin ayuda
 *
 * Entradas (solo lectura):
 *   - docs/manual/<modulo>.md          secciones `##` = tareas de usuario (texto «Para que sirve»)
 *   - docs/catalogo/<mod>/pantallas/   id, nombre, controller y fragmentos_frontend de cada pantalla
 *   - docs/catalogo/<mod>/flujos/      id, pantallas_principales y fragmentos de cada flujo
 *   - docs/guias/_referencia_menus.md  qué controllers tienen entrada de menú (para --strict / --lista)
 *
 * Una sección del manual se asocia a un controller si:
 *   a) su texto cita la ruta `frontend/<mod>/controller/<nombre>.php`, o
 *   b) cita «Flujo: `id`» y ese flujo lista la pantalla en pantallas_principales o fragmentos.
 * Además, un fragmento (`fragmentos_frontend` de una ficha) hereda las secciones de su pantalla padre.
 *
 * Los `ancla` son los ids que `frontend\shared\helpers\ManualAnclas` inyecta en el HTML del manual:
 * se calculan con la misma función sobre el mismo HTML, así que enlazan siempre.
 */

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "Solo CLI.\n");
    exit(1);
}

$root = dirname(__DIR__, 2);
$docs = $root . '/docs';
$salida = $docs . '/manual/_indice_pantallas.json';

require_once $root . '/frontend/shared/helpers/ManualAnclas.php';
if (!class_exists('Parsedown')) {
    require_once $root . '/libs/vendor/erusev/parsedown/Parsedown.php';
}

use frontend\shared\helpers\ManualAnclas;

$args = array_slice($argv, 1);
$check = in_array('--check', $args, true);
$strict = in_array('--strict', $args, true);
$lista = in_array('--lista', $args, true);

const MAX_SIRVE = 300;
const RUTA_CONTROLLER = '#frontend/([a-z_0-9]+)/controller/([A-Za-z0-9_]+)\.php#';

function leer(string $path): string
{
    return is_file($path) ? (string)file_get_contents($path) : '';
}

/** @return list<string> */
function listaComillas(string $texto, string $clave): array
{
    if (preg_match('#^' . preg_quote($clave, '#') . ':\s*\[(.*)\]\s*$#m', $texto, $m) !== 1) {
        return [];
    }
    preg_match_all('#"([^"]+)"#', $m[1], $items);
    return $items[1];
}

function valorComillas(string $texto, string $clave): string
{
    return preg_match('#^' . preg_quote($clave, '#') . ':\s*"([^"]*)"#m', $texto, $m) === 1 ? $m[1] : '';
}

function recortar(string $texto): string
{
    $texto = trim((string)preg_replace('/\s+/u', ' ', $texto));
    if (mb_strlen($texto) <= MAX_SIRVE) {
        return $texto;
    }
    $corte = mb_substr($texto, 0, MAX_SIRVE);
    $espacio = mb_strrpos($corte, ' ');
    return rtrim($espacio !== false ? mb_substr($corte, 0, $espacio) : $corte, " ,;:.") . '…';
}

// ---- Catálogo: fichas de pantalla ----
/** @var array<string, array{controller: string, nombre: string, fragmentos: list<string>}> $fichas por id */
$fichas = [];
/** @var array<string, array{nombre: string, fragmentos: list<string>}> $fichaPorController */
$fichaPorController = [];
foreach (glob($docs . '/catalogo/*/pantallas/*.md') ?: [] as $f) {
    $txt = leer($f);
    $id = valorComillas($txt, 'id');
    $controller = valorComillas($txt, 'controller');
    // Fichas sin pantalla propia (p. ej. «(sin controller propio)»): no son controllers.
    if ($id === '' || preg_match(RUTA_CONTROLLER, $controller) !== 1) {
        continue;
    }
    $fragmentos = [];
    foreach (listaComillas($txt, 'fragmentos_frontend') as $frag) {
        $fragmentos[] = $frag;
    }
    $fichas[$id] = ['controller' => $controller, 'nombre' => valorComillas($txt, 'nombre'), 'fragmentos' => $fragmentos];
    $fichaPorController[$controller] = ['nombre' => valorComillas($txt, 'nombre'), 'fragmentos' => $fragmentos];
}

// ---- Catálogo: flujos → controllers ----
/** @var array<string, list<string>> $controllersDeFlujo id de flujo → controllers */
$controllersDeFlujo = [];
foreach (glob($docs . '/catalogo/*/flujos/*.md') ?: [] as $f) {
    $txt = leer($f);
    $id = valorComillas($txt, 'id');
    if ($id === '') {
        continue;
    }
    foreach (array_merge(listaComillas($txt, 'pantallas_principales'), listaComillas($txt, 'fragmentos')) as $pid) {
        if (isset($fichas[$pid])) {
            $controllersDeFlujo[$id][] = $fichas[$pid]['controller'];
        }
    }
}

// ---- Manual: secciones por módulo ----
$parsedown = new Parsedown();
$parsedown->setSafeMode(true);
$parsedown->setMarkupEscaped(true);

/** @var array<string, list<array{modulo: string, ancla: string, titulo: string, sirve: string}>> $seccionesPorController */
$seccionesPorController = [];
$errores = [];
$totalSecciones = 0;

$manuales = glob($docs . '/manual/*.md') ?: [];
sort($manuales);
foreach ($manuales as $path) {
    $slug = basename($path, '.md');
    if (preg_match('/^[a-z][a-z0-9_]*$/', $slug) !== 1) {
        continue;
    }
    $md = leer($path);
    $md = (string)preg_replace('/\A---\r?\n.*?\r?\n---\r?\n/s', '', $md, 1);

    // ids de los `##` según el HTML real, en orden.
    $h = ManualAnclas::procesar($parsedown->text($md));
    $idsH2 = [];
    foreach ($h['encabezados'] as $e) {
        if ($e['nivel'] === 2) {
            $idsH2[] = ['id' => $e['id'], 'texto' => $e['texto']];
        }
    }

    // Secciones `##` del markdown (ignorando bloques de código).
    $secciones = [];
    $actual = null;
    $enCodigo = false;
    foreach (preg_split('/\r?\n/', $md) ?: [] as $linea) {
        if (preg_match('/^\s*(```|~~~)/', $linea) === 1) {
            $enCodigo = !$enCodigo;
        }
        if (!$enCodigo && preg_match('/^## (.+)$/', $linea) === 1) {
            if ($actual !== null) {
                $secciones[] = $actual;
            }
            $actual = ['cuerpo' => ''];
            continue;
        }
        if ($actual !== null) {
            $actual['cuerpo'] .= $linea . "\n";
        }
    }
    if ($actual !== null) {
        $secciones[] = $actual;
    }

    if (count($secciones) !== count($idsH2)) {
        $errores[] = sprintf('%s.md: %d secciones `##` en markdown pero %d `<h2>` en el HTML; revisar bloques de código.', $slug, count($secciones), count($idsH2));
        continue;
    }

    foreach ($secciones as $i => $sec) {
        $totalSecciones++;
        $cuerpo = $sec['cuerpo'];

        $sirve = '';
        if (preg_match('/^### Para Que Sirve\s*\n(.*?)(?=^#{1,6} |\z)/ms', $cuerpo, $m) === 1) {
            $sirve = recortar($m[1]);
            if (preg_match('/no se han inferido pasos|Revisar manualmente los pasos|pendiente de documentar/iu', $sirve) === 1) {
                $sirve = '';
            }
        }

        $controllers = [];
        $directas = [];
        if (preg_match_all(RUTA_CONTROLLER, $cuerpo, $rutas, PREG_SET_ORDER) > 0) {
            foreach ($rutas as $r) {
                $controllers[$r[0]] = true;
                $directas[$r[0]] = true;
            }
        }
        if (preg_match_all('#Flujo:\s*`([^`]+)`#', $cuerpo, $flujos) > 0) {
            foreach ($flujos[1] as $fid) {
                foreach ($controllersDeFlujo[$fid] ?? [] as $c) {
                    $controllers[$c] = true;
                }
            }
        }
        if ($controllers === []) {
            continue;
        }

        $entrada = ['modulo' => $slug, 'ancla' => $idsH2[$i]['id'], 'titulo' => $idsH2[$i]['texto'], 'sirve' => $sirve];
        foreach (array_keys($controllers) as $c) {
            if (!is_file($root . '/' . $c)) {
                $errores[] = sprintf('%s.md «%s» cita %s, que no existe.', $slug, $idsH2[$i]['texto'], $c);
                continue;
            }
            $seccionesPorController[$c][] = $entrada + ['directa' => isset($directas[$c])];
        }
    }
}

// ---- Herencia de fragmentos ----
foreach ($fichaPorController as $padre => $info) {
    foreach ($info['fragmentos'] as $frag) {
        if ($frag === $padre || !isset($seccionesPorController[$padre]) || preg_match(RUTA_CONTROLLER, $frag) !== 1 || !is_file($root . '/' . $frag)) {
            continue;
        }
        foreach ($seccionesPorController[$padre] as $s) {
            $seccionesPorController[$frag][] = ['directa' => false] + $s;
        }
    }
}

// ---- Ensamblado ----
$pantallas = [];
foreach ($seccionesPorController as $controller => $secs) {
    preg_match(RUTA_CONTROLLER, $controller, $mc);
    $modulo = $mc[1];

    // Únicas por (modulo, ancla); las del propio módulo primero.
    // Una sección que cita la pantalla directamente prevalece sobre la que llega solo por flujo o herencia.
    $unicas = [];
    foreach ($secs as $s) {
        $k = $s['modulo'] . '#' . $s['ancla'];
        if (!isset($unicas[$k]) || ($s['directa'] && !$unicas[$k]['directa'])) {
            $unicas[$k] = $s;
        }
    }
    $unicas = array_values($unicas);
    // Directas primero, luego las del propio módulo; usort es estable (PHP ≥ 8.0), se conserva el orden del manual.
    usort($unicas, static fn(array $a, array $b): int => [(int)$b['directa'], (int)($b['modulo'] === $modulo)]
        <=> [(int)$a['directa'], (int)($a['modulo'] === $modulo)]);
    $unicas = array_map(static fn(array $s): array => [
        'modulo' => $s['modulo'], 'ancla' => $s['ancla'], 'titulo' => $s['titulo'], 'sirve' => $s['sirve'],
    ], $unicas);

    $titulo = $fichaPorController[$controller]['nombre'] ?? '';
    if ($titulo === '') {
        foreach ($fichaPorController as $padre => $info) {
            if (in_array($controller, $info['fragmentos'], true) && $info['nombre'] !== '') {
                $titulo = $info['nombre'];
                break;
            }
        }
    }
    if ($titulo === '') {
        $titulo = $unicas[0]['titulo'];
    }

    $pantallas[$controller] = ['titulo' => $titulo, 'modulo' => $modulo, 'manual' => $unicas];
}
ksort($pantallas);

$json = json_encode(
    ['version' => 1, 'pantallas' => $pantallas],
    JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR,
) . "\n";

// ---- Menús sin ayuda ----
$menuControllers = [];
foreach (explode("\n", leer($docs . '/guias/_referencia_menus.md')) as $line) {
    if (str_starts_with($line, '| ') && preg_match('#`(frontend/[a-z_0-9]+/controller/[A-Za-z0-9_]+\.php)`#', $line, $m) === 1) {
        $menuControllers[$m[1]] = true;
    }
}
$menuSinAyuda = array_values(array_filter(
    array_keys($menuControllers),
    static fn(string $c): bool => is_file($GLOBALS['root'] . '/' . $c) && !isset($pantallas[$c]),
));
sort($menuSinAyuda);

// ---- Informe ----
$problemas = $errores;
foreach ($errores as $e) {
    fwrite(STDERR, "ERROR: $e\n");
}
printf(
    "Pantallas con ayuda: %d | secciones del manual: %d | pantallas de menú: %d (sin ayuda: %d)\n",
    count($pantallas),
    $totalSecciones,
    count($menuControllers),
    count($menuSinAyuda),
);
if ($lista || $strict) {
    foreach ($menuSinAyuda as $c) {
        echo "  sin ayuda: $c\n";
    }
}

if ($check) {
    if (leer($salida) !== $json) {
        fwrite(STDERR, "DESACTUALIZADO: docs/manual/_indice_pantallas.json no coincide; ejecuta php docs/scripts/generar_indice_ayuda.php\n");
        exit(1);
    }
    echo "Índice actualizado.\n";
} else {
    file_put_contents($salida, $json);
    echo 'Escrito ' . substr($salida, strlen($root) + 1) . "\n";
}

exit(($errores !== [] || ($strict && $menuSinAyuda !== [])) ? 1 : 0);
