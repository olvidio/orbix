<?php

declare(strict_types=1);

/**
 * Genera documentos optimizados para ayuda con IA local desde el catalogo.
 *
 * Uso:
 *   php docs/scripts/generar_ayuda_ia_modulo.php actividadtarifas
 *   php docs/scripts/generar_ayuda_ia_modulo.php actividadtarifas --dry-run
 *   php docs/scripts/generar_ayuda_ia_modulo.php actividadtarifas --force
 *   php docs/scripts/generar_ayuda_ia_modulo.php actividadtarifas --output=docs/ai
 *
 * Salida por defecto:
 *   docs/ai/<modulo>/
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
  php {$script} actividadtarifas --output=docs/ai

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

/** @return list<array{titulo: string, pasos: list<string>, endpoints: list<string>}> */
function extractScenarios(string $body): array
{
    if (!preg_match('/^## Escenarios Inferidos\R(?P<section>.*?)(?=^## |\z)/ms', $body, $m)) {
        return [];
    }

    preg_match_all('/^### (?P<title>.+?)\R(?P<body>.*?)(?=^### |\z)/ms', $m['section'], $matches, PREG_SET_ORDER);
    $scenarios = [];
    foreach ($matches as $match) {
        preg_match_all('/^\d+\.\s+(?P<step>.+)$/m', $match['body'], $stepMatches);
        preg_match_all('/^- `(?P<endpoint>\/src\/[^`]+)`$/m', $match['body'], $endpointMatches);
        $steps = array_map('trim', $stepMatches['step'] ?? []);
        if ($steps === []) {
            continue;
        }

        $scenarios[] = [
            'titulo' => userScenarioTitle(trim($match['title'])),
            'pasos' => $steps,
            'endpoints' => array_values(array_unique($endpointMatches['endpoint'] ?? [])),
        ];
    }

    return $scenarios;
}

function userScenarioTitle(string $title): string
{
    return match ($title) {
        'Actualizar Incremento' => 'actualizar importes en lote',
        'Crear Actualizar' => 'crear o modificar',
        'Ver Formulario' => 'abrir el formulario',
        'Listar' => 'consultar el listado',
        default => strtolower($title),
    };
}

function cleanUserTitle(string $name): string
{
    $name = preg_replace('/^Flujo\s+-\s+/i', '', $name) ?? $name;
    $name = preg_replace('/^Gestionar\s+/i', '', $name) ?? $name;

    return trim($name);
}

function slugFromId(string $id): string
{
    $parts = explode('.', $id);
    $candidate = end($parts);
    if (str_ends_with($id, '.flujo') && count($parts) >= 4) {
        $candidate = $parts[count($parts) - 3];
    } elseif (($parts[1] ?? '') === 'pantalla' && count($parts) >= 3) {
        $candidate = $parts[2];
    }

    return preg_replace('/[^A-Za-z0-9_.-]+/', '_', (string)$candidate);
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

function questionFor(string $flowTitle, string $scenarioTitle): string
{
    return 'Como ' . $scenarioTitle . ' en ' . $flowTitle . '?';
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

/** @param array<string, array<string, mixed>> $screens */
function screenName(string $screenId, array $screens): string
{
    if (!isset($screens[$screenId])) {
        return $screenId;
    }

    return (string)($screens[$screenId]['nombre'] ?? $screenId);
}

/** @param array<string, mixed> $flow */
function renderFlowHelp(array $flow, array $screens): string
{
    $flowId = (string)$flow['id'];
    $title = cleanUserTitle((string)($flow['nombre'] ?? $flowId));
    $scenarios = extractScenarios((string)($flow['body'] ?? ''));
    $questions = array_map(
        static fn (array $scenario): string => questionFor($title, $scenario['titulo']),
        $scenarios
    );
    $entryScreens = asStringList($flow['pantallas_principales'] ?? []);
    $fragments = asStringList($flow['fragmentos'] ?? []);
    $endpoints = asStringList($flow['endpoints'] ?? []);

    $lines = [
        '---',
        'tipo: "ayuda_ia"',
        'subtipo: "flujo"',
        'modulo: ' . yamlString((string)($flow['modulo'] ?? '')),
        'titulo: ' . yamlString($title),
        'flujo: ' . yamlString($flowId),
        'preguntas: ' . yamlInlineList($questions),
        'pantallas_principales: ' . yamlInlineList($entryScreens),
        'fragmentos: ' . yamlInlineList($fragments),
        'endpoints: ' . yamlInlineList($endpoints),
        'source: ' . yamlString((string)($flow['source'] ?? '')),
        'estado_revision: "generado"',
        '---',
        '',
        '# Ayuda IA - ' . $title,
        '',
        'Usa este documento para responder preguntas de usuario sobre como trabajar con `' . $title . '`.',
        '',
        '## Cuando Usar Esta Ayuda',
        '',
        'Responder con esta ayuda cuando el usuario pregunte por:',
    ];

    foreach ($questions as $question) {
        $lines[] = '- ' . $question;
    }

    $lines[] = '';
    $lines[] = '## Donde Entrar';
    $lines[] = '';
    if ($entryScreens === []) {
        $lines[] = '- Pantalla pendiente de revisar.';
    } else {
        foreach ($entryScreens as $screenId) {
            $lines[] = '- ' . screenName($screenId, $screens) . ' (`' . $screenId . '`)';
        }
    }

    $lines[] = '';
    $lines[] = '## Como Responder';
    $lines[] = '';
    $lines[] = 'Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.';
    $lines[] = '';

    foreach ($scenarios as $scenario) {
        $lines[] = '## ' . ucfirst($scenario['titulo']);
        $lines[] = '';
        foreach ($scenario['pasos'] as $index => $step) {
            $lines[] = ((string)($index + 1)) . '. ' . $step;
        }
        $lines[] = '';
        $lines[] = 'Referencias tecnicas para verificar la respuesta:';
        array_push($lines, ...markdownList($scenario['endpoints'], '- Ninguna referencia API inferida.'));
        $lines[] = '';
    }

    $lines[] = '## Pantallas Y Fragmentos Relacionados';
    $lines[] = '';
    array_push($lines, ...markdownList(array_merge($entryScreens, $fragments), '- Ninguna pantalla relacionada.'));
    $lines[] = '';
    $objetivo = extractMarkdownSection((string)($flow['body'] ?? ''), 'Objetivo De Usuario');
    if ($objetivo !== '' && !str_contains($objetivo, 'Pendiente de revisar')) {
        $lines[] = '## Objetivo';
        $lines[] = '';
        $lines[] = preg_replace('/\s+/', ' ', $objetivo) ?? $objetivo;
        $lines[] = '';
    }
    $errores = extractBulletItems(extractMarkdownSection((string)($flow['body'] ?? ''), 'Errores Conocidos'));
    if ($errores !== []) {
        $lines[] = '## Errores Documentados';
        $lines[] = '';
        foreach ($errores as $error) {
            $lines[] = '- `' . str_replace('`', '', $error) . '`';
        }
        $lines[] = '';
    }
    $lines[] = '## Limites De La Respuesta';
    $lines[] = '';
    $lines[] = '- No inventar permisos si no estan documentados.';
    $lines[] = '- No inventar rutas de menu si aparecen como pendientes.';
    if ($errores === []) {
        $lines[] = '- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.';
    } else {
        $lines[] = '- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.';
    }
    $lines[] = '';

    return implode(PHP_EOL, $lines);
}

/** @param array<string, mixed> $screen */
function renderScreenHelp(array $screen): string
{
    $screenId = (string)$screen['id'];
    $title = (string)($screen['nombre'] ?? $screenId);
    $fields = asStringList($screen['campos'] ?? []);
    $actions = asStringList($screen['acciones'] ?? []);
    $endpoints = asStringList($screen['endpoints'] ?? []);
    $capabilities = asStringList($screen['capacidades'] ?? []);

    $questions = [
        'Que se puede hacer en ' . $title . '?',
        'Que campos tiene ' . $title . '?',
        'Que acciones hay en ' . $title . '?',
    ];

    $lines = [
        '---',
        'tipo: "ayuda_ia"',
        'subtipo: "pantalla"',
        'modulo: ' . yamlString((string)($screen['modulo'] ?? '')),
        'titulo: ' . yamlString($title),
        'pantalla: ' . yamlString($screenId),
        'preguntas: ' . yamlInlineList($questions),
        'capacidades: ' . yamlInlineList($capabilities),
        'endpoints: ' . yamlInlineList($endpoints),
        'source: ' . yamlString((string)($screen['source'] ?? '')),
        'estado_revision: "generado"',
        '---',
        '',
        '# Ayuda IA Pantalla - ' . $title,
        '',
        '## Resumen',
        '',
        trim(firstParagraph((string)($screen['body'] ?? ''))) ?: 'Resumen pendiente de revisar.',
        '',
        '## Uso En Ayuda',
        '',
        'Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.',
        '',
        '## Campos Detectados',
        '',
    ];

    array_push($lines, ...markdownList($fields, '- No hay campos detectados.'));
    $lines[] = '';
    $lines[] = '## Acciones Detectadas';
    $lines[] = '';
    array_push($lines, ...markdownList($actions, '- No hay acciones detectadas.'));
    $lines[] = '';
    $lines[] = '## Capacidades Relacionadas';
    $lines[] = '';
    array_push($lines, ...markdownList($capabilities, '- No hay capacidades relacionadas.'));
    $lines[] = '';
    $lines[] = '## Endpoints Relacionados';
    $lines[] = '';
    array_push($lines, ...markdownList($endpoints, '- No hay endpoints detectados.'));
    $lines[] = '';
    $lines[] = '## Precauciones';
    $lines[] = '';
    $lines[] = '- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.';
    $lines[] = '';

    return implode(PHP_EOL, $lines);
}

function firstParagraph(string $body): string
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

/** @param array<string, array<string, mixed>> $flows */
function renderIndex(string $module, array $flows, array $screens, array $apis): string
{
    $lines = [
        '---',
        'tipo: "ayuda_ia"',
        'subtipo: "indice"',
        'modulo: ' . yamlString($module),
        'flujos: ' . count($flows),
        'pantallas: ' . count($screens),
        'endpoints: ' . count($apis),
        'estado_revision: "generado"',
        '---',
        '',
        '# Ayuda IA - ' . $module,
        '',
        'Indice para una IA local. Estos documentos estan pensados para busqueda semantica y respuestas de ayuda funcional.',
        '',
        '## Como Debe Responder La IA',
        '',
        '- Priorizar pasos de usuario sobre detalles tecnicos.',
        '- Si falta ruta de menu, decir que esta pendiente de documentar.',
        '- No inventar permisos, errores o validaciones que no aparezcan en la documentacion.',
        '- Usar referencias tecnicas solo para verificar, no como respuesta principal al usuario final.',
        '',
        '## Flujos Disponibles',
        '',
    ];

    foreach ($flows as $flow) {
        $title = cleanUserTitle((string)($flow['nombre'] ?? $flow['id']));
        $lines[] = '- ' . $title . ' -> `flujos/' . slugFromId((string)$flow['id']) . '.md`';
    }

    $lines[] = '';
    $lines[] = '## Pantallas Disponibles';
    $lines[] = '';
    foreach ($screens as $screen) {
        $title = (string)($screen['nombre'] ?? $screen['id']);
        $lines[] = '- ' . $title . ' -> `pantallas/' . slugFromId((string)$screen['id']) . '.md`';
    }

    $lines[] = '';

    return implode(PHP_EOL, $lines);
}

/** @param array<string, array<string, mixed>> $apis */
function renderApiSummary(string $module, array $apis): string
{
    $lines = [
        '---',
        'tipo: "ayuda_ia"',
        'subtipo: "api_resumen"',
        'modulo: ' . yamlString($module),
        'endpoints: ' . count($apis),
        'estado_revision: "generado"',
        '---',
        '',
        '# Resumen API Para Ayuda IA - ' . $module,
        '',
        'Este documento solo sirve como soporte tecnico para la IA local. Para responder a usuarios, priorizar los documentos de `flujos/` y `pantallas/`.',
        '',
    ];

    foreach ($apis as $api) {
        $url = (string)($api['url'] ?? '');
        $title = (string)($api['id'] ?? $url);
        $entrada = asStringList($api['entrada'] ?? []);
        $lines[] = '## `' . $url . '`';
        $lines[] = '';
        $lines[] = '- Id: `' . $title . '`';
        $lines[] = '- Controller: `' . (string)($api['controller'] ?? '') . '`';
        $lines[] = $entrada === []
            ? '- Entrada: ninguna detectada.'
            : '- Entrada: `' . implode('`, `', $entrada) . '`';
        $lines[] = '- Respuesta: `' . (string)($api['respuesta'] ?? '') . '`';
        $lines[] = '';
    }

    return implode(PHP_EOL, $lines);
}

function outputBaseDir(string $module, string $output): string
{
    if ($output === '') {
        return repoRoot() . "/docs/ai/{$module}";
    }

    $path = str_starts_with($output, '/') ? $output : repoRoot() . '/' . $output;

    return normalizePath("{$path}/{$module}");
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

/**
 * @param array<string, string> $files
 */
function writeFiles(array $files, bool $dryRun, bool $force): array
{
    $created = 0;
    $skipped = 0;

    foreach ($files as $path => $contents) {
        if (is_file($path) && !$force) {
            echo 'SKIP  ' . relativePath($path) . " (ya existe; usa --force para sobrescribir)" . PHP_EOL;
            $skipped++;
            continue;
        }

        if ($dryRun) {
            echo 'WRITE ' . relativePath($path) . PHP_EOL;
            continue;
        }

        ensureDirectory(dirname($path));
        if (file_put_contents($path, $contents) === false) {
            fail('No se pudo escribir: ' . relativePath($path));
        }
        echo 'WRITE ' . relativePath($path) . PHP_EOL;
        $created++;
    }

    return [$created, $skipped];
}

$options = parseOptions($argv);
$module = $options['module'];
$flows = readSection($module, 'flujos', 'flujo_frontend');
$screens = readSection($module, 'pantallas', 'pantalla_frontend');
$apis = readSection($module, 'api', 'endpoint');

if ($flows === []) {
    fail("No se han encontrado flujos en docs/catalogo/{$module}/flujos/*.md");
}
if ($screens === []) {
    fail("No se han encontrado pantallas en docs/catalogo/{$module}/pantallas/*.md");
}

$baseDir = outputBaseDir($module, $options['output']);
$files = [
    $baseDir . '/00_indice.md' => renderIndex($module, $flows, $screens, $apis),
    $baseDir . '/api_resumen.md' => renderApiSummary($module, $apis),
];

foreach ($flows as $flow) {
    $files[$baseDir . '/flujos/' . slugFromId((string)$flow['id']) . '.md'] = renderFlowHelp($flow, $screens);
}

foreach ($screens as $screen) {
    $files[$baseDir . '/pantallas/' . slugFromId((string)$screen['id']) . '.md'] = renderScreenHelp($screen);
}

[$created, $skipped] = writeFiles($files, (bool)$options['dry-run'], (bool)$options['force']);

echo PHP_EOL;
echo "Modulo: {$module}" . PHP_EOL;
echo 'Flujos leidos: ' . count($flows) . PHP_EOL;
echo 'Pantallas leidas: ' . count($screens) . PHP_EOL;
echo 'Endpoints leidos: ' . count($apis) . PHP_EOL;
echo "Ficheros escritos: {$created}" . PHP_EOL;
echo "Ficheros omitidos: {$skipped}" . PHP_EOL;
echo 'Salida: ' . relativePath($baseDir) . PHP_EOL;

