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

function extractMarkdownSection(string $body, string $heading): string
{
    if (!preg_match('/^## ' . preg_quote($heading, '/') . '\R(?P<section>.*?)(?=^## |\z)/ms', $body, $m)) {
        return '';
    }

    return trim($m['section']);
}

/** @return list<string> */
function extractBulletItems(string $section): array
{
    if ($section === '') {
        return [];
    }

    preg_match_all('/^- (.+)$/m', $section, $matches);
    $items = [];
    foreach ($matches[1] ?? [] as $item) {
        $item = trim($item);
        $item = preg_replace('/^`(.+)`$/', '$1', $item) ?? $item;
        if ($item !== '') {
            $items[] = $item;
        }
    }

    return array_values(array_unique($items));
}

/**
 * @return array<string, array<string, mixed>>
 */
function readApiByUrl(string $module): array
{
    $items = [];
    foreach (glob(repoRoot() . "/docs/catalogo/{$module}/api/*.md") ?: [] as $file) {
        $contents = file_get_contents($file);
        if ($contents === false || !preg_match('/^---\R(?P<yaml>.*?)\R---\R(?P<body>.*)$/s', $contents, $m)) {
            continue;
        }

        $frontMatter = parseSimpleYaml($m['yaml']);
        if (($frontMatter['tipo'] ?? '') !== 'endpoint') {
            continue;
        }

        $url = (string)($frontMatter['url'] ?? '');
        if ($url === '') {
            continue;
        }

        $frontMatter['body'] = $m['body'];
        $frontMatter['permisos'] = extractMarkdownSection($m['body'], 'Permisos');
        $items[$url] = $frontMatter;
    }

    return $items;
}

/** @param list<string> $endpointUrls @param array<string, array<string, mixed>> $apiByUrl */
function collectFlowErrors(array $endpointUrls, array $apiByUrl, string $flowBody): array
{
    $fromFlow = extractBulletItems(extractMarkdownSection($flowBody, 'Errores Conocidos'));
    $fromFlow = array_values(array_filter(
        $fromFlow,
        static fn (string $item): bool => !str_contains($item, 'No se han documentado')
    ));
    if ($fromFlow !== []) {
        return $fromFlow;
    }

    $errors = [];
    foreach ($endpointUrls as $url) {
        if (!isset($apiByUrl[$url])) {
            continue;
        }
        $errors = array_merge($errors, asStringList($apiByUrl[$url]['errores'] ?? []));
    }

    return array_values(array_unique($errors));
}

/** @param list<string> $endpointUrls @param array<string, array<string, mixed>> $apiByUrl */
function collectFlowPermissions(array $endpointUrls, array $apiByUrl): array
{
    $permissions = [];
    foreach ($endpointUrls as $url) {
        if (!isset($apiByUrl[$url])) {
            continue;
        }
        $body = (string)($apiByUrl[$url]['body'] ?? '');
        $permissions = array_merge($permissions, extractBulletItems(extractMarkdownSection($body, 'Permisos')));
    }

    return array_values(array_unique($permissions));
}

function formatPurposeForUser(string $text): string
{
    $text = trim($text);
    if ($text === '' || str_contains($text, 'Pendiente de revisar')) {
        return $text;
    }

    $sentences = preg_split('/\.\s+/', rtrim($text, '.')) ?: [];
    $sentences = array_values(array_filter(array_map('trim', $sentences)));
    if (count($sentences) <= 1) {
        return rtrim($text, '.') . '.';
    }

    $lines = [];
    foreach ($sentences as $sentence) {
        $lines[] = '- ' . rtrim($sentence, '.') . '.';
    }

    return implode(PHP_EOL, $lines);
}

function buildFlowPurpose(array $flow, array $capabilities): string
{
    $flowBody = (string)($flow['body'] ?? '');
    $objetivo = extractMarkdownSection($flowBody, 'Objetivo De Usuario');
    if ($objetivo !== '' && !str_contains($objetivo, 'Pendiente de revisar')) {
        return preg_replace('/\s+/', ' ', $objetivo) ?? $objetivo;
    }

    $capabilityId = (string)($flow['capacidad'] ?? '');
    if ($capabilityId !== '' && isset($capabilities[$capabilityId])) {
        $capabilityBody = (string)($capabilities[$capabilityId]['body'] ?? '');
        $objetivoCap = extractMarkdownSection($capabilityBody, 'Objetivo Funcional');
        if ($objetivoCap !== '' && !str_contains($objetivoCap, 'Pendiente de revisar')) {
            return preg_replace('/\s+/', ' ', $objetivoCap) ?? $objetivoCap;
        }
    }

    return 'Pendiente de revisar. Explicar aqui, con lenguaje de usuario, que permite hacer esta funcion.';
}

/** @param array<string, array<string, mixed>> $capabilities */
function readCapabilities(string $module): array
{
    return readSection($module, 'capacidades', 'capacidad');
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
        return ['- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).'];
    }

    $lines = [];
    foreach ($screenIds as $screenId) {
        $lines[] = '- ' . screenLabel($screenId, $screens);
    }

    return $lines;
}

/**
 * Propaga la seccion «Ruta de menú» del flujo (o de pantallas de entrada).
 *
 * @param list<string> $entryScreens
 * @param array<string, array<string, mixed>> $screens
 * @return list<string>
 */
function renderMenuRouteLines(string $flowBody, array $entryScreens, array $screens): array
{
    $section = extractMarkdownSection($flowBody, 'Ruta de menú');
    if ($section === '') {
        $section = extractMarkdownSection($flowBody, 'Ruta de menu');
    }

    if ($section === '') {
        foreach ($entryScreens as $screenId) {
            $screenBody = (string)($screens[$screenId]['body'] ?? '');
            if ($screenBody === '') {
                continue;
            }
            $section = extractMarkdownSection($screenBody, 'Ruta de menú');
            if ($section === '') {
                $section = extractMarkdownSection($screenBody, 'Ruta de menu');
            }
            if ($section !== '') {
                break;
            }
        }
    }

    if ($section === '') {
        return ['- Ruta de menu: pendiente de documentar.'];
    }

    $lines = [];
    foreach (preg_split('/\R/', $section) ?: [] as $line) {
        $line = trim($line);
        if ($line === '') {
            continue;
        }
        if (!str_starts_with($line, '-')) {
            $line = '- ' . $line;
        }
        $lines[] = $line;
    }

    return $lines === [] ? ['- Ruta de menu: pendiente de documentar.'] : $lines;
}

/**
 * @param array<string, array<string, mixed>> $flows
 * @param array<string, array<string, mixed>> $screens
 * @param array<string, array<string, mixed>> $capabilities
 * @param array<string, array<string, mixed>> $apiByUrl
 */
function renderManual(string $module, array $flows, array $screens, array $capabilities, array $apiByUrl): string
{
    $menuPending = 0;
    $flowBlocks = [];

    foreach ($flows as $flowId => $flow) {
        $title = userFlowTitle((string)($flow['nombre'] ?? $flowId));
        $entryScreens = asStringList($flow['pantallas_principales'] ?? []);
        if ($entryScreens === []) {
            $entryScreens = asStringList($flow['fragmentos'] ?? []);
        }
        $flowBody = (string)($flow['body'] ?? '');
        $endpointUrls = asStringList($flow['endpoints'] ?? []);
        $scenarios = extractScenarios($flowBody);
        $errors = collectFlowErrors($endpointUrls, $apiByUrl, $flowBody);
        $permissions = collectFlowPermissions($endpointUrls, $apiByUrl);
        $menuLines = renderMenuRouteLines($flowBody, $entryScreens, $screens);
        foreach ($menuLines as $ml) {
            if (str_contains($ml, 'pendiente de documentar')) {
                $menuPending++;
            }
        }

        $block = [];
        $block[] = '## ' . $title;
        $block[] = '';
        $block[] = '### Para Que Sirve';
        $block[] = '';
        $block[] = formatPurposeForUser(buildFlowPurpose($flow, $capabilities));
        $block[] = '';
        $block[] = '### Donde Entrar';
        $block[] = '';
        array_push($block, ...renderScreenList($entryScreens, $screens));
        array_push($block, ...$menuLines);
        $block[] = '';
        $block[] = '### Tareas Habituales';
        $block[] = '';

        if ($scenarios === []) {
            $block[] = 'Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.';
            $block[] = '';
        } else {
            foreach ($scenarios as $scenario) {
                $block[] = '#### ' . $scenario['titulo'];
                $block[] = '';
                foreach ($scenario['pasos'] as $index => $step) {
                    $block[] = ((string)($index + 1)) . '. ' . $step;
                }
                $block[] = '';
            }
        }

        $block[] = '### Errores O Avisos Frecuentes';
        $block[] = '';
        if ($errors === []) {
            $block[] = '- No hay errores documentados en el catalogo para este flujo.';
        } else {
            foreach ($errors as $error) {
                $block[] = '- `' . str_replace('`', '', $error) . '`';
            }
        }
        $block[] = '';

        if ($permissions !== []) {
            $block[] = '### Permisos';
            $block[] = '';
            foreach ($permissions as $permission) {
                $block[] = '- ' . $permission;
            }
            $block[] = '';
        }

        $block[] = '### Referencias Internas';
        $block[] = '';
        $block[] = '- Flujo: `' . $flowId . '`';
        $block[] = '- Fichero catalogo: `' . (string)($flow['source'] ?? '') . '`';
        $block[] = '';
        $flowBlocks[] = $block;
    }

    $estado = $menuPending === 0 ? 'revisado_parcial' : 'generado';
    $intro = $menuPending === 0
        ? 'Manual generado desde `docs/catalogo` con rutas de menú del catálogo. Úsalo como guía de usuario; los detalles técnicos están en el catálogo.'
        : 'Manual generado desde `docs/catalogo`. Algunas rutas de menú siguen pendientes en el catálogo.';

    $lines = [
        '---',
        'tipo: "manual_usuario"',
        'modulo: "' . $module . '"',
        'flujos: ' . count($flows),
        'estado_revision: "' . $estado . '"',
        '---',
        '',
        '# Manual De Usuario - ' . $module,
        '',
        $intro,
        '',
        '## Como Usar Este Manual',
        '',
        'Cada apartado describe una tarea de usuario. Las rutas Legacy/Pills2 vienen del catálogo (`## Ruta de menú`).',
        '',
    ];

    foreach ($flowBlocks as $block) {
        array_push($lines, ...$block);
    }

    $lines[] = '## Notas';
    $lines[] = '';
    if ($menuPending > 0) {
        $lines[] = '- Quedan ' . $menuPending . ' flujos sin ruta de menú documentada en el catálogo.';
    } else {
        $lines[] = '- Rutas de menú propagadas desde el catálogo; revisar en UI si alguna etiqueta de menú cambió.';
    }
    $lines[] = '- Permisos y errores se toman de las fichas API relacionadas.';
    $lines[] = '- Fuente: `docs/catalogo/' . $module . '/flujos/`.';
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
$capabilities = readCapabilities($module);
$apiByUrl = readApiByUrl($module);

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
    echo 'Capacidades leidas: ' . count($capabilities) . PHP_EOL;
    echo 'Endpoints API leidos: ' . count($apiByUrl) . PHP_EOL;
    exit(0);
}

ensureDirectory(dirname($target));
if (file_put_contents($target, renderManual($module, $flows, $screens, $capabilities, $apiByUrl)) === false) {
    fail('No se pudo escribir: ' . relativePath($target));
}

echo 'WRITE ' . relativePath($target) . PHP_EOL;
echo 'Flujos leidos: ' . count($flows) . PHP_EOL;
echo 'Pantallas leidas: ' . count($screens) . PHP_EOL;
echo 'Capacidades leidas: ' . count($capabilities) . PHP_EOL;
echo 'Endpoints API leidos: ' . count($apiByUrl) . PHP_EOL;

