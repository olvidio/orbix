<?php

declare(strict_types=1);

/**
 * Genera un borrador de manual de usuario desde los flujos del catalogo.
 *
 * Uso:
 *   php docs/scripts/generar_manual_usuario_modulo.php actividadtarifas
 *   php docs/scripts/generar_manual_usuario_modulo.php actividadtarifas --dry-run
 *   php docs/scripts/generar_manual_usuario_modulo.php actividadtarifas --force
 *   php docs/scripts/generar_manual_usuario_modulo.php actividadtarifas --output=docs/manual
 *
 * Salida por defecto:
 *   docs/manual/<modulo>.md
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
  php {$script} <modulo> [--output=<directorio-o-fichero.md>] [--dry-run] [--force]

Ejemplos:
  php {$script} actividadtarifas
  php {$script} actividadtarifas --dry-run
  php {$script} actividadtarifas --output=docs/manual

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

/**
 * @return array<string, array<string, mixed>>
 */
function readSection(string $module, string $section, string $expectedType): array
{
    $items = [];
    foreach (catalogFiles($module, $section) as $file) {
        $contents = file_get_contents($file);
        if ($contents === false || !preg_match('/^---\R(?P<yaml>.*?)\R---\R(?P<body>.*)$/s', $contents, $m)) {
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
        $frontMatter['body'] = $m['body'];
        $items[$id] = $frontMatter;
    }

    ksort($items);

    return $items;
}

/** @return list<array{titulo: string, pasos: list<string>}> */
function extractScenarios(string $body): array
{
    if (!preg_match('/^## Escenarios Inferidos\R(?P<section>.*?)(?=^## |\z)/ms', $body, $m)) {
        return [];
    }

    preg_match_all('/^### (?P<title>.+?)\R(?P<body>.*?)(?=^### |\z)/ms', $m['section'], $matches, PREG_SET_ORDER);
    $scenarios = [];
    foreach ($matches as $match) {
        $scenarioBody = preg_replace('/Endpoints asociados:.*$/s', '', $match['body']) ?? $match['body'];
        preg_match_all('/^\d+\.\s+(?P<step>.+)$/m', $scenarioBody, $stepMatches);
        $steps = array_map('trim', $stepMatches['step'] ?? []);
        if ($steps === []) {
            continue;
        }

        $scenarios[] = [
            'titulo' => userScenarioTitle(trim($match['title'])),
            'pasos' => $steps,
        ];
    }

    return $scenarios;
}

function userScenarioTitle(string $title): string
{
    return match ($title) {
        'Actualizar Incremento' => 'Actualizar importes en lote',
        'Crear Actualizar' => 'Crear o modificar',
        'Ver Formulario' => 'Abrir el formulario',
        'Listar' => 'Consultar el listado',
        default => $title,
    };
}

function userFlowTitle(string $name): string
{
    $name = preg_replace('/^Flujo\s+-\s+/i', '', $name) ?? $name;
    $name = preg_replace('/^Gestionar\s+/i', '', $name) ?? $name;

    return trim($name);
}

/** @param array<string, array<string, mixed>> $screens */
function screenLabel(string $screenId, array $screens): string
{
    if (!isset($screens[$screenId])) {
        return $screenId;
    }

    $name = (string)($screens[$screenId]['nombre'] ?? $screenId);
    $controller = (string)($screens[$screenId]['controller'] ?? '');

    return $controller === '' ? $name : "{$name} ({$controller})";
}

/** @param list<string> $screenIds */
function renderScreenList(array $screenIds, array $screens): array
{
    if ($screenIds === []) {
        return ['- Pendiente de revisar.'];
    }

    $lines = [];
    foreach ($screenIds as $screenId) {
        $lines[] = '- ' . screenLabel($screenId, $screens);
    }

    return $lines;
}

/**
 * @param array<string, array<string, mixed>> $flows
 * @param array<string, array<string, mixed>> $screens
 */
function renderManual(string $module, array $flows, array $screens): string
{
    $lines = [
        '---',
        'tipo: "manual_usuario"',
        'modulo: "' . $module . '"',
        'flujos: ' . count($flows),
        'estado_revision: "generado"',
        '---',
        '',
        '# Manual De Usuario - ' . $module,
        '',
        'Este manual es un borrador generado desde `docs/catalogo`. Debe revisarse para ajustar nombres de menu, permisos, validaciones y lenguaje final de usuario.',
        '',
        '## Como Usar Este Manual',
        '',
        'Cada apartado describe una tarea de usuario. Las rutas de menu y nombres visibles pueden necesitar revision manual.',
        '',
    ];

    foreach ($flows as $flowId => $flow) {
        $title = userFlowTitle((string)($flow['nombre'] ?? $flowId));
        $entryScreens = asStringList($flow['pantallas_principales'] ?? []);
        $scenarios = extractScenarios((string)($flow['body'] ?? ''));

        $lines[] = '## ' . $title;
        $lines[] = '';
        $lines[] = '### Para Que Sirve';
        $lines[] = '';
        $lines[] = 'Pendiente de revisar. Explicar aqui, con lenguaje de usuario, que permite hacer esta funcion.';
        $lines[] = '';
        $lines[] = '### Donde Entrar';
        $lines[] = '';
        array_push($lines, ...renderScreenList($entryScreens, $screens));
        $lines[] = '- Ruta de menu: pendiente de documentar.';
        $lines[] = '';
        $lines[] = '### Tareas Habituales';
        $lines[] = '';

        if ($scenarios === []) {
            $lines[] = 'Pendiente de revisar. No se han inferido tareas desde el flujo.';
            $lines[] = '';
        } else {
            foreach ($scenarios as $scenario) {
                $lines[] = '#### ' . $scenario['titulo'];
                $lines[] = '';
                foreach ($scenario['pasos'] as $index => $step) {
                    $lines[] = ((string)($index + 1)) . '. ' . $step;
                }
                $lines[] = '';
            }
        }

        $lines[] = '### Errores O Avisos Frecuentes';
        $lines[] = '';
        $lines[] = '- Pendiente de revisar.';
        $lines[] = '';
        $lines[] = '### Referencias Internas';
        $lines[] = '';
        $lines[] = '- Flujo: `' . $flowId . '`';
        $lines[] = '- Fichero catalogo: `' . (string)($flow['source'] ?? '') . '`';
        $lines[] = '';
    }

    $lines[] = '## Revision Pendiente';
    $lines[] = '';
    $lines[] = '- Sustituir nombres tecnicos por nombres visibles en la aplicacion.';
    $lines[] = '- Completar rutas de menu.';
    $lines[] = '- Confirmar permisos necesarios.';
    $lines[] = '- Anadir capturas o ejemplos si se quiere publicar para usuarios finales.';
    $lines[] = '';

    return implode(PHP_EOL, $lines);
}

function outputPath(string $module, string $output): string
{
    if ($output === '') {
        return repoRoot() . "/docs/manual/{$module}.md";
    }

    $path = str_starts_with($output, '/') ? $output : repoRoot() . '/' . $output;
    $path = normalizePath($path);
    if (str_ends_with($path, '.md')) {
        return $path;
    }

    return normalizePath("{$path}/{$module}.md");
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
$flows = readSection($module, 'flujos', 'flujo_frontend');
$screens = readSection($module, 'pantallas', 'pantalla_frontend');

if ($flows === []) {
    fail("No se han encontrado flujos en docs/catalogo/{$module}/flujos/*.md");
}

$target = outputPath($module, $options['output']);
if (is_file($target) && !$options['force'] && !$options['dry-run']) {
    fail('Ya existe ' . relativePath($target) . '. Usa --force para sobrescribir.');
}

if ($options['dry-run']) {
    echo 'WRITE ' . relativePath($target) . PHP_EOL;
    echo 'Flujos leidos: ' . count($flows) . PHP_EOL;
    echo 'Pantallas leidas: ' . count($screens) . PHP_EOL;
    exit(0);
}

ensureDirectory(dirname($target));
if (file_put_contents($target, renderManual($module, $flows, $screens)) === false) {
    fail('No se pudo escribir: ' . relativePath($target));
}

echo 'WRITE ' . relativePath($target) . PHP_EOL;
echo 'Flujos leidos: ' . count($flows) . PHP_EOL;
echo 'Pantallas leidas: ' . count($screens) . PHP_EOL;

