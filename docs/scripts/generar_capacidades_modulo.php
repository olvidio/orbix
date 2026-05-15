<?php

declare(strict_types=1);

/**
 * Genera propuestas de capacidades a partir de las fichas API del catalogo.
 *
 * Uso:
 *   php docs/scripts/generar_capacidades_modulo.php actividadtarifas
 *   php docs/scripts/generar_capacidades_modulo.php actividadtarifas --dry-run
 *   php docs/scripts/generar_capacidades_modulo.php actividadtarifas --force
 *   php docs/scripts/generar_capacidades_modulo.php actividadtarifas --output=docs/catalogo
 *
 * Entrada por defecto:
 *   docs/catalogo/<modulo>/api/*.md
 *
 * Salida por defecto:
 *   docs/catalogo/<modulo>/capacidades/*.md
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

/** @return list<string> */
function markdownFilesForModule(string $module): array
{
    $files = glob(repoRoot() . "/docs/catalogo/{$module}/api/*.md");
    if ($files === false) {
        return [];
    }

    sort($files);

    return $files;
}

/**
 * @return array{
 *   id: string,
 *   modulo: string,
 *   url: string,
 *   entrada: list<string>,
 *   frontend_referencias: list<string>,
 *   casos_uso: list<string>,
 *   tags: list<string>,
 *   title: string,
 *   description: string,
 *   source: string
 * }|null
 */
function readCatalogEndpoint(string $file): ?array
{
    $contents = file_get_contents($file);
    if ($contents === false) {
        return null;
    }

    if (!preg_match('/^---\R(?P<yaml>.*?)\R---\R(?P<body>.*)$/s', $contents, $m)) {
        return null;
    }

    $frontMatter = parseSimpleYaml($m['yaml']);
    if (($frontMatter['tipo'] ?? '') !== 'endpoint') {
        return null;
    }

    return [
        'id' => (string)($frontMatter['id'] ?? ''),
        'modulo' => (string)($frontMatter['modulo'] ?? ''),
        'url' => (string)($frontMatter['url'] ?? ''),
        'entrada' => asStringList($frontMatter['entrada'] ?? []),
        'frontend_referencias' => asStringList($frontMatter['frontend_referencias'] ?? []),
        'casos_uso' => asStringList($frontMatter['casos_uso'] ?? []),
        'tags' => asStringList($frontMatter['tags'] ?? []),
        'title' => extractTitle($m['body']),
        'description' => extractDescription($m['body']),
        'source' => relativePath($file),
    ];
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

function extractTitle(string $body): string
{
    if (preg_match('/^#\s+(?P<title>.+)$/m', $body, $m)) {
        return trim($m['title']);
    }

    return 'Capacidad';
}

function extractDescription(string $body): string
{
    $body = ltrim($body);
    $body = preg_replace('/^#\s+.+\R+/', '', $body, 1) ?? $body;
    $parts = preg_split('/\R{2,}/', trim($body)) ?: [];

    foreach ($parts as $part) {
        $part = trim($part);
        if ($part !== '' && !str_starts_with($part, '##')) {
            return preg_replace('/\s+/', ' ', $part) ?? $part;
        }
    }

    return '';
}

/**
 * @return array{grupo: string, accion: string}
 */
function inferGroupAndAction(string $url): array
{
    $endpoint = basename($url);
    $rules = [
        '_lista_data' => 'listar',
        '_lista' => 'listar',
        '_form_data' => 'ver_formulario',
        '_form' => 'ver_formulario',
        '_update_inc' => 'actualizar_incremento',
        '_update' => 'crear_actualizar',
        '_eliminar' => 'eliminar',
        '_delete' => 'eliminar',
        '_copiar' => 'copiar',
        '_data' => 'obtener_datos',
        '_datos' => 'obtener_datos',
        '_get' => 'obtener',
        '_guardar' => 'guardar',
        '_crear' => 'crear',
        '_nueva' => 'crear',
        '_nuevo' => 'crear',
    ];

    foreach ($rules as $suffix => $action) {
        if (str_ends_with($endpoint, $suffix)) {
            return [
                'grupo' => substr($endpoint, 0, -strlen($suffix)),
                'accion' => $action,
            ];
        }
    }

    return [
        'grupo' => $endpoint,
        'accion' => 'ejecutar',
    ];
}

function titleFromGroup(string $group): string
{
    return 'Gestionar ' . ucwords(str_replace('_', ' ', $group));
}

/** @param list<string> $uses */
function entitiesFromUseCases(array $uses, string $fallbackGroup): array
{
    $entities = [];
    foreach ($uses as $use) {
        $class = basename(str_replace('\\', '/', $use));
        $entity = preg_replace('/(ListaData|FormData|UpdateInc|Update|Eliminar|Delete|Copiar|Data|Guardar|Crear|Nuevo|Nueva)$/', '', $class) ?? $class;
        if ($entity !== '') {
            $entities[] = $entity;
        }
    }

    if ($entities === []) {
        $entities[] = str_replace(' ', '', ucwords(str_replace('_', ' ', $fallbackGroup)));
    }

    $entities = array_values(array_unique($entities));
    sort($entities);

    return $entities;
}

/**
 * @param list<array<string, mixed>> $endpoints
 * @return array<string, array<string, mixed>>
 */
function groupCapabilities(array $endpoints): array
{
    $groups = [];

    foreach ($endpoints as $endpoint) {
        $inferred = inferGroupAndAction($endpoint['url']);
        $group = $inferred['grupo'];
        if (!isset($groups[$group])) {
            $groups[$group] = [
                'grupo' => $group,
                'acciones' => [],
                'endpoints' => [],
                'pantallas' => [],
                'casos_uso' => [],
                'tags' => [],
                'descripciones' => [],
                'sources' => [],
            ];
        }

        $groups[$group]['acciones'][] = $inferred['accion'];
        $groups[$group]['endpoints'][] = $endpoint['url'];
        $groups[$group]['pantallas'] = array_merge($groups[$group]['pantallas'], $endpoint['frontend_referencias']);
        $groups[$group]['casos_uso'] = array_merge($groups[$group]['casos_uso'], $endpoint['casos_uso']);
        $groups[$group]['tags'] = array_merge($groups[$group]['tags'], $endpoint['tags']);
        $groups[$group]['sources'][] = $endpoint['source'];
        if ($endpoint['description'] !== '') {
            $groups[$group]['descripciones'][] = $endpoint['description'];
        }
    }

    foreach ($groups as &$group) {
        foreach (['acciones', 'endpoints', 'pantallas', 'casos_uso', 'tags', 'descripciones', 'sources'] as $key) {
            $group[$key] = array_values(array_unique($group[$key]));
            sort($group[$key]);
        }
        $group['entidades'] = entitiesFromUseCases($group['casos_uso'], $group['grupo']);
    }
    unset($group);

    ksort($groups);

    return $groups;
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

/** @param array<string, mixed> $capability */
function renderCapability(string $module, array $capability): string
{
    $group = $capability['grupo'];
    $id = "{$module}.{$group}.gestionar";
    $title = titleFromGroup($group);
    $tags = array_values(array_unique(array_merge([$module, $group], $capability['tags'])));
    sort($tags);

    $lines = [
        '---',
        'id: ' . yamlString($id),
        'tipo: "capacidad"',
        'modulo: ' . yamlString($module),
        'nombre: ' . yamlString($title),
        'entidades: ' . yamlInlineList($capability['entidades']),
        'acciones: ' . yamlInlineList($capability['acciones']),
        'endpoints: ' . yamlInlineList($capability['endpoints']),
        'pantallas: ' . yamlInlineList($capability['pantallas']),
        'casos_uso: ' . yamlInlineList($capability['casos_uso']),
        'tags: ' . yamlInlineList($tags),
        'estado_revision: "generado"',
        '---',
        '',
        '# ' . $title,
        '',
        'Propuesta generada automaticamente a partir de endpoints con prefijo comun `' . $group . '`.',
        '',
        '## Objetivo Funcional',
        '',
        'Pendiente de revisar. Describir aqui que proceso de negocio cubre esta capacidad.',
        '',
        '## Acciones Detectadas',
        '',
    ];

    foreach ($capability['acciones'] as $action) {
        $lines[] = '- `' . $action . '`';
    }

    $lines[] = '';
    $lines[] = '## Endpoints';
    $lines[] = '';
    foreach ($capability['endpoints'] as $endpoint) {
        $lines[] = '- `' . $endpoint . '`';
    }

    $lines[] = '';
    $lines[] = '## Pantallas Relacionadas';
    $lines[] = '';
    if ($capability['pantallas'] === []) {
        $lines[] = 'No se han detectado pantallas relacionadas.';
    } else {
        foreach ($capability['pantallas'] as $screen) {
            $lines[] = '- `' . $screen . '`';
        }
    }

    $lines[] = '';
    $lines[] = '## Casos De Uso Detectados';
    $lines[] = '';
    if ($capability['casos_uso'] === []) {
        $lines[] = 'No se han detectado casos de uso de aplicacion.';
    } else {
        foreach ($capability['casos_uso'] as $useCase) {
            $lines[] = '- `' . $useCase . '`';
        }
    }

    $lines[] = '';
    $lines[] = '## Pistas Desde Endpoints';
    $lines[] = '';
    foreach ($capability['descripciones'] as $description) {
        $lines[] = '- ' . $description;
    }

    $lines[] = '';
    $lines[] = '## Revision Manual';
    $lines[] = '';
    $lines[] = '- Confirmar si todos los endpoints pertenecen a la misma capacidad.';
    $lines[] = '- Separar esta capacidad si mezcla procesos distintos.';
    $lines[] = '- Marcar capacidades parecidas o duplicadas en otros modulos.';
    $lines[] = '- Completar permisos, efectos sobre datos y ejemplos de uso.';
    $lines[] = '';

    return implode(PHP_EOL, $lines);
}

function outputDir(string $module, string $output): string
{
    if ($output === '') {
        return repoRoot() . "/docs/catalogo/{$module}/capacidades";
    }

    $path = str_starts_with($output, '/') ? $output : repoRoot() . '/' . $output;

    return normalizePath("{$path}/{$module}/capacidades");
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
$files = markdownFilesForModule($module);
if ($files === []) {
    fail("No se han encontrado fichas en docs/catalogo/{$module}/api/*.md");
}

$endpoints = [];
foreach ($files as $file) {
    $endpoint = readCatalogEndpoint($file);
    if ($endpoint !== null && $endpoint['url'] !== '') {
        $endpoints[] = $endpoint;
    }
}

if ($endpoints === []) {
    fail("No se han encontrado endpoints validos en docs/catalogo/{$module}/api/*.md");
}

$capabilities = groupCapabilities($endpoints);
$targetDir = outputDir($module, $options['output']);

if (!$options['dry-run']) {
    ensureDirectory($targetDir);
}

$created = 0;
$skipped = 0;

foreach ($capabilities as $capability) {
    $target = $targetDir . '/' . $capability['grupo'] . '.md';
    if (is_file($target) && !$options['force']) {
        echo 'SKIP  ' . relativePath($target) . " (ya existe; usa --force para sobrescribir)" . PHP_EOL;
        $skipped++;
        continue;
    }

    if ($options['dry-run']) {
        echo 'WRITE ' . relativePath($target) . PHP_EOL;
        continue;
    }

    if (file_put_contents($target, renderCapability($module, $capability)) === false) {
        fail('No se pudo escribir: ' . relativePath($target));
    }

    echo 'WRITE ' . relativePath($target) . PHP_EOL;
    $created++;
}

echo PHP_EOL;
echo "Modulo: {$module}" . PHP_EOL;
echo 'Endpoints leidos: ' . count($endpoints) . PHP_EOL;
echo 'Capacidades detectadas: ' . count($capabilities) . PHP_EOL;
echo "Ficheros escritos: {$created}" . PHP_EOL;
echo "Ficheros omitidos: {$skipped}" . PHP_EOL;
echo 'Salida: ' . relativePath($targetDir) . PHP_EOL;

