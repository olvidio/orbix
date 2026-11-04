#!/usr/bin/env php
<?php
declare(strict_types=1);

/**
 * Auditoría pantalla → ayuda: ¿cada pantalla de frontend/ tiene documentación?
 *
 * Uso:
 *   php tools/audit/doc_cobertura_ayuda.php                 # resumen por módulo
 *   php tools/audit/doc_cobertura_ayuda.php notas           # detalle de un módulo
 *   php tools/audit/doc_cobertura_ayuda.php --menu          # solo pantallas con entrada de menú
 *   php tools/audit/doc_cobertura_ayuda.php --json          # salida JSON (informe)
 *   php tools/audit/doc_cobertura_ayuda.php --strict        # exit 1 si hay pantallas de menú sin ayuda
 *
 * Solo lectura. Por cada controller `frontend/<mod>/controller/*.php` comprueba:
 *   - ficha   : docs/catalogo/<mod>/pantallas/<nombre>.md (o es fragmento de una ficha)
 *   - manual  : docs/manual/<mod>.md cita su ruta (o la de la pantalla de la que es fragmento)
 *   - guia    : alguna guía de docs/guias/ cita su ruta, o la etiqueta de su entrada de menú en negrita (heurístico)
 *   - ai      : docs/ai/<mod>/pantallas/<nombre>.md
 *   - relleno : (columna) secciones del manual sin contenido real; mide calidad, no cobertura
 *   - menu    : aparece en docs/guias/_referencia_menus.md (índice URL → ruta de menú)
 *
 * «Sin ayuda» = ni manual ni guía. Prioridad: pantallas con menú (las que el usuario alcanza directamente).
 * Excepciones aceptadas: docs/excepciones_modulos.md no se lee; usar $EXCLUIR_MODULOS / $EXCLUIR_CONTROLLERS.
 */

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "Solo CLI.\n");
    exit(1);
}

$root = dirname(__DIR__, 2);
$docs = $root . '/docs';

// Módulos internos de desarrollo sin documentación de usuario.
$EXCLUIR_MODULOS = ['devel_codegen'];
// Controllers de infraestructura (navegación, proxies) que no son pantallas de usuario.
$EXCLUIR_CONTROLLERS = [
    'shared/nav_atras', 'shared/nav_state', 'shared/src_ajax',
];

$args = array_slice($argv, 1);
$soloMenu = in_array('--menu', $args, true);
$json = in_array('--json', $args, true);
$strict = in_array('--strict', $args, true);
$only = '';
foreach ($args as $a) {
    if (!str_starts_with($a, '--')) {
        $only = $a;
    }
}

function leer(string $path): string
{
    return is_file($path) ? (string)file_get_contents($path) : '';
}

/** @return list<string> */
function listaControllers(string $dir): array
{
    $out = [];
    foreach (glob($dir . '/*.php') ?: [] as $f) {
        $out[] = basename($f, '.php');
    }
    sort($out);
    return $out;
}

// ---- Índice de menús: controller path → [etiquetas hoja] ----
$menuPorController = [];
$refMenus = leer($docs . '/guias/_referencia_menus.md');
foreach (explode("\n", $refMenus) as $line) {
    if (!str_starts_with($line, '| ')) {
        continue;
    }
    $cols = array_map('trim', explode('|', trim($line, " |\r")));
    if (count($cols) < 6 || !preg_match('#`(frontend/[a-z_]+/controller/[A-Za-z0-9_]+\.php)`#', $cols[1], $m)) {
        continue;
    }
    foreach ([$cols[4], $cols[5]] as $rutas) {
        foreach (preg_split('#<br>#', $rutas) ?: [] as $ruta) {
            $partes = array_map('trim', explode('>', $ruta));
            $hoja = end($partes);
            if ($hoja !== '' && $hoja !== false) {
                $menuPorController[$m[1]][$hoja] = true;
            }
        }
    }
}

// ---- Texto de todas las guías (salvo referencia generada y planes) ----
$textoGuias = '';
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($docs . '/guias', FilesystemIterator::SKIP_DOTS));
foreach ($it as $f) {
    $n = $f->getFilename();
    if ($f->getExtension() !== 'md' || in_array($n, ['_referencia_menus.md', 'PLAN_GUIAS_PROCESOS.md', '_plantilla_guia.md'], true)) {
        continue;
    }
    $textoGuias .= "\n" . leer($f->getPathname());
}

$modulos = [];
foreach (glob($root . '/frontend/*/controller', GLOB_ONLYDIR) ?: [] as $dir) {
    $modulos[] = basename(dirname($dir));
}
sort($modulos);

$informe = [];
$relleno = [];
foreach ($modulos as $mod) {
    if (in_array($mod, $EXCLUIR_MODULOS, true) || ($only !== '' && $only !== $mod)) {
        continue;
    }
    $manual = leer("$docs/manual/$mod.md");
    // Relleno del generador: secciones sin contenido real (calidad, no cobertura).
    $relleno[$mod] = preg_match_all('/no se han inferido pasos|Revisar manualmente los pasos|pendiente de documentar/iu', $manual);
    $controllers = listaControllers("$root/frontend/$mod/controller");

    // Fragmentos: controller → pantalla padre (según fichas).
    $padre = [];
    foreach (glob("$docs/catalogo/$mod/pantallas/*.md") ?: [] as $ficha) {
        $txt = leer($ficha);
        if (preg_match('#^controller:\s*"[^"]*/([A-Za-z0-9_]+)\.php"#m', $txt, $m)
            && preg_match('#^fragmentos_frontend:\s*\[(.*)\]#m', $txt, $f)) {
            preg_match_all('#/([A-Za-z0-9_]+)\.php#', $f[1], $fm);
            foreach ($fm[1] as $frag) {
                $padre[$frag] = $m[1];
            }
        }
    }

    // Pantallas cubiertas vía flujo: el manual cita «Flujo: `id`» y el flujo las lista como principales.
    $porFlujo = [];
    foreach (glob("$docs/catalogo/$mod/flujos/*.md") ?: [] as $flujo) {
        $txt = leer($flujo);
        if (preg_match('#^id:\s*"([^"]+)"#m', $txt, $mi) && str_contains($manual, $mi[1])
            && preg_match('#^pantallas_principales:\s*\[(.*)\]#m', $txt, $pp)) {
            preg_match_all('#\.pantalla\.([A-Za-z0-9_]+)#', $pp[1], $pm);
            foreach ($pm[1] as $nombre) {
                $porFlujo[$nombre] = true;
            }
        }
    }

    foreach ($controllers as $c) {
        if (in_array("$mod/$c", $EXCLUIR_CONTROLLERS, true)) {
            continue;
        }
        $ruta = "frontend/$mod/controller/$c.php";
        $menu = isset($menuPorController[$ruta]);
        if ($soloMenu && !$menu) {
            continue;
        }
        $ficha = is_file("$docs/catalogo/$mod/pantallas/$c.md");
        $esFragmento = isset($padre[$c]);
        $origen = $esFragmento ? "frontend/$mod/controller/{$padre[$c]}.php" : $ruta;

        $enManual = str_contains($manual, $ruta) || isset($porFlujo[$c]) || ($esFragmento && str_contains($manual, $origen));
        $enGuia = str_contains($textoGuias, $ruta);
        if (!$enGuia && $menu) {
            foreach (array_keys($menuPorController[$ruta]) as $hoja) {
                if (mb_strlen($hoja) >= 4 && str_contains($textoGuias, '**' . $hoja . '**')) {
                    $enGuia = true;
                    break;
                }
            }
        }
        $informe[$mod][] = [
            'controller' => $c,
            'menu' => $menu,
            'ficha' => $ficha || $esFragmento,
            'manual' => $enManual,
            'guia' => $enGuia,
            'ai' => is_file("$docs/ai/$mod/pantallas/$c.md") || ($esFragmento && is_file("$docs/ai/$mod/pantallas/{$padre[$c]}.md")),
            'sin_ayuda' => !$enManual && !$enGuia,
        ];
    }
}

if ($json) {
    echo json_encode($informe, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), "\n";
}

$totMenuSin = 0;
if (!$json) {
    if ($only === '') {
        printf("%-20s %5s %5s %6s %6s %5s %10s %12s %9s\n", 'módulo', 'ctrl', 'menú', 'ficha', 'manual', 'guía', 'sin ayuda', 'menú s/ayuda', 'relleno');
    }
    $tot = array_fill_keys(['n', 'menu', 'ficha', 'manual', 'guia', 'sin', 'msin'], 0);
    foreach ($informe as $mod => $filas) {
        $n = count($filas);
        $menu = count(array_filter($filas, fn($f) => $f['menu']));
        $ficha = count(array_filter($filas, fn($f) => $f['ficha']));
        $manual = count(array_filter($filas, fn($f) => $f['manual']));
        $guia = count(array_filter($filas, fn($f) => $f['guia']));
        $sin = count(array_filter($filas, fn($f) => $f['sin_ayuda']));
        $msin = count(array_filter($filas, fn($f) => $f['sin_ayuda'] && $f['menu']));
        $totMenuSin += $msin;
        foreach (['n' => $n, 'menu' => $menu, 'ficha' => $ficha, 'manual' => $manual, 'guia' => $guia, 'sin' => $sin, 'msin' => $msin] as $k => $v) {
            $tot[$k] += $v;
        }
        if ($only === '') {
            printf("%-20s %5d %5d %6d %6d %5d %10d %12d %9d\n", $mod, $n, $menu, $ficha, $manual, $guia, $sin, $msin, $relleno[$mod]);
        } else {
            foreach ($filas as $f) {
                $marca = ($f['sin_ayuda'] ? 'SIN AYUDA' : 'ok       ');
                printf(
                    "  %s %s  ficha=%s manual=%s guia=%s ai=%s  %s\n",
                    $marca,
                    $f['menu'] ? '[menú]' : '      ',
                    $f['ficha'] ? 'Y' : '-',
                    $f['manual'] ? 'Y' : '-',
                    $f['guia'] ? 'Y' : '-',
                    $f['ai'] ? 'Y' : '-',
                    $f['controller']
                );
            }
        }
    }
    if ($only === '') {
        printf("%-20s %5d %5d %6d %6d %5d %10d %12d %9d\n", 'TOTAL', $tot['n'], $tot['menu'], $tot['ficha'], $tot['manual'], $tot['guia'], $tot['sin'], $tot['msin'], array_sum($relleno));
        echo "\nrelleno = secciones del manual con texto de relleno del generador (sin pasos reales).\n";
        echo "\nDetalle de un módulo: php tools/audit/doc_cobertura_ayuda.php <modulo>\n";
    }
} else {
    foreach ($informe as $filas) {
        foreach ($filas as $f) {
            $totMenuSin += ($f['sin_ayuda'] && $f['menu']) ? 1 : 0;
        }
    }
}

exit($strict && $totMenuSin > 0 ? 1 : 0);
