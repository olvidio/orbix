<?php

declare(strict_types=1);

/**
 * Genera fichas Markdown con front matter YAML para los endpoints de un modulo.
 *
 * Uso:
 *   php docs/scripts/generar_api_modulo_md.php actividadtarifas
 *   php docs/scripts/generar_api_modulo_md.php actividadtarifas --dry-run
 *   php docs/scripts/generar_api_modulo_md.php actividadtarifas --output=docs/catalogo
 *   php docs/scripts/generar_api_modulo_md.php actividadtarifas --force
 *
 * Salida por defecto:
 *   docs/catalogo/<modulo>/api/<endpoint>.md
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

/** @return array<string, string|bool> */
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

    if (!preg_match('/^[a-zA-Z0-9_]+$/', (string)$options['module'])) {
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

/** @return list<array{method: string, url: string, controller: string, route_comment: string}> */
function parseRoutes(string $module): array
{
    $root = repoRoot();
    $routesPath = "{$root}/src/{$module}/config/routes.php";
    if (!is_file($routesPath)) {
        fail("No existe el fichero de rutas: " . relativePath($routesPath));
    }

    $contents = file_get_contents($routesPath);
    if ($contents === false) {
        fail("No se puede leer: " . relativePath($routesPath));
    }

    $pattern = <<<'REGEX'
/
(?P<comment>(?:\s*(?:
    \/\/[^\n]*\n
  | \/\*.*?\*\/\s*
))*)
\$r->addRoute\(
\s*(?P<methods>\[[^\]]+\]|'[^']+'|"[^"]+")
\s*,\s*
['"](?P<url>[^'"]+)['"]
\s*,\s*
(?:static\s+)?function\s*\([^)]*\)\s*(?:use\s*\([^)]*\)\s*)?
\{
(?P<body>.*?)
\}\s*\);
/sx
REGEX;

    preg_match_all($pattern, $contents, $matches, PREG_SET_ORDER);

    $routes = [];
    foreach ($matches as $match) {
        $url = $match['url'];
        $controller = controllerPathFromRouteBody($module, $match['body'], $url);
        $routes[] = [
            'method' => parseMethodExpr($match['methods']),
            'url' => $url,
            'controller' => $controller,
            'route_comment' => cleanPhpComment($match['comment'] ?? ''),
        ];
    }

    return $routes;
}

function controllerPathFromRouteBody(string $module, string $body, string $url): string
{
    $root = repoRoot();
    $controllerDir = "{$root}/src/{$module}/infrastructure/ui/http/controllers";

    if (preg_match('/require\s+\$base\s*\.\s*[\'"](?P<suffix>[^\'"]+)[\'"]\s*;/', $body, $m)) {
        return normalizePath($controllerDir . '/' . ltrim($m['suffix'], '/'));
    }

    if (preg_match('/require\s+__DIR__\s*\.\s*[\'"](?P<suffix>[^\'"]+)[\'"]\s*;/', $body, $m)) {
        return normalizePath("{$root}/src/{$module}/config/" . $m['suffix']);
    }

    if (preg_match('/require\s+(?P<expr>[^;]+);/', $body, $m)) {
        $file = basename(str_replace(['"', "'"], '', trim($m['expr'])));
        if ($file !== '') {
            return normalizePath($controllerDir . '/' . $file);
        }
    }

    $endpoint = basename($url);
    return normalizePath("{$controllerDir}/{$endpoint}.php");
}

function parseMethodExpr(string $expr): string
{
    preg_match_all('/[\'"](?P<method>[A-Z]+)[\'"]/', $expr, $matches);
    if (!empty($matches['method'])) {
        return implode(', ', array_values(array_unique($matches['method'])));
    }

    return trim($expr, " \t\n\r\0\x0B'\"");
}

function cleanPhpComment(string $comment): string
{
    $comment = trim($comment);
    if ($comment === '') {
        return '';
    }

    $comment = preg_replace('#/\*\*?|\*/#', '', $comment) ?? $comment;
    $lines = preg_split('/\R/', $comment) ?: [];
    $clean = [];
    foreach ($lines as $line) {
        $line = preg_replace('/^\s*(\/\/|\*)\s?/', '', $line) ?? $line;
        $line = trim($line);
        if ($line !== '') {
            $clean[] = $line;
        }
    }

    return trim(implode(' ', $clean));
}

/**
 * @return array{
 *   summary: string,
 *   inputs: list<array{source: string, name: string, type: string, evidence: string}>,
 *   uses: list<string>,
 *   response: array{helper: string, shape: string, evidence: string},
 *   whole_post: bool,
 *   whole_get: bool
 * }
 */
function analyzeController(string $controllerPath): array
{
    $default = [
        'summary' => '',
        'inputs' => [],
        'uses' => [],
        'response' => [
            'helper' => '',
            'shape' => '',
            'evidence' => '',
        ],
        'whole_post' => false,
        'whole_get' => false,
    ];

    if (!is_file($controllerPath)) {
        return $default;
    }

    $contents = file_get_contents($controllerPath);
    if ($contents === false) {
        return $default;
    }

    $default['summary'] = extractSummary($contents);
    $default['inputs'] = extractInputs($contents);
    $default['uses'] = extractApplicationUses($contents);
    $default['response'] = extractResponse($contents);
    $default['whole_post'] = preg_match('/\$_POST\b/', $contents) === 1;
    $default['whole_get'] = preg_match('/\$_GET\b/', $contents) === 1;

    return $default;
}

function extractSummary(string $contents): string
{
    if (!preg_match('#/\*\*(?P<doc>.*?)\*/#s', $contents, $m)) {
        return '';
    }

    $doc = cleanPhpComment($m['doc']);
    $doc = preg_replace('/@\w+.*$/', '', $doc) ?? $doc;
    $parts = preg_split('/(?<=[.!?])\s+/', trim($doc));

    return trim((string)($parts[0] ?? $doc));
}

/** @return list<array{source: string, name: string, type: string, evidence: string}> */
function extractInputs(string $contents): array
{
    $inputs = [];

    $filterPattern = '/(?P<cast>\([a-zA-Z_\\\\][a-zA-Z0-9_\\\\]*\)\s*)?filter_input\(\s*INPUT_(?P<source>POST|GET)\s*,\s*[\'"](?P<name>[^\'"]+)[\'"](?P<rest>[^)]*)\)/';
    preg_match_all($filterPattern, $contents, $matches, PREG_SET_ORDER);
    foreach ($matches as $match) {
        $inputs[] = [
            'source' => strtolower($match['source']),
            'name' => $match['name'],
            'type' => inferInputType($match['cast'] ?? '', $match['rest'] ?? ''),
            'evidence' => trim($match[0]),
        ];
    }

    $superGlobalPattern = '/\$_(?P<source>POST|GET)\s*\[\s*[\'"](?P<name>[^\'"]+)[\'"]\s*\]/';
    preg_match_all($superGlobalPattern, $contents, $matches, PREG_SET_ORDER);
    foreach ($matches as $match) {
        $inputs[] = [
            'source' => strtolower($match['source']),
            'name' => $match['name'],
            'type' => 'mixed',
            'evidence' => trim($match[0]),
        ];
    }

    $byKey = [];
    foreach ($inputs as $input) {
        $key = $input['source'] . ':' . $input['name'];
        if (!isset($byKey[$key]) || $byKey[$key]['type'] === 'mixed') {
            $byKey[$key] = $input;
        }
    }

    usort($byKey, static function (array $a, array $b): int {
        return [$a['source'], $a['name']] <=> [$b['source'], $b['name']];
    });

    return array_values($byKey);
}

function inferInputType(string $cast, string $rest): string
{
    $cast = trim($cast, " \t\n\r\0\x0B()");
    $rest = trim($rest);

    if (str_contains($rest, 'FILTER_REQUIRE_ARRAY')) {
        return 'array';
    }

    return match (strtolower($cast)) {
        'int', 'integer' => 'integer',
        'string' => 'string',
        'bool', 'boolean' => 'boolean',
        'float', 'double' => 'number',
        default => 'mixed',
    };
}

/** @return list<string> */
function extractApplicationUses(string $contents): array
{
    preg_match_all('/^use\s+(?P<class>src\\\\[^;]+\\\\application\\\\[^;]+);/m', $contents, $matches);
    $classes = $matches['class'] ?? [];
    $classes = array_values(array_unique($classes));
    sort($classes);

    return $classes;
}

/** @return array{helper: string, shape: string, evidence: string} */
function extractResponse(string $contents): array
{
    if (preg_match('/ContestarJson::(?P<helper>enviarDataAnidado|enviar|send)\((?P<args>.*?)\)\s*;/s', $contents, $m)) {
        $helper = $m['helper'];
        $args = preg_replace('/\s+/', ' ', trim($m['args'])) ?? trim($m['args']);
        $shape = match ($helper) {
            'send' => 'custom_json',
            'enviarDataAnidado' => 'standard_envelope_nested_data',
            default => 'standard_envelope_string_data',
        };

        return [
            'helper' => 'ContestarJson::' . $helper,
            'shape' => $shape,
            'evidence' => $args,
        ];
    }

    if (preg_match('/echo\s+(?P<expr>[^;]+);/', $contents, $m)) {
        return [
            'helper' => 'echo',
            'shape' => 'raw_response',
            'evidence' => trim($m['expr']),
        ];
    }

    return [
        'helper' => '',
        'shape' => '',
        'evidence' => '',
    ];
}

/** @return list<string> */
function findFrontendReferences(string $url): array
{
    $frontendDir = repoRoot() . '/frontend';
    if (!is_dir($frontendDir)) {
        return [];
    }

    $references = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($frontendDir, FilesystemIterator::SKIP_DOTS)
    );

    foreach ($iterator as $fileInfo) {
        if (!$fileInfo instanceof SplFileInfo || !$fileInfo->isFile()) {
            continue;
        }
        if (!in_array($fileInfo->getExtension(), ['php', 'phtml', 'js'], true)) {
            continue;
        }

        $contents = file_get_contents($fileInfo->getPathname());
        if ($contents === false || !str_contains($contents, $url)) {
            continue;
        }

        $references[] = relativePath($fileInfo->getPathname());
    }

    sort($references);

    return $references;
}

function endpointId(string $module, string $url): string
{
    $endpoint = basename($url);
    return "{$module}.{$endpoint}";
}

function endpointFileName(string $url): string
{
    return preg_replace('/[^a-zA-Z0-9_.-]+/', '_', basename($url)) . '.md';
}

function titleFromEndpoint(string $endpoint): string
{
    $title = str_replace(['_', '-'], ' ', $endpoint);
    $title = preg_replace('/\s+/', ' ', $title) ?? $title;

    return ucwords(trim($title));
}

function yamlString(string $value): string
{
    return '"' . str_replace(['\\', '"'], ['\\\\', '\"'], $value) . '"';
}

/** @param list<string> $values */
function yamlInlineList(array $values): string
{
    if ($values === []) {
        return '[]';
    }

    return '[' . implode(', ', array_map('yamlString', $values)) . ']';
}

/**
 * @param list<array{source: string, name: string, type: string, evidence: string}> $inputs
 * @param list<string> $frontendReferences
 * @param list<string> $uses
 * @param array{helper: string, shape: string, evidence: string} $response
 */
function renderMarkdown(
    string $module,
    array $route,
    array $inputs,
    array $frontendReferences,
    array $uses,
    array $response,
    string $summary,
    bool $wholePost,
    bool $wholeGet
): string {
    $url = $route['url'];
    $endpoint = basename($url);
    $id = endpointId($module, $url);
    $title = titleFromEndpoint($endpoint);
    $controller = relativePath($route['controller']);
    $methods = array_map('trim', explode(',', $route['method']));
    $tags = array_values(array_unique(array_filter([
        $module,
        ...preg_split('/[_-]+/', $endpoint) ?: [],
    ])));

    $inputNames = array_map(
        static fn (array $input): string => $input['source'] . '.' . $input['name'] . ':' . $input['type'],
        $inputs
    );

    $frontMatter = [];
    $frontMatter[] = '---';
    $frontMatter[] = 'id: ' . yamlString($id);
    $frontMatter[] = 'tipo: "endpoint"';
    $frontMatter[] = 'modulo: ' . yamlString($module);
    $frontMatter[] = 'url: ' . yamlString($url);
    $frontMatter[] = 'metodos: ' . yamlInlineList($methods);
    $frontMatter[] = 'controller: ' . yamlString($controller);
    $frontMatter[] = 'entrada: ' . yamlInlineList($inputNames);
    $frontMatter[] = 'respuesta: ' . yamlString($response['shape'] ?: 'pendiente_revision');
    $frontMatter[] = 'frontend_referencias: ' . yamlInlineList($frontendReferences);
    $frontMatter[] = 'casos_uso: ' . yamlInlineList($uses);
    $frontMatter[] = 'tags: ' . yamlInlineList($tags);
    $frontMatter[] = 'estado_revision: "generado"';
    $frontMatter[] = '---';

    $lines = $frontMatter;
    $lines[] = '';
    $lines[] = '# ' . $title;
    $lines[] = '';
    $lines[] = $summary !== ''
        ? $summary
        : 'Descripcion funcional pendiente de revisar.';
    $lines[] = '';
    $lines[] = '## Endpoint';
    $lines[] = '';
    $lines[] = '- URL: `' . $url . '`';
    $lines[] = '- Metodos registrados: `' . $route['method'] . '`';
    $lines[] = '- Controller: `' . $controller . '`';
    if ($route['route_comment'] !== '') {
        $lines[] = '- Comentario de ruta: ' . $route['route_comment'];
    }
    $lines[] = '';
    $lines[] = '## Entrada Inferida';
    $lines[] = '';

    if ($inputs === []) {
        $lines[] = 'No se han detectado parametros individuales mediante `filter_input`, `$_POST[...]` o `$_GET[...]`.';
    } else {
        foreach ($inputs as $input) {
            $lines[] = '- `' . $input['source'] . '.' . $input['name'] . '` (`' . $input['type'] . '`)';
        }
    }
    if ($wholePost) {
        $lines[] = '- Nota: el controller usa `$_POST` directamente; revisar si acepta mas campos que los listados.';
    }
    if ($wholeGet) {
        $lines[] = '- Nota: el controller usa `$_GET` directamente; revisar si acepta mas campos que los listados.';
    }

    $lines[] = '';
    $lines[] = '## Salida Inferida';
    $lines[] = '';
    if ($response['helper'] !== '') {
        $lines[] = '- Helper: `' . $response['helper'] . '`';
        $lines[] = '- Forma: `' . ($response['shape'] ?: 'pendiente_revision') . '`';
        $lines[] = '- Evidencia: `' . $response['evidence'] . '`';
    } else {
        $lines[] = 'No se ha detectado salida estandar. Revisar manualmente.';
    }

    $lines[] = '';
    $lines[] = '## Casos De Uso Detectados';
    $lines[] = '';
    if ($uses === []) {
        $lines[] = 'No se han detectado imports de `src\\...\\application\\...`.';
    } else {
        foreach ($uses as $use) {
            $lines[] = '- `' . $use . '`';
        }
    }

    $lines[] = '';
    $lines[] = '## Frontend Relacionado';
    $lines[] = '';
    if ($frontendReferences === []) {
        $lines[] = 'No se han encontrado referencias exactas al endpoint en `frontend/`.';
    } else {
        foreach ($frontendReferences as $reference) {
            $lines[] = '- `' . $reference . '`';
        }
    }

    $lines[] = '';
    $lines[] = '## Revision Manual';
    $lines[] = '';
    $lines[] = '- Completar objetivo funcional.';
    $lines[] = '- Confirmar permisos/autorizacion.';
    $lines[] = '- Confirmar efectos sobre datos.';
    $lines[] = '- Anadir ejemplos reales de request/response.';
    $lines[] = '- Marcar procesos parecidos o duplicados si aplica.';
    $lines[] = '';

    return implode(PHP_EOL, $lines);
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
$module = (string)$options['module'];
$output = (string)$options['output'];
$outputDir = $output !== ''
    ? normalizePath((str_starts_with($output, '/') ? $output : repoRoot() . '/' . $output) . '/' . $module . '/api')
    : repoRoot() . "/docs/catalogo/{$module}/api";

$routes = parseRoutes($module);
if ($routes === []) {
    fail("No se han encontrado rutas en src/{$module}/config/routes.php");
}

if (!$options['dry-run']) {
    ensureDirectory($outputDir);
}

$created = 0;
$skipped = 0;

foreach ($routes as $route) {
    $analysis = analyzeController($route['controller']);
    $frontendReferences = findFrontendReferences($route['url']);
    $markdown = renderMarkdown(
        $module,
        $route,
        $analysis['inputs'],
        $frontendReferences,
        $analysis['uses'],
        $analysis['response'],
        $analysis['summary'],
        $analysis['whole_post'],
        $analysis['whole_get']
    );

    $target = $outputDir . '/' . endpointFileName($route['url']);
    if (is_file($target) && !$options['force']) {
        echo 'SKIP  ' . relativePath($target) . " (ya existe; usa --force para sobrescribir)" . PHP_EOL;
        $skipped++;
        continue;
    }

    if ($options['dry-run']) {
        echo 'WRITE ' . relativePath($target) . PHP_EOL;
        continue;
    }

    if (file_put_contents($target, $markdown) === false) {
        fail("No se pudo escribir: " . relativePath($target));
    }

    echo 'WRITE ' . relativePath($target) . PHP_EOL;
    $created++;
}

echo PHP_EOL;
echo "Modulo: {$module}" . PHP_EOL;
echo 'Rutas detectadas: ' . count($routes) . PHP_EOL;
echo "Ficheros escritos: {$created}" . PHP_EOL;
echo "Ficheros omitidos: {$skipped}" . PHP_EOL;
echo 'Salida: ' . relativePath($outputDir) . PHP_EOL;

