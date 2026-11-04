#!/usr/bin/env php
<?php
declare(strict_types=1);

/**
 * Rellena `pantallas_principales: []` en los flujos del catálogo cuando la pantalla se deduce sin ambigüedad.
 *
 * Uso:
 *   php tools/fix/enlazar_flujos_pantallas.php            # simulación (no escribe), lista lo que haría
 *   php tools/fix/enlazar_flujos_pantallas.php --apply    # escribe los cambios
 *   php tools/fix/enlazar_flujos_pantallas.php ubis       # limitar a un módulo
 *
 * Criterios, por orden:
 *   1. existe una ficha de pantalla con el mismo nombre que el flujo;
 *   2. exactamente una pantalla comparte algún endpoint con el flujo o declara su capacidad;
 *   3. si hay varias, exactamente una que no sea fragmento (subtipo distinto de fragmento_*);
 *   4. si siguen siendo varias, exactamente una con entrada de menú (aparece en docs/guias/_referencia_menus.md).
 * Ambiguos (varias pantallas) y sin candidata se listan al final para revisión manual.
 *
 * Opción --todas: en vez de dejarlos para revisión manual,
 *   - ambiguos: enlaza TODAS las pantallas candidatas;
 *   - sin candidata: enlaza las pantallas que llaman al endpoint del flujo (búsqueda del
 *     endpoint en las carpetas controller y view de frontend; la pantalla es la ficha con el mismo
 *     nombre que el fichero que lo llama).
 * Solo toca la línea `pantallas_principales: []` del frontmatter; el resto del fichero queda igual.
 * Tras aplicar: regenerar manual/ai (docs/scripts/generar_documentacion_modulo.sh) en los módulos afectados.
 */

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "Solo CLI.\n");
    exit(1);
}

$docs = dirname(__DIR__, 2) . '/docs/catalogo';
$apply = in_array('--apply', $argv, true);
$todas = in_array('--todas', $argv, true);
$only = '';
foreach (array_slice($argv, 1) as $a) {
    if (!str_starts_with($a, '--')) {
        $only = $a;
    }
}

/** @return list<string> */
function listaFm(string $txt, string $key): array
{
    if (preg_match('/^' . preg_quote($key, '/') . ':\s*\[(.*)\]/m', $txt, $m) !== 1) {
        return [];
    }
    preg_match_all('/"([^"]+)"/', $m[1], $mm);
    return $mm[1];
}

/**
 * Pantallas (fichas del módulo) cuyo controlador o vista del frontend menciona alguno de los endpoints.
 *
 * @param list<string> $eps
 * @return list<string>
 */
function pantallasQueLlaman(string $docs, string $mod, array $eps): array
{
    $front = dirname($docs, 2) . '/frontend';
    $found = [];
    foreach ($eps as $ep) {
        $needle = ltrim($ep, '/');
        $cmd = 'grep -rlF -- ' . escapeshellarg($needle) . ' ' . escapeshellarg($front) . ' 2>/dev/null';
        foreach (explode("\n", trim((string)shell_exec($cmd))) as $file) {
            if ($file === '') {
                continue;
            }
            $b = preg_replace('/\.(html\.twig|phtml|php|twig|js)$/', '', basename($file));
            $b = ltrim((string)$b, '_');
            if (is_file("$docs/$mod/pantallas/$b.md")) {
                $found[$b] = true;
            }
        }
    }
    ksort($found);

    return array_keys($found);
}

$menus = (string)@file_get_contents($docs . '/../guias/_referencia_menus.md');
$resumen = ['nombre' => 0, 'unica' => 0, 'multiple' => 0, 'ambigua' => [], 'ninguna' => []];
$porModulo = [];

foreach (glob($docs . '/*/flujos/*.md') ?: [] as $flujo) {
    $mod = basename(dirname($flujo, 2));
    if ($only !== '' && $only !== $mod) {
        continue;
    }
    $txt = (string)file_get_contents($flujo);
    if (preg_match('/^pantallas_principales: \[\]\s*$/m', $txt) !== 1) {
        continue;
    }
    $base = basename($flujo, '.md');
    $destino = null;

    if (is_file("$docs/$mod/pantallas/$base.md")) {
        $destino = $base;
        $resumen['nombre']++;
    } else {
        $eps = listaFm($txt, 'endpoints');
        $cap = preg_match('/^capacidad:\s*"([^"]+)"/m', $txt, $m) === 1 ? $m[1] : '';
        $hits = [];
        foreach (glob("$docs/$mod/pantallas/*.md") ?: [] as $ficha) {
            $pt = (string)file_get_contents($ficha);
            if (array_intersect($eps, listaFm($pt, 'endpoints')) !== []
                || ($cap !== '' && in_array($cap, listaFm($pt, 'capacidades'), true))) {
                $hits[] = basename($ficha, '.md');
            }
        }
        if (count($hits) > 1) {
            $noFragmento = array_values(array_filter($hits, static function (string $h) use ($docs, $mod): bool {
                $t = (string)file_get_contents("$docs/$mod/pantallas/$h.md");
                return preg_match('/^subtipo:\s*"fragmento/m', $t) !== 1;
            }));
            if (count($noFragmento) === 1) {
                $hits = $noFragmento;
            }
        }
        if (count($hits) > 1) {
            $conMenu = array_values(array_filter($hits, static function (string $h) use ($menus, $mod): bool {
                return str_contains($menus, "frontend/$mod/controller/$h.php");
            }));
            if (count($conMenu) === 1) {
                $hits = $conMenu;
            }
        }
        if ($hits === [] && $todas) {
            $hits = pantallasQueLlaman($docs, $mod, $eps);
        }
        if (count($hits) === 1) {
            $destino = $hits[0];
            $resumen['unica']++;
        } elseif ($hits === []) {
            $resumen['ninguna'][] = "$mod/$base";
        } elseif ($todas) {
            $destino = $hits;
            $resumen['multiple']++;
        } else {
            $resumen['ambigua'][] = "$mod/$base → " . implode(', ', $hits);
        }
    }

    if ($destino !== null) {
        $porModulo[$mod] = ($porModulo[$mod] ?? 0) + 1;
        if ($apply) {
            $ids = array_map(static fn (string $d): string => '"' . $mod . '.pantalla.' . $d . '"', (array)$destino);
            $nuevo = preg_replace(
                '/^pantallas_principales: \[\]\s*$/m',
                'pantallas_principales: [' . implode(', ', $ids) . ']',
                $txt,
                1
            );
            file_put_contents($flujo, $nuevo);
        }
    }
}

ksort($porModulo);
echo ($apply ? "APLICADO" : "SIMULACIÓN (usar --apply para escribir)"), "\n";
printf("enlazados por nombre: %d · por endpoint/capacidad: %d · con varias pantallas: %d\n", $resumen['nombre'], $resumen['unica'], $resumen['multiple']);
foreach ($porModulo as $m => $n) {
    printf("  %-20s %d\n", $m, $n);
}
printf("\nAmbiguos (%d) — decidir a mano:\n", count($resumen['ambigua']));
foreach ($resumen['ambigua'] as $l) {
    echo "  $l\n";
}
printf("\nSin pantalla candidata (%d) — flujo sin UI propia o ficha ausente:\n", count($resumen['ninguna']));
foreach ($resumen['ninguna'] as $l) {
    echo "  $l\n";
}
