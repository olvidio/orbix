<?php

declare(strict_types=1);

/**
 * Genera fichas Markdown de pantallas frontend a partir de frontend/<modulo>/controller/*.php.
 *
 * Uso:
 *   php docs/scripts/generar_pantallas_modulo.php actividadtarifas
 *   php docs/scripts/generar_pantallas_modulo.php actividadtarifas --dry-run
 *   php docs/scripts/generar_pantallas_modulo.php actividadtarifas --force
 *   php docs/scripts/generar_pantallas_modulo.php actividadtarifas --output=docs/catalogo
 *
 * Salida por defecto:
 *   docs/catalogo/<modulo>/pantallas/*.md
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
function controllerFilesForModule(string $module): array
{
    $files = glob(repoRoot() . "/frontend/{$module}/controller/*.php");
    if ($files === false) {
        return [];
    }

    sort($files);

    return $files;
}

/** @return list<string> */
function renderedViews(string $module, string $controllerPath, string $contents): array
{
    $views = [];
    preg_match_all('/->renderizar\(\s*[\'"](?P<view>[^\'"]+\.phtml)[\'"]/', $contents, $matches);
    foreach ($matches['view'] ?? [] as $view) {
        $views[] = repoRoot() . "/frontend/{$module}/view/" . basename($view);
    }

    $baseView = repoRoot() . "/frontend/{$module}/view/" . basename($controllerPath, '.php') . '.phtml';
    if (is_file($baseView)) {
        $views[] = $baseView;
    }

    $views = array_values(array_unique(array_filter($views, 'is_file')));
    sort($views);

    return $views;
}

function readFileOrEmpty(string $path): string
{
    $contents = file_get_contents($path);

    return $contents === false ? '' : $contents;
}

function cleanPhpComment(string $comment): string
{
    $comment = preg_replace('#/\*\*?|\*/#', '', $comment) ?? $comment;
    $lines = preg_split('/\R/', $comment) ?: [];
    $clean = [];

    foreach ($lines as $line) {
        $line = preg_replace('/^\s*(\/\/|\*)\s?/', '', $line) ?? $line;
        $line = trim($line);
        if ($line !== '' && !str_starts_with($line, '@')) {
            $clean[] = $line;
        }
    }

    return trim(implode(' ', $clean));
}

function extractSummary(string $contents): string
{
    if (!preg_match('#/\*\*(?P<doc>.*?)\*/#s', $contents, $m)) {
        return '';
    }

    $doc = cleanPhpComment($m['doc']);
    $parts = preg_split('/(?<=[.!?])\s+/', $doc) ?: [];

    return trim($parts[0] ?? $doc);
}

/** @return list<string> */
function extractEndpoints(string $contents): array
{
    preg_match_all('#/src/[A-Za-z0-9_./-]+#', $contents, $matches);
    $endpoints = [];
    foreach ($matches[0] ?? [] as $endpoint) {
        $endpoint = preg_replace('/\.php$/', '', $endpoint) ?? $endpoint;
        $endpoint = rtrim($endpoint, ".,);'\"");
        if (str_ends_with($endpoint, '_')) {
            continue;
        }
        $endpoints[] = $endpoint;
    }

    $endpoints = array_values(array_unique($endpoints));
    sort($endpoints);

    return $endpoints;
}

/** @return list<string> */
function extractFrontendLinks(string $contents): array
{
    preg_match_all('#frontend/[A-Za-z0-9_/.-]+/controller/[A-Za-z0-9_.-]+\.php#', $contents, $matches);
    $links = array_values(array_unique($matches[0] ?? []));
    sort($links);

    return $links;
}

/** @return list<string> */
function extractFields(string $contents): array
{
    $fields = [];

    preg_match_all('/filter_input\(\s*INPUT_(?P<source>POST|GET)\s*,\s*[\'"](?P<name>[^\'"]+)[\'"]/', $contents, $matches, PREG_SET_ORDER);
    foreach ($matches as $match) {
        $fields[] = strtolower($match['source']) . '.' . $match['name'];
    }

    preg_match_all('/setCamposForm\(\s*[\'"](?P<fields>[^\'"]*)[\'"]\s*\)/', $contents, $matches);
    foreach ($matches['fields'] ?? [] as $fieldList) {
        foreach (preg_split('/[!|,\s]+/', $fieldList) ?: [] as $field) {
            $field = trim($field);
            if ($field !== '') {
                $fields[] = 'form.' . $field;
            }
        }
    }

    preg_match_all('/<(input|select|textarea)\b[^>]*\bname\s*=\s*[\'"](?P<name>[^\'"]+)[\'"]/i', $contents, $matches);
    foreach ($matches['name'] ?? [] as $name) {
        $fields[] = 'html.' . $name;
    }

    $fields = array_values(array_unique($fields));
    sort($fields);

    return $fields;
}

/** @return list<string> */
function extractActions(string $contents): array
{
    $actions = [];

    preg_match_all('/\b(?P<name>fnjs_[A-Za-z0-9_]+)\s*=\s*function\b/', $contents, $matches);
    foreach ($matches['name'] ?? [] as $name) {
        $actions[] = $name;
    }

    preg_match_all('/\b(?P<name>fnjs_[A-Za-z0-9_]+)\s*\(/', $contents, $matches);
    foreach ($matches['name'] ?? [] as $name) {
        $actions[] = $name;
    }

    preg_match_all('/<input\b[^>]*\btype\s*=\s*[\'"]button[\'"][^>]*\bvalue\s*=\s*[\'"](?P<value>[^\'"]+)[\'"]/i', $contents, $matches);
    foreach ($matches['value'] ?? [] as $value) {
        $value = trim(strip_tags($value));
        if ($value !== '') {
            $actions[] = 'button:' . $value;
        }
    }

    $actions = array_values(array_unique($actions));
    sort($actions);

    return $actions;
}

function screenKind(string $controllerPath, string $contents): string
{
    $base = basename($controllerPath, '.php');
    if (preg_match('/(_lista|_form|_ajax|_data)$/', $base) || str_contains($contents, 'PostRequest::getDataFromUrl')) {
        return 'fragmento_ajax';
    }

    return 'pantalla';
}

function titleFromController(string $controllerPath): string
{
    return ucwords(str_replace('_', ' ', basename($controllerPath, '.php')));
}

/** @return array<string, array{id: string, endpoints: list<string>}> */
function capabilityMap(string $module): array
{
    $files = glob(repoRoot() . "/docs/catalogo/{$module}/capacidades/*.md");
    if ($files === false) {
        return [];
    }

    $map = [];
    foreach ($files as $file) {
        $contents = readFileOrEmpty($file);
        if (!preg_match('/^---\R(?P<yaml>.*?)\R---/s', $contents, $m)) {
            continue;
        }
        $frontMatter = parseSimpleYaml($m['yaml']);
        $id = (string)($frontMatter['id'] ?? '');
        $endpoints = asStringList($frontMatter['endpoints'] ?? []);
        if ($id !== '') {
            $map[$id] = [
                'id' => $id,
                'endpoints' => $endpoints,
            ];
        }
    }

    return $map;
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

/** @param list<string> $endpoints */
function relatedCapabilities(array $endpoints, array $capabilityMap): array
{
    $related = [];
    foreach ($capabilityMap as $capability) {
        if (array_intersect($endpoints, $capability['endpoints']) !== []) {
            $related[] = $capability['id'];
        }
    }

    sort($related);

    return $related;
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

/**
 * @param list<string> $items
 * @return list<string>
 */
function markdownList(array $items, string $emptyText): array
{
    if ($items === []) {
        return [$emptyText];
    }

    return array_map(static fn (string $item): string => '- `' . $item . '`', $items);
}

/** @param array<string, mixed> $screen */
function renderScreen(array $screen): string
{
    $lines = [
        '---',
        'id: ' . yamlString($screen['id']),
        'tipo: "pantalla_frontend"',
        'subtipo: ' . yamlString($screen['kind']),
        'modulo: ' . yamlString($screen['module']),
        'nombre: ' . yamlString($screen['title']),
        'controller: ' . yamlString($screen['controller']),
        'vistas: ' . yamlInlineList($screen['views']),
        'fragmentos_frontend: ' . yamlInlineList($screen['frontend_links']),
        'endpoints: ' . yamlInlineList($screen['endpoints']),
        'capacidades: ' . yamlInlineList($screen['capabilities']),
        'campos: ' . yamlInlineList($screen['fields']),
        'acciones: ' . yamlInlineList($screen['actions']),
        'estado_revision: "generado"',
        '---',
        '',
        '# ' . $screen['title'],
        '',
        $screen['summary'] !== '' ? $screen['summary'] : 'Descripcion funcional pendiente de revisar.',
        '',
        '## Tipo',
        '',
        '- Subtipo: `' . $screen['kind'] . '`',
        '- Controller: `' . $screen['controller'] . '`',
        '',
        '## Vistas Relacionadas',
        '',
        ...markdownList($screen['views'], 'No se han detectado vistas PHTML relacionadas.'),
        '',
        '## Fragmentos Frontend Relacionados',
        '',
        ...markdownList($screen['frontend_links'], 'No se han detectado controladores frontend relacionados.'),
        '',
        '## Endpoints Usados',
        '',
        ...markdownList($screen['endpoints'], 'No se han detectado endpoints `/src/...`.'),
        '',
        '## Capacidades Relacionadas',
        '',
        ...markdownList($screen['capabilities'], 'No se han detectado capacidades relacionadas.'),
        '',
        '## Campos Detectados',
        '',
        ...markdownList($screen['fields'], 'No se han detectado campos de formulario.'),
        '',
        '## Acciones Detectadas',
        '',
        ...markdownList($screen['actions'], 'No se han detectado acciones.'),
        '',
        '## Manual De Usuario',
        '',
        'Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.',
        '',
        '## Revision Manual',
        '',
        '- Confirmar si es pantalla principal o fragmento AJAX.',
        '- Completar nombre funcional orientado a usuario.',
        '- Revisar campos obligatorios y significado de cada accion.',
        '- Confirmar si las capacidades relacionadas son correctas.',
        '',
    ];

    return implode(PHP_EOL, $lines);
}

function outputDir(string $module, string $output): string
{
    if ($output === '') {
        return repoRoot() . "/docs/catalogo/{$module}/pantallas";
    }

    $path = str_starts_with($output, '/') ? $output : repoRoot() . '/' . $output;

    return normalizePath("{$path}/{$module}/pantallas");
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
$controllers = controllerFilesForModule($module);
if ($controllers === []) {
    fail("No se han encontrado controllers en frontend/{$module}/controller/*.php");
}

$capabilities = capabilityMap($module);
$targetDir = outputDir($module, $options['output']);
if (!$options['dry-run']) {
    ensureDirectory($targetDir);
}

$created = 0;
$skipped = 0;

foreach ($controllers as $controllerPath) {
    $controllerContents = readFileOrEmpty($controllerPath);
    $viewPaths = renderedViews($module, $controllerPath, $controllerContents);
    $combinedContents = $controllerContents;
    foreach ($viewPaths as $viewPath) {
        $combinedContents .= PHP_EOL . readFileOrEmpty($viewPath);
    }

    $endpoints = extractEndpoints($combinedContents);
    $screen = [
        'id' => $module . '.pantalla.' . basename($controllerPath, '.php'),
        'module' => $module,
        'title' => titleFromController($controllerPath),
        'kind' => screenKind($controllerPath, $controllerContents),
        'controller' => relativePath($controllerPath),
        'views' => array_map('relativePath', $viewPaths),
        'frontend_links' => extractFrontendLinks($combinedContents),
        'endpoints' => $endpoints,
        'capabilities' => relatedCapabilities($endpoints, $capabilities),
        'fields' => extractFields($combinedContents),
        'actions' => extractActions($combinedContents),
        'summary' => extractSummary($controllerContents),
    ];

    $target = $targetDir . '/' . basename($controllerPath, '.php') . '.md';
    if (is_file($target) && !$options['force']) {
        echo 'SKIP  ' . relativePath($target) . " (ya existe; usa --force para sobrescribir)" . PHP_EOL;
        $skipped++;
        continue;
    }

    if ($options['dry-run']) {
        echo 'WRITE ' . relativePath($target) . PHP_EOL;
        continue;
    }

    if (file_put_contents($target, renderScreen($screen)) === false) {
        fail('No se pudo escribir: ' . relativePath($target));
    }

    echo 'WRITE ' . relativePath($target) . PHP_EOL;
    $created++;
}

echo PHP_EOL;
echo "Modulo: {$module}" . PHP_EOL;
echo 'Controllers leidos: ' . count($controllers) . PHP_EOL;
echo "Ficheros escritos: {$created}" . PHP_EOL;
echo "Ficheros omitidos: {$skipped}" . PHP_EOL;
echo 'Salida: ' . relativePath($targetDir) . PHP_EOL;

