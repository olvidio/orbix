#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Regenera frontend/menus/view/traducir_menu.phtml con los textos de la BD que la aplicación
 * traduce en tiempo de ejecución, para que xgettext los lleve a languages/es_ES.pot:
 *
 *   - nombres de grupo de menú (`_($nombreGrupo)`): aux_grupmenu de sv-e y sf, ref_grupmenu de comun;
 *   - menús activos (`_($menu)`): aux_menus de todos los esquemas de sv-e y sf (tabla padre `global`)
 *     y las plantillas ref_menus de comun;
 *   - tipos de repetición de actividades: xa_tipo_repeticion de comun.
 *
 * Solo lee. Sustituye al antiguo endpoint /src/menus/menus_generar_txt (retirado el 2026-11-16), que solo
 * volcaba los menús del esquema de quien lo pulsaba y no incluía los grupos.
 *
 * Uso:
 *   php tools/i18n/generar_textos_menus.php [--contenedor=orbix-db-1] [--bd-prefijo=] [--comprobar]
 *
 *   --contenedor  contenedor Docker de PostgreSQL (o variable ORBIX_DB_CONTAINER; por defecto orbix-db-1)
 *   --bd-prefijo  prefijo de las BD (p. ej. «pruebas-» para los clones de pruebas); por defecto ninguno
 *   --comprobar   no escribe: sale con código 1 si el fichero no está al día
 */

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "Solo CLI.\n");
    exit(1);
}

require __DIR__ . '/textos_menus_lib.php';

$root = dirname(__DIR__, 2);
$destino = $root . '/frontend/menus/view/traducir_menu.phtml';

$opts = getopt('', ['contenedor:', 'bd-prefijo:', 'comprobar']);
$contenedor = is_string($opts['contenedor'] ?? null)
    ? $opts['contenedor']
    : (getenv('ORBIX_DB_CONTAINER') ?: 'orbix-db-1');
$prefijo = is_string($opts['bd-prefijo'] ?? null) ? $opts['bd-prefijo'] : '';
$comprobar = array_key_exists('comprobar', $opts);

/**
 * @return list<string>
 */
function i18n_consulta(string $contenedor, string $bd, string $sql): array
{
    // -0: separador de registros NUL, por si algún texto tuviera saltos de línea.
    $cmd = 'docker exec ' . escapeshellarg($contenedor)
        . ' psql -U postgres -X -A -t -0 -v ON_ERROR_STOP=1 -d ' . escapeshellarg($bd)
        . ' -c ' . escapeshellarg($sql) . ' 2>&1';
    exec($cmd, $lineas, $code);
    $salida = implode("\n", $lineas);
    if ($code !== 0) {
        fwrite(STDERR, "Error consultando $bd: $salida\n");
        exit(1);
    }
    $filas = explode("\0", $salida);

    return array_values(array_filter($filas, static fn (string $f): bool => $f !== ''));
}

$fuentes = [
    'grupos de menú' => [
        ['sv-e', 'SELECT grup_menu FROM global.aux_grupmenu'],
        ['sf', 'SELECT grup_menu FROM global.aux_grupmenu'],
        ['comun', 'SELECT grup_menu FROM public.ref_grupmenu'],
    ],
    'menús' => [
        ['sv-e', 'SELECT menu FROM global.aux_menus WHERE ok'],
        ['sf', 'SELECT menu FROM global.aux_menus WHERE ok'],
        ['comun', 'SELECT menu FROM public.ref_menus WHERE ok'],
    ],
    'tipos de repetición de actividades' => [
        ['comun', 'SELECT repeticion FROM public.xa_tipo_repeticion'],
    ],
];

$secciones = [];
foreach ($fuentes as $titulo => $consultas) {
    $secciones[$titulo] = [];
    foreach ($consultas as [$bd, $sql]) {
        foreach (i18n_consulta($contenedor, $prefijo . $bd, $sql) as $texto) {
            $secciones[$titulo][] = $texto;
        }
    }
}

$nuevo = i18n_phtml_textos($secciones);
$actual = is_file($destino) ? (string)file_get_contents($destino) : '';
$rel = substr($destino, strlen($root) + 1);
$total = substr_count($nuevo, "\n_(");

if ($nuevo === $actual) {
    echo "$rel: al día ($total textos).\n";
    exit(0);
}
if ($comprobar) {
    echo "$rel: no está al día; ejecutar php tools/i18n/generar_textos_menus.php\n";
    exit(1);
}
if (file_put_contents($destino, $nuevo) === false) {
    fwrite(STDERR, "No se pudo escribir $destino\n");
    exit(1);
}
echo "$rel: regenerado ($total textos).\n";
