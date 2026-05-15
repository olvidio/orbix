<?php

declare(strict_types=1);

/**
 * Genera un OpenAPI YAML a partir de las fichas Markdown de docs/catalogo.
 *
 * Uso:
 *   php docs/scripts/generar_openapi_desde_catalogo.php actividadtarifas
 *   php docs/scripts/generar_openapi_desde_catalogo.php actividadtarifas --dry-run
 *   php docs/scripts/generar_openapi_desde_catalogo.php actividadtarifas --force
 *   php docs/scripts/generar_openapi_desde_catalogo.php actividadtarifas --output=docs/catalogo
 *
 * Entrada por defecto:
 *   docs/catalogo/<modulo>/api/*.md
 *
 * Salida por defecto:
 *   docs/catalogo/<modulo>/openapi.yaml
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
  php {$script} <modulo> [--output=<directorio-o-fichero.yaml>] [--dry-run] [--force]

Ejemplos:
  php {$script} actividadtarifas
  php {$script} actividadtarifas --dry-run
  php {$script} actividadtarifas --output=docs/catalogo
  php {$script} actividadtarifas --output=docs/catalogo/actividadtarifas/openapi.yaml

TXT;
}

/** @return list<string> */
function markdownFilesForModule(string $module): array
{
    $pattern = repoRoot() . "/docs/catalogo/{$module}/api/*.md";
    $files = glob($pattern);
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
 *   metodos: list<string>,
 *   entrada: list<string>,
 *   respuesta: string,
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

    $body = $m['body'];
    $title = extractTitle($body);
    $description = extractDescription($body);

    return [
        'id' => (string)($frontMatter['id'] ?? ''),
        'modulo' => (string)($frontMatter['modulo'] ?? ''),
        'url' => (string)($frontMatter['url'] ?? ''),
        'metodos' => array_values(array_filter(asStringList($frontMatter['metodos'] ?? []))),
        'entrada' => array_values(array_filter(asStringList($frontMatter['entrada'] ?? []))),
        'respuesta' => (string)($frontMatter['respuesta'] ?? ''),
        'tags' => array_values(array_filter(asStringList($frontMatter['tags'] ?? []))),
        'title' => $title,
        'description' => $description,
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

        $key = $m['key'];
        $value = trim($m['value']);
        if (str_starts_with($value, '[') && str_ends_with($value, ']')) {
            $data[$key] = parseInlineList($value);
            continue;
        }

        $data[$key] = unquoteYamlString($value);
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

    $length = strlen($inside);
    for ($i = 0; $i < $length; $i++) {
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
    if (
        strlen($value) >= 2
        && str_starts_with($value, '"')
        && str_ends_with($value, '"')
    ) {
        $value = substr($value, 1, -1);

        return str_replace(['\\"', '\\\\'], ['"', '\\'], $value);
    }

    return $value;
}

/** @param string|list<string> $value */
function asStringList(string|array $value): array
{
    if (is_array($value)) {
        return array_map('strval', $value);
    }

    return [$value];
}

function extractTitle(string $body): string
{
    if (preg_match('/^#\s+(?P<title>.+)$/m', $body, $m)) {
        return trim($m['title']);
    }

    return 'Endpoint';
}

function extractDescription(string $body): string
{
    $body = ltrim($body);
    $body = preg_replace('/^#\s+.+\R+/', '', $body, 1) ?? $body;
    $parts = preg_split('/\R{2,}/', trim($body)) ?: [];

    foreach ($parts as $part) {
        $part = trim($part);
        if ($part === '' || str_starts_with($part, '##')) {
            continue;
        }

        return preg_replace('/\s+/', ' ', $part) ?? $part;
    }

    return 'Endpoint generado desde el catalogo.';
}

/** @return list<array{source: string, name: string, type: string}> */
function parseEntrada(array $entrada): array
{
    $inputs = [];
    foreach ($entrada as $entry) {
        if (!preg_match('/^(?P<source>post|get|query|body)\.(?P<name>[^:]+):(?P<type>.+)$/i', $entry, $m)) {
            continue;
        }

        $source = strtolower($m['source']);
        $inputs[] = [
            'source' => $source === 'query' ? 'get' : ($source === 'body' ? 'post' : $source),
            'name' => $m['name'],
            'type' => strtolower($m['type']),
        ];
    }

    return $inputs;
}

/** @param list<array{source: string, name: string, type: string}> $inputs */
function documentedMethods(array $registeredMethods, array $inputs): array
{
    $registered = array_map('strtoupper', $registeredMethods);
    $hasPost = count(array_filter($inputs, static fn (array $input): bool => $input['source'] === 'post')) > 0;
    $hasGet = count(array_filter($inputs, static fn (array $input): bool => $input['source'] === 'get')) > 0;
    $methods = [];

    if ($hasPost && in_array('POST', $registered, true)) {
        $methods[] = 'post';
    }
    if ($hasGet && in_array('GET', $registered, true)) {
        $methods[] = 'get';
    }
    if ($methods === []) {
        if (in_array('POST', $registered, true)) {
            $methods[] = 'post';
        } elseif (in_array('GET', $registered, true)) {
            $methods[] = 'get';
        } elseif ($registered !== []) {
            $methods[] = strtolower($registered[0]);
        } else {
            $methods[] = 'post';
        }
    }

    return $methods;
}

function operationId(string $id, string $method): string
{
    $operationId = strtolower($method) . '_' . str_replace(['.', '-', '/'], '_', $id);
    $operationId = preg_replace('/[^A-Za-z0-9_]+/', '_', $operationId) ?? $operationId;

    return trim($operationId, '_');
}

function openApiScalarType(string $type): string
{
    return match ($type) {
        'integer', 'int' => 'integer',
        'number', 'float', 'double' => 'number',
        'boolean', 'bool' => 'boolean',
        'array' => 'array',
        default => 'string',
    };
}

function schemaRefForResponse(string $respuesta): string
{
    return match ($respuesta) {
        'standard_envelope_nested_data' => '#/components/schemas/OrbixStandardResponseNestedData',
        'custom_json' => '#/components/schemas/OrbixCustomJsonResponse',
        default => '#/components/schemas/OrbixStandardResponseStringData',
    };
}

/** @param list<array{source: string, name: string, type: string}> $inputs */
function renderPathMethod(array $endpoint, string $method, array $inputs): array
{
    $isGet = $method === 'get';
    $relevantInputs = array_values(array_filter(
        $inputs,
        static fn (array $input): bool => $isGet ? $input['source'] === 'get' : $input['source'] === 'post'
    ));

    $lines = [];
    $lines[] = "    {$method}:";
    $lines[] = '      operationId: ' . yamlString(operationId($endpoint['id'], $method));
    $lines[] = '      summary: ' . yamlString($endpoint['title']);
    $lines[] = '      description: ' . yamlBlock($endpoint['description'] . "\n\nFuente catalogo: `" . $endpoint['source'] . '`', 8);
    $lines[] = '      tags:';
    foreach (tagsForEndpoint($endpoint) as $tag) {
        $lines[] = '        - ' . yamlString($tag);
    }

    if ($isGet && $relevantInputs !== []) {
        $lines[] = '      parameters:';
        foreach ($relevantInputs as $input) {
            $lines[] = '        - name: ' . yamlString($input['name']);
            $lines[] = '          in: query';
            $lines[] = '          required: false';
            $lines[] = '          schema:';
            $lines[] = '            type: ' . yamlString(openApiScalarType($input['type']));
        }
    }

    if (!$isGet) {
        $lines[] = '      requestBody:';
        $lines[] = '        required: false';
        $lines[] = '        content:';
        $lines[] = '          application/x-www-form-urlencoded:';
        $lines[] = '            schema:';
        $lines[] = '              type: object';
        $lines[] = '              properties:';
        if ($relevantInputs === []) {
            $lines[] = '                _sin_parametros_detectados:';
            $lines[] = '                  type: string';
            $lines[] = '                  description: ' . yamlString('No se han inferido campos desde el catalogo.');
        } else {
            foreach ($relevantInputs as $input) {
                $lines[] = '                ' . yamlKey($input['name']) . ':';
                $lines[] = '                  type: ' . yamlString(openApiScalarType($input['type']));
                if ($input['type'] === 'array') {
                    $lines[] = '                  items:';
                    $lines[] = '                    type: string';
                }
            }
        }
    }

    $lines[] = '      responses:';
    $lines[] = '        "200":';
    $lines[] = '          description: ' . yamlString('Respuesta generada por Orbix.');
    $lines[] = '          content:';
    $lines[] = '            application/json:';
    $lines[] = '              schema:';
    $lines[] = '                $ref: ' . yamlString(schemaRefForResponse($endpoint['respuesta']));
    $lines[] = '        "400":';
    $lines[] = '          description: ' . yamlString('Peticion no valida o error de validacion.');
    $lines[] = '          content:';
    $lines[] = '            application/json:';
    $lines[] = '              schema:';
    $lines[] = '                $ref: ' . yamlString(schemaRefForResponse($endpoint['respuesta']));

    return $lines;
}

/** @return list<string> */
function tagsForEndpoint(array $endpoint): array
{
    $tags = $endpoint['tags'];
    if ($tags === []) {
        return [$endpoint['modulo']];
    }

    return [reset($tags)];
}

function yamlString(string $value): string
{
    return '"' . str_replace(["\\", '"'], ["\\\\", '\"'], $value) . '"';
}

function yamlKey(string $value): string
{
    if (preg_match('/^[A-Za-z_][A-Za-z0-9_-]*$/', $value)) {
        return $value;
    }

    return yamlString($value);
}

function yamlBlock(string $value, int $indent): string
{
    $spaces = str_repeat(' ', $indent);
    $lines = preg_split('/\R/', $value) ?: [];

    return "|\n" . implode("\n", array_map(static fn (string $line): string => $spaces . $line, $lines));
}

/** @param list<array<string, mixed>> $endpoints */
function renderOpenApi(string $module, array $endpoints): string
{
    $lines = [];
    $lines[] = 'openapi: "3.1.0"';
    $lines[] = 'info:';
    $lines[] = '  title: ' . yamlString("Orbix API - {$module}");
    $lines[] = '  version: "0.1.0"';
    $lines[] = '  description: ' . yamlBlock('OpenAPI generado desde `docs/catalogo`. Revisar obligatoriedad de campos y ejemplos antes de publicarlo como contrato estable.', 4);
    $lines[] = '  license:';
    $lines[] = '    name: "Proprietary"';
    $lines[] = '    identifier: "LicenseRef-Proprietary"';
    $lines[] = 'servers:';
    $lines[] = '  - url: "/"';
    $lines[] = 'security:';
    $lines[] = '  - OrbixSession: []';
    $lines[] = 'paths:';

    $usedResponseRefs = [];
    foreach ($endpoints as $endpoint) {
        $inputs = parseEntrada($endpoint['entrada']);
        $methods = documentedMethods($endpoint['metodos'], $inputs);
        $usedResponseRefs[] = schemaRefForResponse($endpoint['respuesta']);
        $lines[] = '  ' . yamlString($endpoint['url']) . ':';
        foreach ($methods as $method) {
            array_push($lines, ...renderPathMethod($endpoint, $method, $inputs));
        }
    }

    array_push($lines, ...renderComponents(array_values(array_unique($usedResponseRefs))));

    return implode(PHP_EOL, $lines) . PHP_EOL;
}

/**
 * @param list<string> $usedResponseRefs
 * @return list<string>
 */
function renderComponents(array $usedResponseRefs): array
{
    $lines = [
        'components:',
        '  securitySchemes:',
        '    OrbixSession:',
        '      type: apiKey',
        '      in: cookie',
        '      name: PHPSESSID',
        '  schemas:',
    ];

    if (in_array('#/components/schemas/OrbixStandardResponseStringData', $usedResponseRefs, true)) {
        array_push($lines, ...[
            '    OrbixStandardResponseStringData:',
            '      type: object',
            '      required:',
            '        - success',
            '        - data',
            '      properties:',
            '        success:',
            '          type: boolean',
            '        mensaje:',
            '          type: string',
            '        data:',
            '          oneOf:',
            '            - type: string',
            '            - type: object',
            '            - type: array',
            '              items: {}',
            '          description: "En ContestarJson::enviar, los arrays/objetos suelen llegar codificados como string JSON por compatibilidad."',
        ]);
    }

    if (in_array('#/components/schemas/OrbixStandardResponseNestedData', $usedResponseRefs, true)) {
        array_push($lines, ...[
            '    OrbixStandardResponseNestedData:',
            '      type: object',
            '      required:',
            '        - success',
            '        - data',
            '      properties:',
            '        success:',
            '          type: boolean',
            '        mensaje:',
            '          type: string',
            '        data:',
            '          oneOf:',
            '            - type: object',
            '            - type: array',
            '              items: {}',
            '            - type: string',
        ]);
    }

    if (in_array('#/components/schemas/OrbixCustomJsonResponse', $usedResponseRefs, true)) {
        array_push($lines, ...[
            '    OrbixCustomJsonResponse:',
            '      type: object',
            '      additionalProperties: true',
        ]);
    }

    return $lines;
}

function outputPath(string $module, string $output): string
{
    if ($output === '') {
        return repoRoot() . "/docs/catalogo/{$module}/openapi.yaml";
    }

    $path = str_starts_with($output, '/') ? $output : repoRoot() . '/' . $output;
    $path = normalizePath($path);
    if (str_ends_with($path, '.yaml') || str_ends_with($path, '.yml')) {
        return $path;
    }

    return normalizePath("{$path}/{$module}/openapi.yaml");
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
    if ($endpoint === null) {
        continue;
    }
    if ($endpoint['url'] === '' || $endpoint['id'] === '') {
        fwrite(STDERR, 'WARN  Ficha incompleta omitida: ' . relativePath($file) . PHP_EOL);
        continue;
    }
    $endpoints[] = $endpoint;
}

if ($endpoints === []) {
    fail("No se han encontrado endpoints validos en docs/catalogo/{$module}/api/*.md");
}

usort($endpoints, static fn (array $a, array $b): int => $a['url'] <=> $b['url']);

$target = outputPath($module, $options['output']);
$yaml = renderOpenApi($module, $endpoints);

if (is_file($target) && !$options['force'] && !$options['dry-run']) {
    fail('Ya existe ' . relativePath($target) . '. Usa --force para sobrescribir.');
}

if ($options['dry-run']) {
    echo 'WRITE ' . relativePath($target) . PHP_EOL;
    echo 'Endpoints incluidos: ' . count($endpoints) . PHP_EOL;
    exit(0);
}

ensureDirectory(dirname($target));
if (file_put_contents($target, $yaml) === false) {
    fail('No se pudo escribir: ' . relativePath($target));
}

echo 'WRITE ' . relativePath($target) . PHP_EOL;
echo 'Endpoints incluidos: ' . count($endpoints) . PHP_EOL;

