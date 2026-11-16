#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Pasada completa de gettext en un solo comando (equivalente al tools/i18n/actualizar.sh de secretary):
 *
 *   1. textos de menús de la BD → frontend/menus/view/traducir_menu.phtml (generar_textos_menus.php)
 *   2. languages/es_ES.pot desde el código (actualizar_es_pot.php)
 *   3. msgmerge sobre ca/en/de/it (languages/<locale>/LC_MESSAGES/orbix.po)
 *   4. traducción con Groq de lo nuevo: entradas fuzzy (--fuzzy) y vacías (traduir_groq.py)
 *   5. msgfmt --check → .mo
 *   6. informe de revisión de las cadenas nuevas (informe_revision.py) y cobertura (audit_po_cobertura.php)
 *
 * Uso:
 *   php tools/i18n/actualizar.php [--sin-menus] [--sin-traducir] [--revision=/ruta/informe.md]
 *                                 [--contenedor=orbix-db-1]
 *
 *   --sin-menus     no regenera los textos de menús (sin acceso a la BD)
 *   --sin-traducir  no llama a Groq: las cadenas nuevas quedan vacías o fuzzy y el script sale con 1
 *   --revision      dónde escribir el informe (por defecto en el directorio temporal del sistema)
 *
 * Requisitos: xgettext/msgmerge/msgfmt (gettext), Docker con el contenedor de PostgreSQL para el paso 1,
 * y para 4 y 6 el venv tools/i18n/.venv (requirements.txt) y GROQ_API_KEY en .env.
 * Guía: docs/dev/traducciones_gettext.md.
 *
 * Sale con 1 si queda alguna cadena nueva vacía o fuzzy, o si falla un paso.
 */

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "Solo CLI.\n");
    exit(1);
}

$root = dirname(__DIR__, 2);
chdir($root);

$opts = getopt('', ['sin-menus', 'sin-traducir', 'revision:', 'contenedor:']);
$sinMenus = array_key_exists('sin-menus', $opts);
$sinTraducir = array_key_exists('sin-traducir', $opts);
$revision = is_string($opts['revision'] ?? null)
    ? $opts['revision']
    : sys_get_temp_dir() . '/orbix-i18n-revision-' . date('Ymd-His') . '.md';
$contenedor = is_string($opts['contenedor'] ?? null) ? $opts['contenedor'] : null;

const LOCALES = [
    'ca_ES.UTF-8' => 'catalán',
    'en_US.UTF-8' => 'inglés',
    'de_DE.UTF-8' => 'alemán',
    'it_IT.UTF-8' => 'italiano',
];
$python = $root . '/tools/i18n/.venv/bin/python';
$pot = $root . '/languages/es_ES.pot';

function paso(string $titulo): void
{
    echo "\n== $titulo\n";
}

/**
 * @param list<string> $argv
 */
function ejecutar(array $argv, bool $obligatorio = true): int
{
    $cmd = implode(' ', array_map('escapeshellarg', $argv));
    passthru($cmd, $code);
    if ($code !== 0 && $obligatorio) {
        fwrite(STDERR, "Ha fallado: $cmd\n");
        exit(1);
    }

    return $code;
}

// 1. Menús
paso('1. Textos de menús de la BD');
if ($sinMenus) {
    echo "(omitido: --sin-menus)\n";
} else {
    $args = [PHP_BINARY, 'tools/i18n/generar_textos_menus.php'];
    if ($contenedor !== null) {
        $args[] = '--contenedor=' . $contenedor;
    }
    ejecutar($args);
}

// 2. Plantilla
paso('2. languages/es_ES.pot');
$potAnterior = sys_get_temp_dir() . '/orbix-es_ES.pot.' . getmypid();
copy($pot, $potAnterior);
ejecutar([PHP_BINARY, 'tools/i18n/actualizar_es_pot.php']);

// 3. msgmerge
paso('3. msgmerge');
foreach (array_keys(LOCALES) as $loc) {
    echo "$loc: ";
    ejecutar(['msgmerge', '--update', '--backup=none', "languages/$loc/LC_MESSAGES/orbix.po", 'languages/es_ES.pot']);
}

// 4. Traducción
paso('4. Traducción de lo nuevo (Groq)');
$hayPython = is_executable($python);
if ($sinTraducir) {
    echo "(omitido: --sin-traducir)\n";
} elseif (!$hayPython) {
    echo "(omitido: falta el venv tools/i18n/.venv; ver tools/README.md)\n";
} else {
    foreach (LOCALES as $loc => $nombre) {
        foreach ([['--fuzzy'], []] as $extra) {
            ejecutar(array_merge(
                [$python, 'tools/i18n/traduir_groq.py', '--idioma', $loc, '--idioma-nom', $nombre],
                $extra
            ));
        }
    }
}

// 5. Compilar
paso('5. msgfmt --check');
foreach (array_keys(LOCALES) as $loc) {
    // polib (traduir_groq.py) corta las líneas largas distinto que gettext: volver al formato de
    // msgmerge para que cada pasada no reescriba medio fichero.
    $po = "languages/$loc/LC_MESSAGES/orbix.po";
    ejecutar(['msgcat', '--output-file=' . $po, $po]);
    echo "$loc: ";
    ejecutar(['msgfmt', '--check', '--statistics', '-o', "languages/$loc/LC_MESSAGES/orbix.mo", "languages/$loc/LC_MESSAGES/orbix.po"]);
}

// 6. Revisión
paso('6. Revisión');
$rc = 0;
if ($hayPython) {
    $rc = ejecutar([$python, 'tools/i18n/informe_revision.py', '--pot-anterior', $potAnterior, '--salida', $revision], false);
} else {
    echo "(sin informe: falta el venv tools/i18n/.venv)\n";
}
@unlink($potAnterior);
ejecutar([PHP_BINARY, 'tools/audit/audit_po_cobertura.php'], false);

if ($rc !== 0) {
    fwrite(STDERR, "\nQuedan cadenas nuevas vacías o fuzzy: no entran en el .mo (se ven en español).\n");
    exit(1);
}
echo "\nHecho. Revisar las traducciones nuevas en: $revision\n";
