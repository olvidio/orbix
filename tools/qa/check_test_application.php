<?php
/**
 * Lista las clases de `application/` y los controladores HTTP de `src/` que
 * ningún fichero de `tests/` referencia por nombre de clase (o de fichero).
 *
 * Complementa `tools/qa/check_test_granular.sh`, que solo mira entidades,
 * value objects y repositorios Pg.
 *
 * Uso (desde la raíz del repo):
 *   php tools/qa/check_test_application.php            # resumen por módulo
 *   php tools/qa/check_test_application.php --list     # + ficheros sin test
 *   php tools/qa/check_test_application.php --json     # salida JSON
 *   php tools/qa/check_test_application.php --modulo=notas --list
 *   php tools/qa/check_test_application.php --strict   # exit 1 si hay huecos
 *   php tools/qa/check_test_application.php --baseline # exit 1 solo si hay huecos NUEVOS
 *                                                       # (no están en test_application_baseline.json)
 *   php tools/qa/check_test_application.php --update-baseline  # reescribe la línea base (tras cubrir huecos)
 *
 * Heurística: una clase «tiene test» si su nombre aparece como palabra en
 * algún fichero bajo `tests/`. Es una cota inferior de cobertura, no una
 * medida: no dice si el test ejerce las ramas (para eso, cobertura de líneas).
 */

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$opts = getopt('', ['list', 'json', 'strict', 'modulo::', 'baseline', 'update-baseline']);
$baselineFile = __DIR__ . '/test_application_baseline.json';
$soloModulo = isset($opts['modulo']) && is_string($opts['modulo']) ? $opts['modulo'] : null;

/** @return list<string> */
function phpFiles(string $dir): array
{
    if (!is_dir($dir)) {
        return [];
    }
    $out = [];
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS));
    foreach ($it as $f) {
        if ($f->isFile() && $f->getExtension() === 'php') {
            $out[] = $f->getPathname();
        }
    }
    sort($out);
    return $out;
}

// Corpus de tests: un único string para búsquedas rápidas por palabra.
$corpus = '';
foreach (phpFiles($root . '/tests') as $t) {
    $corpus .= "\n" . file_get_contents($t);
}

function referenciado(string $nombre, string $corpus): bool
{
    return (bool) preg_match('/(?<![A-Za-z0-9_])' . preg_quote($nombre, '/') . '(?![A-Za-z0-9_])/', $corpus);
}

$resultado = [];
$modulos = array_filter(glob($root . '/src/*', GLOB_ONLYDIR) ?: [], static fn ($d) => $soloModulo === null || basename($d) === $soloModulo);

foreach ($modulos as $mod) {
    $nombreMod = basename($mod);
    $grupos = [
        'application' => $mod . '/application',
        'controllers' => $mod . '/infrastructure/ui/http/controllers',
    ];
    foreach ($grupos as $tipo => $dir) {
        foreach (phpFiles($dir) as $fichero) {
            $base = basename($fichero, '.php');
            // Los controladores son scripts sin clase: se busca el nombre de fichero.
            $resultado[$nombreMod][$tipo]['total'] = ($resultado[$nombreMod][$tipo]['total'] ?? 0) + 1;
            if (!referenciado($base, $corpus)) {
                $resultado[$nombreMod][$tipo]['sin_test'][] = substr($fichero, strlen($root) + 1);
            }
        }
    }
}
ksort($resultado);

if (isset($opts['json'])) {
    echo json_encode($resultado, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), "\n";
}

$totales = ['application' => [0, 0], 'controllers' => [0, 0]];
if (!isset($opts['json'])) {
    printf("%-22s %-22s %-22s\n", 'Módulo', 'application (sin/total)', 'controllers (sin/total)');
    foreach ($resultado as $mod => $info) {
        $fila = [];
        foreach (['application', 'controllers'] as $tipo) {
            $total = $info[$tipo]['total'] ?? 0;
            $sin = count($info[$tipo]['sin_test'] ?? []);
            $totales[$tipo][0] += $sin;
            $totales[$tipo][1] += $total;
            $fila[] = $total === 0 ? '-' : sprintf('%d/%d', $sin, $total);
        }
        printf("%-22s %-22s %-22s\n", $mod, $fila[0], $fila[1]);
        if (isset($opts['list'])) {
            foreach (['application', 'controllers'] as $tipo) {
                foreach ($info[$tipo]['sin_test'] ?? [] as $f) {
                    echo "    - $f\n";
                }
            }
        }
    }
    printf("\nTOTAL application: %d/%d sin test · controllers: %d/%d sin test\n", $totales['application'][0], $totales['application'][1], $totales['controllers'][0], $totales['controllers'][1]);
}

if (isset($opts['strict'])) {
    foreach ($resultado as $info) {
        foreach ($info as $g) {
            if (!empty($g['sin_test'])) {
                exit(1);
            }
        }
    }
}

if (isset($opts['update-baseline']) || isset($opts['baseline'])) {
    $sinTest = [];
    foreach ($resultado as $info) {
        foreach ($info as $g) {
            foreach ($g['sin_test'] ?? [] as $f) {
                $sinTest[] = $f;
            }
        }
    }
    sort($sinTest);
    if (isset($opts['update-baseline'])) {
        file_put_contents($baselineFile, json_encode($sinTest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");
        echo 'Línea base actualizada: ' . count($sinTest) . " ficheros sin test.\n";
        exit(0);
    }
    $baseline = is_file($baselineFile) ? (array) json_decode((string) file_get_contents($baselineFile), true) : [];
    $nuevos = array_values(array_diff($sinTest, $baseline));
    $cubiertos = array_values(array_diff($baseline, $sinTest));
    if ($cubiertos !== []) {
        echo "\nYa tienen test (se pueden quitar de la línea base con --update-baseline): " . count($cubiertos) . "\n";
    }
    if ($nuevos !== []) {
        echo "\nHUECOS NUEVOS (clases de application/ o controladores sin ningún test):\n";
        foreach ($nuevos as $f) {
            echo "    - $f\n";
        }
        exit(1);
    }
    echo "\nOK: ningún hueco nuevo respecto a la línea base.\n";
}
