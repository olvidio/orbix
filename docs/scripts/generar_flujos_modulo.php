<?php

declare(strict_types=1);

/**
 * Genera propuestas de flujos frontend desde capacidades y pantallas del catalogo.
 *
 * Uso:
 *   php docs/scripts/generar_flujos_modulo.php actividadtarifas
 *   php docs/scripts/generar_flujos_modulo.php actividadtarifas --dry-run
 *   php docs/scripts/generar_flujos_modulo.php actividadtarifas --force
 *   php docs/scripts/generar_flujos_modulo.php actividadtarifas --output=docs/catalogo
 *
 * Salida por defecto:
 *   docs/catalogo/<modulo>/flujos/*.md
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

/** @param array<string, array<string, mixed>> $screens */
function screensForCapability(string $capabilityId, array $screens): array
{
    $selected = [];
    foreach ($screens as $screenId => $screen) {
        if (in_array($capabilityId, asStringList($screen['capacidades'] ?? []), true)) {
            $selected[$screenId] = $screen;
        }
    }

    return $selected;
}

/** @param array<string, array<string, mixed>> $screens */
function screenIdsBySubtype(array $screens, string $subtype): array
{
    $ids = [];
    foreach ($screens as $screenId => $screen) {
        if (($screen['subtipo'] ?? '') === $subtype) {
            $ids[] = $screenId;
        }
    }
    sort($ids);

    return $ids;
}

/** @param array<string, array<string, mixed>> $screens */
function collectScreenValues(array $screens, string $field): array
{
    $values = [];
    foreach ($screens as $screen) {
        $values = array_merge($values, asStringList($screen[$field] ?? []));
    }
    $values = array_values(array_unique($values));
    sort($values);

    return $values;
}

/** @param list<string> $values */
function yamlInlineList(array $values): string
{
    if ($values === []) {
        return '[]';
    }

    return '[' . implode(', ', array_map('yamlString', $values)) . ']';
}

function yamlString(string $value): string
{
    return '"' . str_replace(['\\', '"'], ['\\\\', '\"'], $value) . '"';
}

/** @param list<string> $items */
function markdownList(array $items, string $emptyText): array
{
    if ($items === []) {
        return [$emptyText];
    }

    return array_map(static fn (string $item): string => '- `' . $item . '`', $items);
}

/** @param list<string> $endpoints */
function endpointsForAction(array $endpoints, string $action): array
{
    $suffixes = match ($action) {
        'listar' => ['_lista_data', '_lista'],
        'ver_formulario' => ['_form_data', '_form'],
        'crear_actualizar' => ['_update', '_guardar', '_crear', '_nuevo', '_nueva'],
        'eliminar' => ['_eliminar', '_delete', '_borrar'],
        'copiar' => ['_copiar'],
        'actualizar_incremento' => ['_update_inc'],
        default => [],
    };

    if ($suffixes === []) {
        return [];
    }

    $matches = [];
    foreach ($endpoints as $endpoint) {
        foreach ($suffixes as $suffix) {
            if (str_ends_with($endpoint, $suffix)) {
                $matches[] = $endpoint;
                break;
            }
        }
    }

    $matches = array_values(array_unique($matches));
    sort($matches);

    return $matches;
}

/** @return list<string> */
function scenarioSteps(string $action): array
{
    return match ($action) {
        'listar' => [
            'Abrir la pantalla principal del flujo.',
            'Rellenar los filtros visibles si los hay.',
            'Ejecutar la accion de busqueda/listado.',
            'Revisar el listado mostrado en pantalla.',
        ],
        'ver_formulario' => [
            'Desde el listado, elegir crear un nuevo registro o modificar uno existente.',
            'Abrir el formulario asociado.',
            'Comprobar que los campos cargados corresponden al registro o contexto seleccionado.',
        ],
        'crear_actualizar' => [
            'Abrir el formulario de alta o modificacion.',
            'Rellenar o corregir los campos requeridos.',
            'Guardar los cambios.',
            'Comprobar que la pantalla vuelve al listado y refleja el cambio.',
        ],
        'eliminar' => [
            'Seleccionar o abrir el registro que se quiere eliminar.',
            'Pulsar la accion de eliminar.',
            'Confirmar la operacion si aparece dialogo de confirmacion.',
            'Comprobar que el registro desaparece del listado.',
        ],
        'copiar' => [
            'Abrir el listado en el contexto origen/destino correspondiente.',
            'Pulsar la accion de copiar.',
            'Confirmar la operacion si aparece dialogo de confirmacion.',
            'Comprobar que los datos copiados aparecen en el listado.',
        ],
        'actualizar_incremento' => [
            'Abrir la pantalla o proceso que permite actualizacion en lote.',
            'Revisar el conjunto de registros afectados.',
            'Ejecutar la actualizacion.',
            'Comprobar importes o valores recalculados.',
        ],
        default => [
            'Revisar manualmente los pasos de esta accion.',
        ],
    };
}

function actionTitle(string $action): string
{
    return ucwords(str_replace('_', ' ', $action));
}

/**
 * @param array<string, mixed> $capability
 * @param array<string, array<string, mixed>> $relatedScreens
 */
function renderFlow(string $module, array $capability, array $relatedScreens): string
{
    $capabilityId = (string)$capability['id'];
    $name = (string)($capability['nombre'] ?? $capabilityId);
    $actions = asStringList($capability['acciones'] ?? []);
    $endpoints = asStringList($capability['endpoints'] ?? []);
    $mainScreens = screenIdsBySubtype($relatedScreens, 'pantalla');
    $fragments = screenIdsBySubtype($relatedScreens, 'fragmento_ajax');
    $fields = collectScreenValues($relatedScreens, 'campos');
    $jsActions = collectScreenValues($relatedScreens, 'acciones');
    sort($actions);
    sort($endpoints);

    $lines = [
        '---',
        'id: ' . yamlString($capabilityId . '.flujo'),
        'tipo: "flujo_frontend"',
        'modulo: ' . yamlString($module),
        'nombre: ' . yamlString('Flujo - ' . $name),
        'capacidad: ' . yamlString($capabilityId),
        'pantallas_principales: ' . yamlInlineList($mainScreens),
        'fragmentos: ' . yamlInlineList($fragments),
        'acciones: ' . yamlInlineList($actions),
        'endpoints: ' . yamlInlineList($endpoints),
        'estado_revision: "generado"',
        '---',
        '',
        '# Flujo - ' . $name,
        '',
        'Propuesta generada automaticamente desde la capacidad `' . $capabilityId . '` y sus pantallas relacionadas.',
        '',
        '## Objetivo De Usuario',
        '',
        'Pendiente de revisar. Redactar aqui el objetivo en lenguaje de usuario, no tecnico.',
        '',
        '## Punto De Entrada',
        '',
        ...markdownList($mainScreens, 'No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.'),
        '',
        '## Fragmentos O Pantallas Auxiliares',
        '',
        ...markdownList($fragments, 'No se han detectado fragmentos AJAX relacionados.'),
        '',
        '## Escenarios Inferidos',
        '',
    ];

    foreach ($actions as $action) {
        $actionEndpoints = endpointsForAction($endpoints, $action);
        $lines[] = '### ' . actionTitle($action);
        $lines[] = '';
        $lines[] = 'Pasos propuestos:';
        foreach (scenarioSteps($action) as $index => $step) {
            $lines[] = ((string)($index + 1)) . '. ' . $step;
        }
        $lines[] = '';
        $lines[] = 'Endpoints asociados:';
        array_push($lines, ...markdownList($actionEndpoints, '- Ninguno inferido para esta accion.'));
        $lines[] = '';
    }

    $lines[] = '## Campos Y Acciones Detectadas En Pantalla';
    $lines[] = '';
    $lines[] = 'Campos:';
    array_push($lines, ...markdownList($fields, '- Ninguno detectado.'));
    $lines[] = '';
    $lines[] = 'Acciones JavaScript:';
    array_push($lines, ...markdownList($jsActions, '- Ninguna detectada.'));
    $lines[] = '';
    $lines[] = '## Endpoints Del Flujo';
    $lines[] = '';
    array_push($lines, ...markdownList($endpoints, 'No se han detectado endpoints.'));
    $lines[] = '';
    $lines[] = '## Revision Manual';
    $lines[] = '';
    $lines[] = '- Confirmar si el flujo debe separarse en varios flujos de usuario.';
    $lines[] = '- Cambiar nombres tecnicos por nombres de usuario.';
    $lines[] = '- Completar precondiciones, permisos, validaciones y errores comunes.';
    $lines[] = '- Redactar los pasos definitivos para el manual de usuario.';
    $lines[] = '';

    return implode(PHP_EOL, $lines);
}

function outputDir(string $module, string $output): string
{
    if ($output === '') {
        return repoRoot() . "/docs/catalogo/{$module}/flujos";
    }

    $path = str_starts_with($output, '/') ? $output : repoRoot() . '/' . $output;

    return normalizePath("{$path}/{$module}/flujos");
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

function fileNameForCapability(string $capabilityId): string
{
    $parts = explode('.', $capabilityId);
    $base = $parts[count($parts) - 2] ?? end($parts);

    return preg_replace('/[^A-Za-z0-9_.-]+/', '_', (string)$base) . '.md';
}

$options = parseOptions($argv);
$module = $options['module'];
$capabilities = readSection($module, 'capacidades', 'capacidad');
$screens = readSection($module, 'pantallas', 'pantalla_frontend');

if ($capabilities === []) {
    fail("No se han encontrado capacidades en docs/catalogo/{$module}/capacidades/*.md");
}
if ($screens === []) {
    fail("No se han encontrado pantallas en docs/catalogo/{$module}/pantallas/*.md");
}

$targetDir = outputDir($module, $options['output']);
if (!$options['dry-run']) {
    ensureDirectory($targetDir);
}

$created = 0;
$skipped = 0;

foreach ($capabilities as $capabilityId => $capability) {
    $relatedScreens = screensForCapability($capabilityId, $screens);
    $target = $targetDir . '/' . fileNameForCapability($capabilityId);
    if (is_file($target) && !$options['force']) {
        echo 'SKIP  ' . relativePath($target) . " (ya existe; usa --force para sobrescribir)" . PHP_EOL;
        $skipped++;
        continue;
    }

    if ($options['dry-run']) {
        echo 'WRITE ' . relativePath($target) . PHP_EOL;
        continue;
    }

    if (file_put_contents($target, renderFlow($module, $capability, $relatedScreens)) === false) {
        fail('No se pudo escribir: ' . relativePath($target));
    }

    echo 'WRITE ' . relativePath($target) . PHP_EOL;
    $created++;
}

echo PHP_EOL;
echo "Modulo: {$module}" . PHP_EOL;
echo 'Capacidades leidas: ' . count($capabilities) . PHP_EOL;
echo 'Pantallas leidas: ' . count($screens) . PHP_EOL;
echo "Ficheros escritos: {$created}" . PHP_EOL;
echo "Ficheros omitidos: {$skipped}" . PHP_EOL;
echo 'Salida: ' . relativePath($targetDir) . PHP_EOL;

