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
(?:static\s+)?function\s*\([^)]*\)(?:\s*:\s*\??[\w\\|]+)?\s*(?:use\s*\([^)]*\)\s*)?
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

    $inputHelperPattern = '/input_(?P<helper>string|int|string_list)\(\s*(?P<arrayVar>\$_POST|\$_GET|\$[a-zA-Z_][a-zA-Z0-9_]*)\s*,\s*[\'"](?P<name>[^\'"]+)[\'"]/';
    preg_match_all($inputHelperPattern, $contents, $matches, PREG_SET_ORDER);
    foreach ($matches as $match) {
        $inputs[] = [
            'source' => inferInputHelperSource($match['arrayVar']),
            'name' => $match['name'],
            'type' => inferInputHelperType($match['helper']),
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

function inferInputHelperSource(string $arrayVar): string
{
    return $arrayVar === '$_GET' ? 'get' : 'post';
}

function inferInputHelperType(string $helper): string
{
    return match ($helper) {
        'int' => 'integer',
        'string_list' => 'array',
        default => 'string',
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

function applicationClassToPath(string $classFqn): string
{
    $relative = str_replace('\\', '/', $classFqn) . '.php';

    return normalizePath(repoRoot() . '/' . $relative);
}

/**
 * @return array{
 *   summary: string,
 *   inputs: list<array{name: string, type: string, source: string}>,
 *   required: list<string>,
 *   errors: list<string>,
 *   return_shape: list<array{name: string, type: string}>,
 *   effects: list<string>
 * }
 */
function analyzeApplicationClass(string $classFqn): array
{
    $default = [
        'summary' => '',
        'inputs' => [],
        'required' => [],
        'errors' => [],
        'return_shape' => [],
        'effects' => [],
        'permisos' => [],
    ];

    $path = applicationClassToPath($classFqn);
    if (!is_file($path)) {
        return $default;
    }

    $contents = file_get_contents($path);
    if ($contents === false) {
        return $default;
    }

    $default['summary'] = extractClassSummary($contents);
    $default['effects'] = extractEffectsFromDoc($contents);
    $default['permisos'] = extractPermissionHints($contents);
    $methodBody = extractApplicationMethodBody($contents);
    if ($methodBody !== '') {
        $default['inputs'] = extractApplicationInputs($methodBody);
        $default['required'] = extractRequiredFields($methodBody);
    }
    $default['errors'] = extractErrorMessages($contents);
    $default['return_shape'] = extractReturnShape($contents);
    $default['permisos'] = extractPermissionHints($contents);

    return $default;
}

/** @return list<string> */
function extractPermissionHints(string $contents): array
{
    preg_match_all("/have_perm_oficina\(\s*['\"](?P<perm>[^'\"]+)['\"]\s*\)/", $contents, $matches);
    $perms = array_values(array_unique($matches['perm'] ?? []));
    if ($perms === []) {
        return [];
    }

    return array_map(static fn (string $perm): string => 'Permiso oficina `' . $perm . '`', $perms);
}

function extractClassSummary(string $contents): string
{
    if (!preg_match('#/\*\*(?P<doc>.*?)\*/#s', $contents, $m)) {
        return '';
    }

    $doc = cleanPhpComment($m['doc']);
    $doc = preg_replace('/@\w+.*$/m', '', $doc) ?? $doc;
    $doc = trim(preg_replace('/\s+/', ' ', $doc) ?? $doc);

    return $doc;
}

/** @return list<string> */
function extractEffectsFromDoc(string $contents): array
{
    if (!preg_match('#/\*\*(?P<doc>.*?)\*/#s', $contents, $m)) {
        return [];
    }

    $doc = cleanPhpComment($m['doc']);
    $effects = [];
    $keywords = ['dossier', 'asistente', 'HashB', 'elimina', 'abrir', 'cerrar', 'sincroniz'];
    foreach (preg_split('/(?<=[.!?])\s+/', $doc) ?: [] as $sentence) {
        $sentence = trim($sentence);
        if ($sentence === '') {
            continue;
        }
        foreach ($keywords as $keyword) {
            if (stripos($sentence, $keyword) !== false) {
                $effects[] = $sentence;
                break;
            }
        }
    }

    return array_values(array_unique($effects));
}

function extractApplicationMethodBody(string $contents): string
{
    foreach (['execute', 'build'] as $method) {
        foreach (['public\s+static\s+', 'public\s+'] as $prefix) {
            $pattern = '/' . $prefix . 'function\s+' . $method . '\s*\([^)]*\)\s*(?::\s*[^{]+)?\{(?P<body>.*)\n\s*\}/s';
            if (preg_match($pattern, $contents, $m)) {
                return $m['body'];
            }
        }
    }

    return '';
}

/**
 * @return list<array{name: string, type: string, source: string}>
 */
function extractApplicationInputs(string $methodBody): array
{
    $inputs = [];

    $helperPattern = '/input_(?P<helper>string|int|string_list)\(\s*\$(?:input|post|a_post)\s*,\s*[\'"](?P<name>[^\'"]+)[\'"]/';
    preg_match_all($helperPattern, $methodBody, $matches, PREG_SET_ORDER);
    foreach ($matches as $match) {
        $inputs[$match['name']] = [
            'name' => $match['name'],
            'type' => inferInputHelperType($match['helper']),
            'source' => 'post',
        ];
    }

    $pattern = '/\$(?:input|post|a_post)\[\s*[\'"](?P<name>[^\'"]+)[\'"]\s*\]/';
    preg_match_all($pattern, $methodBody, $matches, PREG_SET_ORDER);
    foreach ($matches as $match) {
        $name = $match['name'];
        if (isset($inputs[$name]) && $inputs[$name]['type'] !== 'mixed') {
            continue;
        }
        $type = inferApplicationFieldType($methodBody, $name);
        $inputs[$name] = [
            'name' => $name,
            'type' => $type,
            'source' => 'post',
        ];
    }

    return array_values($inputs);
}

function inferApplicationFieldType(string $methodBody, string $name): string
{
    $quoted = preg_quote($name, '/');
    if (preg_match('/input_(?P<helper>string|int|string_list)\(\s*\$(?:input|post|a_post)\s*,\s*[\'"]' . $quoted . '[\'"]/', $methodBody, $helperMatch)) {
        return inferInputHelperType($helperMatch['helper']);
    }
    if (preg_match('/\(\s*array\s*\)\s*\(\s*\$_(?:input|post)\[\s*[\'"]' . $quoted . '[\'"]\s*\]/', $methodBody)) {
        return 'array';
    }
    if (preg_match('/\(\s*int\s*\)\s*\(\s*\$_(?:input|post)\[\s*[\'"]' . $quoted . '[\'"]/', $methodBody)) {
        return 'integer';
    }
    if (preg_match('/\(\s*string\s*\)\s*\(\s*\$_(?:input|post)\[\s*[\'"]' . $quoted . '[\'"]/', $methodBody)) {
        return 'string';
    }
    if (preg_match('/\(\s*bool\s*\)\s*\(\s*\$_(?:input|post)\[\s*[\'"]' . $quoted . '[\'"]/', $methodBody)) {
        return 'boolean';
    }
    if (preg_match('/!\s*empty\s*\(\s*\$_(?:input|post)\[\s*[\'"]' . $quoted . '[\'"]/', $methodBody)) {
        return 'boolean';
    }
    if (preg_match('/\$(?:input|post)\[\s*[\'"]' . $quoted . '[\'"]\s*\]/', $methodBody, $m, PREG_OFFSET_CAPTURE)) {
        $pos = $m[0][1];
        $lineStart = strrpos(substr($methodBody, 0, $pos), "\n") ?: 0;
        $line = substr($methodBody, $lineStart, $pos - $lineStart + strlen($m[0][0]));
        if (preg_match('/\(\s*int\s*\)/', $line)) {
            return 'integer';
        }
        if (preg_match('/\(\s*string\s*\)/', $line)) {
            return 'string';
        }
        if (preg_match('/\(\s*bool\s*\)/', $line)) {
            return 'boolean';
        }
        if (preg_match('/\(\s*array\s*\)/', $line)) {
            return 'array';
        }
    }

    return 'mixed';
}

/** @return list<string> */
function extractRequiredFields(string $methodBody): array
{
    $required = [];
    if (preg_match_all('/if\s*\((?P<cond>[^)]+)\)\s*\{\s*return\s+_?\(/', $methodBody, $matches)) {
        foreach ($matches['cond'] as $cond) {
            foreach (extractFieldNamesFromCondition($cond) as $name) {
                $required[] = $name;
            }
        }
    }

    return array_values(array_unique($required));
}

/** @return list<string> */
function extractFieldNamesFromCondition(string $cond): array
{
    $names = [];
    if (preg_match_all('/\$(?:input|post|a_post|id_\w+)\[\s*[\'"](?P<name>[^\'"]+)[\'"]\s*\]/', $cond, $m)) {
        $names = array_merge($names, $m['name']);
    }
    if (preg_match_all('/\$(id_[a-z_]+)\s*<=\s*0/', $cond, $m)) {
        $names = array_merge($names, $m[1]);
    }

    return array_values(array_unique($names));
}

/** @return list<string> */
function extractErrorMessages(string $contents): array
{
    preg_match_all('/return\s+(?:\(string\)\s*)?_\(\s*(["\'])(?P<msg>(?:\\\\.|(?!\1).)*)\1\s*\)/s', $contents, $matches);
    $messages = $matches['msg'] ?? [];
    $decoded = [];
    foreach ($messages as $message) {
        $decoded[] = stripcslashes($message);
    }

    return array_values(array_unique($decoded));
}

/**
 * @return list<array{name: string, type: string}>
 */
function extractReturnShape(string $contents): array
{
    $marker = '@return array{';
    $pos = strpos($contents, $marker);
    if ($pos === false) {
        return [];
    }

    $start = $pos + strlen($marker);
    $depth = 1;
    $len = strlen($contents);
    $end = $start;
    for (; $end < $len && $depth > 0; $end++) {
        $char = $contents[$end];
        if ($char === '{') {
            $depth++;
        } elseif ($char === '}') {
            $depth--;
        }
    }

    if ($depth !== 0) {
        return [];
    }

    $shapeBlock = substr($contents, $start, $end - $start - 1);
    $shape = [];
    foreach (preg_split('/\R/', $shapeBlock) ?: [] as $line) {
        $line = trim($line);
        $line = ltrim($line, '* ');
        $line = rtrim($line, ',');
        if (!preg_match('/^(?P<name>[A-Za-z0-9_]+)\s*:\s*(?P<type>.+)$/', $line, $parts)) {
            continue;
        }
        $shape[] = [
            'name' => $parts['name'],
            'type' => normalizeShapeType($parts['type']),
        ];
    }

    return $shape;
}

function normalizeShapeType(string $type): string
{
    $type = trim($type);
    if (str_starts_with($type, 'array')) {
        return 'array';
    }

    return match ($type) {
        'bool' => 'boolean',
        'int' => 'integer',
        'float' => 'number',
        default => $type,
    };
}

/**
 * @return array{field: string, action: string}|null
 */
function extractHashBFromController(string $contents): ?array
{
    if (!preg_match('/filter_input\(\s*INPUT_POST\s*,\s*[\'"](?P<field>ctx_[^\'"]+)[\'"]/', $contents, $fieldMatch)) {
        return null;
    }
    if (!preg_match('/HashB::open\(\s*\$[^,]+,\s*[\'"](?P<action>[^\'"]+)[\'"]\s*\)/', $contents, $actionMatch)) {
        return null;
    }

    return [
        'field' => $fieldMatch['field'],
        'action' => $actionMatch['action'],
    ];
}

function inferOperacion(string $endpoint): string
{
    if (str_ends_with($endpoint, '_lista_data')) {
        return 'lista_data';
    }
    if (str_ends_with($endpoint, '_form_data')) {
        return 'form_data';
    }

    return 'mutacion';
}

/**
 * @param list<array{source: string, name: string, type: string, evidence: string}> $controllerInputs
 * @param list<array{name: string, type: string, source: string}> $applicationInputs
 * @return list<array{source: string, name: string, type: string, evidence: string, from: string}>
 */
function mergeInputs(array $controllerInputs, array $applicationInputs): array
{
    $merged = [];
    foreach ($controllerInputs as $input) {
        $merged[$input['name']] = [
            'source' => $input['source'],
            'name' => $input['name'],
            'type' => $input['type'],
            'evidence' => $input['evidence'],
            'from' => 'controller',
        ];
    }
    foreach ($applicationInputs as $input) {
        if (isset($merged[$input['name']])) {
            if ($merged[$input['name']]['type'] === 'mixed' && $input['type'] !== 'mixed') {
                $merged[$input['name']]['type'] = $input['type'];
            }
            $merged[$input['name']]['from'] = 'controller+application';
            continue;
        }
        $merged[$input['name']] = [
            'source' => $input['source'],
            'name' => $input['name'],
            'type' => $input['type'],
            'evidence' => 'application layer',
            'from' => 'application',
        ];
    }

    usort($merged, static function (array $a, array $b): int {
        return [$a['source'], $a['name']] <=> [$b['source'], $b['name']];
    });

    return array_values($merged);
}

/**
 * @param list<string> $uses
 * @return array{
 *   summary: string,
 *   inputs: list<array{name: string, type: string, source: string}>,
 *   required: list<string>,
 *   errors: list<string>,
 *   return_shape: list<array{name: string, type: string}>,
 *   effects: list<string>
 * }
 */
function analyzeApplicationUses(array $uses): array
{
    $combined = [
        'summary' => '',
        'inputs' => [],
        'required' => [],
        'errors' => [],
        'return_shape' => [],
        'effects' => [],
        'permisos' => [],
    ];

    foreach ($uses as $classFqn) {
        $analysis = analyzeApplicationClass($classFqn);
        if ($combined['summary'] === '' && $analysis['summary'] !== '') {
            $combined['summary'] = $analysis['summary'];
        }
        $combined['inputs'] = mergeApplicationInputLists($combined['inputs'], $analysis['inputs']);
        $combined['required'] = array_values(array_unique([...$combined['required'], ...$analysis['required']]));
        $combined['errors'] = array_values(array_unique([...$combined['errors'], ...$analysis['errors']]));
        if ($combined['return_shape'] === [] && $analysis['return_shape'] !== []) {
            $combined['return_shape'] = $analysis['return_shape'];
        }
        $combined['effects'] = array_values(array_unique([...$combined['effects'], ...$analysis['effects']]));
        $combined['permisos'] = array_values(array_unique([...$combined['permisos'], ...$analysis['permisos']]));
    }

    return $combined;
}

/**
 * @param list<array{name: string, type: string, source: string}> $a
 * @param list<array{name: string, type: string, source: string}> $b
 * @return list<array{name: string, type: string, source: string}>
 */
function mergeApplicationInputLists(array $a, array $b): array
{
    $merged = [];
    foreach ([...$a, ...$b] as $input) {
        if (!isset($merged[$input['name']]) || $merged[$input['name']]['type'] === 'mixed') {
            $merged[$input['name']] = $input;
        }
    }

    return array_values($merged);
}

function responseDataSchemaName(string $module, string $endpoint, array $uses): string
{
    if ($uses !== []) {
        $short = basename(str_replace('\\', '/', $uses[0]));

        return $module . '_' . $short . 'Data';
    }

    return $module . '_' . $endpoint . '_Data';
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
 * @param list<array{source: string, name: string, type: string, evidence: string, from?: string}> $inputs
 * @param list<string> $frontendReferences
 * @param list<string> $uses
 * @param array{helper: string, shape: string, evidence: string} $response
 * @param array{
 *   summary: string,
 *   inputs: list<array{name: string, type: string, source: string}>,
 *   required: list<string>,
 *   errors: list<string>,
 *   return_shape: list<array{name: string, type: string}>,
 *   effects: list<string>,
 *   permisos: list<string>
 * } $application
 * @param array{field: string, action: string}|null $hashB
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
    bool $wholeGet,
    array $application,
    ?array $hashB
): string {
    $url = $route['url'];
    $endpoint = basename($url);
    $id = endpointId($module, $url);
    $title = titleFromEndpoint($endpoint);
    $controller = relativePath($route['controller']);
    $methods = array_map('trim', explode(',', $route['method']));
    $operacion = inferOperacion($endpoint);
    $tags = array_values(array_unique(array_filter([
        $module,
        ...preg_split('/[_-]+/', $endpoint) ?: [],
    ])));

    $required = array_values(array_unique($application['required']));
    if ($hashB !== null && !in_array($hashB['field'], $required, true)) {
        $required[] = $hashB['field'];
    }
    $inputNames = array_map(
        static fn (array $input): string => $input['source'] . '.' . $input['name'] . ':' . $input['type'],
        $inputs
    );

    $respuestaData = array_map(
        static fn (array $field): string => $field['name'] . ':' . $field['type'],
        $application['return_shape']
    );
    $respuestaDataSchema = $respuestaData !== []
        ? responseDataSchemaName($module, $endpoint, $uses)
        : '';

    $frontMatter = [];
    $frontMatter[] = '---';
    $frontMatter[] = 'id: ' . yamlString($id);
    $frontMatter[] = 'tipo: "endpoint"';
    $frontMatter[] = 'modulo: ' . yamlString($module);
    $frontMatter[] = 'url: ' . yamlString($url);
    $frontMatter[] = 'metodos: ' . yamlInlineList($methods);
    $frontMatter[] = 'operacion: ' . yamlString($operacion);
    $frontMatter[] = 'controller: ' . yamlString($controller);
    $frontMatter[] = 'entrada: ' . yamlInlineList($inputNames);
    $frontMatter[] = 'entrada_obligatoria: ' . yamlInlineList($required);
    $frontMatter[] = 'respuesta: ' . yamlString($response['shape'] ?: 'pendiente_revision');
    if ($respuestaDataSchema !== '') {
        $frontMatter[] = 'respuesta_data_schema: ' . yamlString($respuestaDataSchema);
        $frontMatter[] = 'respuesta_data: ' . yamlInlineList($respuestaData);
    }
    if ($hashB !== null) {
        $frontMatter[] = 'requiere_hashb: true';
        $frontMatter[] = 'hashb_campo: ' . yamlString($hashB['field']);
        $frontMatter[] = 'hashb_action: ' . yamlString($hashB['action']);
        if (!in_array('Operación no autorizada', $application['errors'], true)) {
            $application['errors'][] = 'Operación no autorizada';
        }
    } else {
        $frontMatter[] = 'requiere_hashb: false';
    }
    if ($application['errors'] !== []) {
        $frontMatter[] = 'errores: ' . yamlInlineList($application['errors']);
    }
    $frontMatter[] = 'frontend_referencias: ' . yamlInlineList($frontendReferences);
    $frontMatter[] = 'casos_uso: ' . yamlInlineList($uses);
    $frontMatter[] = 'tags: ' . yamlInlineList($tags);
    $frontMatter[] = 'estado_revision: "generado"';
    $frontMatter[] = '---';

    $description = $summary !== '' ? $summary : ($application['summary'] !== '' ? $application['summary'] : 'Descripcion funcional pendiente de revisar.');

    $lines = $frontMatter;
    $lines[] = '';
    $lines[] = '# ' . $title;
    $lines[] = '';
    $lines[] = $description;
    $lines[] = '';
    $lines[] = 'Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).';
    $lines[] = '';
    $lines[] = '## Endpoint';
    $lines[] = '';
    $lines[] = '- URL: `' . $url . '`';
    $lines[] = '- Metodos registrados: `' . $route['method'] . '`';
    $lines[] = '- Operacion: `' . $operacion . '`';
    $lines[] = '- Controller: `' . $controller . '`';
    if ($route['route_comment'] !== '') {
        $lines[] = '- Comentario de ruta: ' . $route['route_comment'];
    }
    $lines[] = '';
    $lines[] = '## Entrada';
    $lines[] = '';

    if ($inputs === []) {
        $lines[] = 'Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).';
    } else {
        $lines[] = '| Campo | Tipo | Origen | Obligatorio | Notas |';
        $lines[] = '|-------|------|--------|-------------|-------|';
        foreach ($inputs as $input) {
            $obligatorio = in_array($input['name'], $required, true) ? 'Si' : 'No';
            $notas = $input['from'] ?? 'controller';
            if ($hashB !== null && in_array($input['name'], ['id_item', 'id_ubi', 'year'], true)) {
                $notas .= '; ignorado en body si viene en cápsula HashB';
            }
            $lines[] = sprintf(
                '| `%s` | `%s` | %s | %s | %s |',
                $input['name'],
                $input['type'],
                $input['from'] ?? 'controller',
                $obligatorio,
                $notas
            );
        }
    }
    if ($wholePost) {
        $lines[] = '';
        $lines[] = 'El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.';
    }
    if ($wholeGet) {
        $lines[] = '';
        $lines[] = 'Nota: el controller tambien lee `$_GET` directamente.';
    }

    if ($hashB !== null) {
        $lines[] = '';
        $lines[] = '## Autorizacion HashB';
        $lines[] = '';
        $lines[] = '- Campo POST: `' . $hashB['field'] . '`';
        $lines[] = '- Accion: `' . $hashB['action'] . '`';
        $lines[] = '- Cápsula invalida: `success: false`, `mensaje: "Operación no autorizada"`.';
        $lines[] = '- Ver `docs/dev/hash_arquitectura.md`.';
    }

    $lines[] = '';
    $lines[] = '## Salida';
    $lines[] = '';
    if ($response['helper'] !== '') {
        $lines[] = '- Helper: `' . $response['helper'] . '`';
        $lines[] = '- Forma: `' . ($response['shape'] ?: 'pendiente_revision') . '`';
        if ($operacion === 'mutacion') {
            $lines[] = '- Exito: `success: true`, `data: "ok"`.';
        }
        if ($respuestaData !== []) {
            $lines[] = '- Payload en `data` (schema `' . $respuestaDataSchema . '`):';
            foreach ($application['return_shape'] as $field) {
                $lines[] = '  - `' . $field['name'] . '` (`' . $field['type'] . '`)';
            }
        }
    } else {
        $lines[] = 'No se ha detectado salida estandar. Revisar manualmente.';
    }

    if ($application['effects'] !== []) {
        $lines[] = '';
        $lines[] = '## Efectos colaterales';
        $lines[] = '';
        foreach ($application['effects'] as $effect) {
            $lines[] = '- ' . $effect;
        }
    }

    if ($application['permisos'] !== []) {
        $lines[] = '';
        $lines[] = '## Permisos';
        $lines[] = '';
        foreach ($application['permisos'] as $permiso) {
            $lines[] = '- ' . $permiso;
        }
    }

    if ($application['errors'] !== []) {
        $lines[] = '';
        $lines[] = '## Errores conocidos';
        $lines[] = '';
        foreach ($application['errors'] as $error) {
            $lines[] = '- `' . str_replace('`', '', $error) . '`';
        }
    }

    $lines[] = '';
    $lines[] = '## Casos De Uso';
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
    $lines[] = '- Confirmar permisos/autorizacion de oficina.';
    $lines[] = '- Anadir ejemplos reales de request/response.';
    $lines[] = '- Marcar `estado_revision: "revisado"` cuando este validado.';

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
    $controllerPath = $route['controller'];
    $controllerContents = is_file($controllerPath) ? (file_get_contents($controllerPath) ?: '') : '';
    $analysis = analyzeController($controllerPath);
    $application = analyzeApplicationUses($analysis['uses']);
    $hashB = $controllerContents !== '' ? extractHashBFromController($controllerContents) : null;
    $mergedInputs = mergeInputs($analysis['inputs'], $application['inputs']);
    $frontendReferences = findFrontendReferences($route['url']);
    $summary = $analysis['summary'] !== '' ? $analysis['summary'] : $application['summary'];
    $markdown = renderMarkdown(
        $module,
        $route,
        $mergedInputs,
        $frontendReferences,
        $analysis['uses'],
        $analysis['response'],
        $summary,
        $analysis['whole_post'],
        $analysis['whole_get'],
        $application,
        $hashB
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

