<?php
/**
 * Agrega uno o varios informes clover de PHPUnit por módulo de `src/` y capa
 * (domain / application / infrastructure) y emite una tabla Markdown.
 *
 * Uso (desde la raíz del repo):
 *   php tools/qa/cobertura_por_modulo.php build/artifacts/clover-unit.xml [build/artifacts/clover-integration.xml ...] > docs/dev/reports/cobertura_por_modulo.md
 *
 * Con varios ficheros, la cobertura de cada línea es la unión (cubierta si
 * lo está en cualquiera). Generar los clover con:
 *   docker exec -w /var/www/orbix orbix-php-fpm-1 env XDEBUG_MODE=coverage php -d memory_limit=-1 \
 *     libs/vendor/bin/phpunit -c build/phpunit-no-stop.xml --testsuite unit --coverage-clover build/artifacts/clover-unit.xml
 */

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$ficheros = array_slice($argv, 1);
if ($ficheros === []) {
    fwrite(STDERR, "Uso: php tools/qa/cobertura_por_modulo.php <clover.xml>...\n");
    exit(2);
}

/** @var array<string, array<int, bool>> $lineas  ruta relativa => [nº línea => cubierta] */
$lineas = [];
foreach ($ficheros as $f) {
    $xml = simplexml_load_file($f);
    if ($xml === false) {
        fwrite(STDERR, "No se puede leer $f\n");
        exit(2);
    }
    foreach ($xml->xpath('//file') as $file) {
        $ruta = (string) $file['name'];
        $pos = strpos($ruta, '/src/');
        if ($pos === false) {
            continue;
        }
        $rel = substr($ruta, $pos + 1);
        foreach ($file->line as $l) {
            if ((string) $l['type'] !== 'stmt') {
                continue;
            }
            $n = (int) $l['num'];
            $lineas[$rel][$n] = ($lineas[$rel][$n] ?? false) || ((int) $l['count'] > 0);
        }
    }
}

function capa(string $rel): string
{
    return match (true) {
        str_contains($rel, '/domain/') => 'domain',
        str_contains($rel, '/application/') => 'application',
        str_contains($rel, '/infrastructure/') => 'infrastructure',
        default => 'otros',
    };
}

$capas = ['domain', 'application', 'infrastructure', 'otros'];
$agg = [];
foreach ($lineas as $rel => $ls) {
    $mod = explode('/', $rel)[1] ?? '?';
    $c = capa($rel);
    $agg[$mod][$c]['t'] = ($agg[$mod][$c]['t'] ?? 0) + count($ls);
    $agg[$mod][$c]['c'] = ($agg[$mod][$c]['c'] ?? 0) + count(array_filter($ls));
}
ksort($agg);

$pct = static fn (array $x): string => ($x['t'] ?? 0) === 0 ? '—' : sprintf('%.0f%%', 100 * $x['c'] / $x['t']);

echo "# Cobertura de líneas por módulo y capa\n\n";
echo '> Generado con `php tools/qa/cobertura_por_modulo.php ' . implode(' ', $ficheros) . "`\n";
echo '> Fecha: ' . date('Y-m-d') . "\n> Solo líneas ejecutables; `—` = capa sin líneas.\n\n";
echo "| Módulo | domain | application | infrastructure | total | líneas |\n|---|---:|---:|---:|---:|---:|\n";
$gt = ['t' => 0, 'c' => 0];
foreach ($agg as $mod => $info) {
    $t = array_sum(array_column($info, 't'));
    $c = array_sum(array_column($info, 'c'));
    $gt['t'] += $t;
    $gt['c'] += $c;
    printf("| %s | %s | %s | %s | %s | %d |\n", $mod, $pct($info['domain'] ?? []), $pct($info['application'] ?? []), $pct($info['infrastructure'] ?? []), $pct(['t' => $t, 'c' => $c]), $t);
}
printf("| **TOTAL** | | | | **%s** | %d |\n", $pct($gt), $gt['t']);
