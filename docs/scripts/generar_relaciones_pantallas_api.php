<?php

declare(strict_types=1);

/**
 * Genera un indice cruzado entre pantallas frontend, capacidades y endpoints API.
 *
 * Uso:
 *   php docs/scripts/generar_relaciones_pantallas_api.php actividadtarifas
 *   php docs/scripts/generar_relaciones_pantallas_api.php actividadtarifas --dry-run
 *   php docs/scripts/generar_relaciones_pantallas_api.php actividadtarifas --force
 *   php docs/scripts/generar_relaciones_pantallas_api.php actividadtarifas --output=docs/catalogo
 *
 * Salida por defecto:
 *   docs/catalogo/<modulo>/relaciones/pantallas_api.md
 */

/** @return never */
function fail(string $message): void
{
    fwrite(STDERR, $message . PHP_EOL);
    exit(1);
}

function repoRoot(): string
{
    return dirname(__DIR__, 2);
}

function relativePath(string $path): string
{
    $root = repoRoot();
    if (str_starts_with($path, $root . DIRECTORY_SEPARATOR)) {
        return str_replace(DIRECTORY_SEPARATOR, '/', substr($path, strlen($root) + 1));
    }

    return str_replace(DIRECTORY_SEPARATOR, '/', $path);
}

function normalizePath(string $path): string
{
    $parts = [];
    $path = str_replace('\\', '/', $path);
    $prefix = str_starts_with($path, '/') ? '/' : '';

    foreach (explode('/', $path) as $part) {
        if ($part === '' || $part === '.') {
            continue;
        }
        if ($part === '..') {
            array_pop($parts);
            continue;
        }
        $parts[] = $part;
    }

    return $prefix . implode('/', $parts);
}

/** @return array{module: string, output: string, dry-run: bool, force: bool} */
function parseOptions(array $argv): array
{
    $options = [
        'module' => '',
        'output' => '',
        'dry-run' => false,
        'force' => false,
    ];

    foreach (array_slice($argv, 1) as $arg) {
        if ($arg === '--dry-run') {
            $options['dry-run'] = true;
            continue;
        }
        if ($arg === '--force') {
            $options['force'] = true;
            continue;
        }
        if (str_starts_with($arg, '--output=')) {
            $options['output'] = substr($arg, strlen('--output='));
            continue;
        }
        if ($arg === '-h' || $arg === '--help') {
            showHelp();
            exit(0);
        }
        if ($options['module'] === '') {
            $options['module'] = $arg;
            continue;
        }

        fail("Argumento no reconocido: {$arg}");
    }

    if ($options['module'] === '') {
        showHelp();
        exit(1);
    }

    if (!preg_match('/^[a-zA-Z0-9_]+$/', $options['module'])) {
        fail('Nombre de modulo no valido. Usa solo letras, numeros y guion bajo.');
    }

    return $options;
}

function showHelp(): void
{
    $script = relativePath(__FILE__);
    echo <<<TXT
Uso:
  php {$script} <modulo> [--output=<directorio>] [--dry-run] [--force]

Ejemplos:
  php {$script} actividadtarifas
  php {$script} actividadtarifas --dry-run
  php {$script} actividadtarifas --output=docs/catalogo

TXT;
}

/** @return array<string, string|list<string>> */
function parseSimpleYaml(string $yaml): array
{
    $data = [];
    $lines = preg_split('/\R/', $yaml) ?: [];

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        if (!preg_match('/^(?P<key>[A-Za-z0-9_-]+):\s*(?P<value>.*)$/', $line, $m)) {
            continue;
        }

        $value = trim($m['value']);
        $data[$m['key']] = str_starts_with($value, '[') && str_ends_with($value, ']')
            ? parseInlineList($value)
            : unquoteYamlString($value);
    }

    return $data;
}

/** @return list<string> */
function parseInlineList(string $value): array
{
    $inside = trim(substr($value, 1, -1));
    if ($inside === '') {
        return [];
    }

    $items = [];
    $current = '';
    $inQuote = false;
    $escape = false;

    for ($i = 0, $length = strlen($inside); $i < $length; $i++) {
        $char = $inside[$i];
        if ($escape) {
            $current .= $char;
            $escape = false;
            continue;
        }
        if ($char === '\\') {
            $current .= $char;
            $escape = true;
            continue;
        }
        if ($char === '"') {
            $current .= $char;
            $inQuote = !$inQuote;
            continue;
        }
        if ($char === ',' && !$inQuote) {
            $items[] = unquoteYamlString(trim($current));
            $current = '';
            continue;
        }
        $current .= $char;
    }

    if (trim($current) !== '') {
        $items[] = unquoteYamlString(trim($current));
    }

    return $items;
}

function unquoteYamlString(string $value): string
{
    $value = trim($value);
    if (strlen($value) >= 2 && str_starts_with($value, '"') && str_ends_with($value, '"')) {
        $value = substr($value, 1, -1);

        return str_replace(['\\"', '\\\\'], ['"', '\\'], $value);
    }

    return $value;
}

/** @param string|list<string> $value */
function asStringList(string|array $value): array
{
    return is_array($value) ? array_map('strval', $value) : [$value];
}

/** @return list<string> */
function catalogFiles(string $module, string $section): array
{
    $files = glob(repoRoot() . "/docs/catalogo/{$module}/{$section}/*.md");
    if ($files === false) {
        return [];
    }

    sort($files);

    return $files;
}

/** @return array<string, array<string, mixed>> */
function readSection(string $module, string $section, string $expectedType): array
{
    $items = [];
    foreach (catalogFiles($module, $section) as $file) {
        $contents = file_get_contents($file);
        if ($contents === false || !preg_match('/^---\R(?P<yaml>.*?)\R---/s', $contents, $m)) {
            continue;
        }
        $frontMatter = parseSimpleYaml($m['yaml']);
        if (($frontMatter['tipo'] ?? '') !== $expectedType) {
            continue;
        }
        $id = (string)($frontMatter['id'] ?? '');
        if ($id === '') {
            continue;
        }
        $frontMatter['source'] = relativePath($file);
        $items[$id] = $frontMatter;
    }

    ksort($items);

    return $items;
}

/** @param array<string, array<string, mixed>> $capabilities */
function endpointsFromCapabilities(array $capabilityIds, array $capabilities): array
{
    $endpoints = [];
    foreach ($capabilityIds as $capabilityId) {
        if (!isset($capabilities[$capabilityId])) {
            continue;
        }
        $endpoints = array_merge($endpoints, asStringList($capabilities[$capabilityId]['endpoints'] ?? []));
    }

    $endpoints = array_values(array_unique($endpoints));
    sort($endpoints);

    return $endpoints;
}

/**
 * @param array<string, array<string, mixed>> $screens
 * @return array<string, list<string>>
 */
function screenIdsByEndpoint(array $screens): array
{
    $byEndpoint = [];
    foreach ($screens as $screenId => $screen) {
        foreach (asStringList($screen['endpoints'] ?? []) as $endpoint) {
            $byEndpoint[$endpoint][] = $screenId;
        }
    }

    foreach ($byEndpoint as &$screenIds) {
        $screenIds = array_values(array_unique($screenIds));
        sort($screenIds);
    }
    unset($screenIds);
    ksort($byEndpoint);

    return $byEndpoint;
}

/**
 * @param array<string, array<string, mixed>> $screens
 * @param array<string, array<string, mixed>> $capabilities
 * @return array<string, list<string>>
 */
function screenIdsByCapabilityEndpoint(array $screens, array $capabilities): array
{
    $byEndpoint = [];
    foreach ($screens as $screenId => $screen) {
        $capabilityEndpoints = endpointsFromCapabilities(asStringList($screen['capacidades'] ?? []), $capabilities);
        foreach ($capabilityEndpoints as $endpoint) {
            $byEndpoint[$endpoint][] = $screenId;
        }
    }

    foreach ($byEndpoint as &$screenIds) {
        $screenIds = array_values(array_unique($screenIds));
        sort($screenIds);
    }
    unset($screenIds);
    ksort($byEndpoint);

    return $byEndpoint;
}

/** @param list<string> $items */
function renderList(array $items, string $emptyText): array
{
    if ($items === []) {
        return [$emptyText];
    }

    return array_map(static fn (string $item): string => '- `' . $item . '`', $items);
}

/**
 * @param array<string, array<string, mixed>> $screens
 * @param array<string, array<string, mixed>> $apis
 * @param array<string, array<string, mixed>> $capabilities
 */
function renderRelations(string $module, array $screens, array $apis, array $capabilities): string
{
    $directByEndpoint = screenIdsByEndpoint($screens);
    $capabilityByEndpoint = screenIdsByCapabilityEndpoint($screens, $capabilities);
    $allApiUrls = [];
    foreach ($apis as $api) {
        $url = (string)($api['url'] ?? '');
        if ($url !== '') {
            $allApiUrls[] = $url;
        }
    }
    $allApiUrls = array_values(array_unique($allApiUrls));
    sort($allApiUrls);

    $lines = [
        '---',
        'tipo: "relacion_pantallas_api"',
        'modulo: "' . $module . '"',
        'pantallas: ' . count($screens),
        'endpoints_api: ' . count($apis),
        'capacidades: ' . count($capabilities),
        'estado_revision: "generado"',
        '---',
        '',
        '# Relacion Pantallas API - ' . $module,
        '',
        'Indice generado automaticamente para cruzar pantallas frontend, capacidades y endpoints API.',
        '',
        '## Por Pantalla',
        '',
    ];

    foreach ($screens as $screenId => $screen) {
        $directEndpoints = asStringList($screen['endpoints'] ?? []);
        $capabilityIds = asStringList($screen['capacidades'] ?? []);
        $capabilityEndpoints = endpointsFromCapabilities($capabilityIds, $capabilities);
        $indirectEndpoints = array_values(array_diff($capabilityEndpoints, $directEndpoints));
        sort($directEndpoints);
        sort($capabilityIds);
        sort($indirectEndpoints);

        $lines[] = '### `' . $screenId . '`';
        $lines[] = '';
        $lines[] = '- Controller: `' . (string)($screen['controller'] ?? '') . '`';
        $lines[] = '- Subtipo: `' . (string)($screen['subtipo'] ?? '') . '`';
        $lines[] = '';
        $lines[] = 'Endpoints directos:';
        array_push($lines, ...renderList($directEndpoints, '- Ninguno detectado.'));
        $lines[] = '';
        $lines[] = 'Capacidades:';
        array_push($lines, ...renderList($capabilityIds, '- Ninguna detectada.'));
        $lines[] = '';
        $lines[] = 'Endpoints aportados por capacidades:';
        array_push($lines, ...renderList($indirectEndpoints, '- Ninguno adicional.'));
        $lines[] = '';
    }

    $lines[] = '## Por Endpoint API';
    $lines[] = '';

    foreach ($allApiUrls as $url) {
        $directScreens = $directByEndpoint[$url] ?? [];
        $capabilityScreens = $capabilityByEndpoint[$url] ?? [];
        $onlyViaCapability = array_values(array_diff($capabilityScreens, $directScreens));
        sort($onlyViaCapability);

        $lines[] = '### `' . $url . '`';
        $lines[] = '';
        $lines[] = 'Pantallas directas:';
        array_push($lines, ...renderList($directScreens, '- Ninguna detectada.'));
        $lines[] = '';
        $lines[] = 'Pantallas via capacidad:';
        array_push($lines, ...renderList($onlyViaCapability, '- Ninguna adicional.'));
        $lines[] = '';
    }

    $orphanDirect = [];
    $orphanAll = [];
    foreach ($allApiUrls as $url) {
        if (($directByEndpoint[$url] ?? []) === []) {
            $orphanDirect[] = $url;
        }
        if (($directByEndpoint[$url] ?? []) === [] && ($capabilityByEndpoint[$url] ?? []) === []) {
            $orphanAll[] = $url;
        }
    }

    $lines[] = '## Alertas De Revision';
    $lines[] = '';
    $lines[] = 'Endpoints sin pantalla directa detectada:';
    array_push($lines, ...renderList($orphanDirect, '- Ninguno.'));
    $lines[] = '';
    $lines[] = 'Endpoints sin pantalla directa ni capacidad relacionada:';
    array_push($lines, ...renderList($orphanAll, '- Ninguno.'));
    $lines[] = '';
    $lines[] = '## Revision Manual';
    $lines[] = '';
    $lines[] = '- Confirmar si los endpoints sin pantalla directa se usan desde fragmentos AJAX no enlazados.';
    $lines[] = '- Revisar pantallas que dependen de varias capacidades.';
    $lines[] = '- Completar relaciones si hay navegacion generada dinamicamente o desde menus.';
    $lines[] = '';

    return implode(PHP_EOL, $lines);
}

function outputPath(string $module, string $output): string
{
    if ($output === '') {
        return repoRoot() . "/docs/catalogo/{$module}/relaciones/pantallas_api.md";
    }

    $path = str_starts_with($output, '/') ? $output : repoRoot() . '/' . $output;
    $path = normalizePath($path);
    if (str_ends_with($path, '.md')) {
        return $path;
    }

    return normalizePath("{$path}/{$module}/relaciones/pantallas_api.md");
}

function ensureDirectory(string $dir): void
{
    if (is_dir($dir)) {
        return;
    }

    if (!mkdir($dir, 0775, true) && !is_dir($dir)) {
        fail("No se pudo crear el directorio: " . relativePath($dir));
    }
}

$options = parseOptions($argv);
$module = $options['module'];
$screens = readSection($module, 'pantallas', 'pantalla_frontend');
$apis = readSection($module, 'api', 'endpoint');
$capabilities = readSection($module, 'capacidades', 'capacidad');

if ($screens === []) {
    fail("No se han encontrado pantallas en docs/catalogo/{$module}/pantallas/*.md");
}
if ($apis === []) {
    fail("No se han encontrado endpoints API en docs/catalogo/{$module}/api/*.md");
}

$target = outputPath($module, $options['output']);
if (is_file($target) && !$options['force'] && !$options['dry-run']) {
    fail('Ya existe ' . relativePath($target) . '. Usa --force para sobrescribir.');
}

if ($options['dry-run']) {
    echo 'WRITE ' . relativePath($target) . PHP_EOL;
    echo 'Pantallas: ' . count($screens) . PHP_EOL;
    echo 'Endpoints API: ' . count($apis) . PHP_EOL;
    echo 'Capacidades: ' . count($capabilities) . PHP_EOL;
    exit(0);
}

ensureDirectory(dirname($target));
if (file_put_contents($target, renderRelations($module, $screens, $apis, $capabilities)) === false) {
    fail('No se pudo escribir: ' . relativePath($target));
}

echo 'WRITE ' . relativePath($target) . PHP_EOL;
echo 'Pantallas: ' . count($screens) . PHP_EOL;
echo 'Endpoints API: ' . count($apis) . PHP_EOL;
echo 'Capacidades: ' . count($capabilities) . PHP_EOL;

