#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Audita controladores frontend que aún no migraron la navegación post-FrontBootstrap.
 *
 * Detecta:
 *   - stack_after_recordar: goStack()/new Posicion tras recordar() (restaurar stack ANTES)
 *   - recordar_sin_post_limpio: recordar() sin replaceStackParametros vía ListNavSupport
 *   - sin_clear_stack: recordar() sin ListNavSupport::bootRecordar / clear_inherited (riesgo deleteFroward)
 *   - padre_antes_recordar: setParametros(..., 1) antes de recordar() (URL de pila incorrecta)
 *   - recordar_temprano_sin_restore: recordar() al inicio sin goStack previo pese a manejar stack
 *   - imprimir_sin_padre_limpio: vista con mostrar_back_arrow y padre sin persist de impresión
 *
 * Uso:
 *   php tools/audit/audit_posicion_nav_migration.php
 *   php tools/audit/audit_posicion_nav_migration.php --json
 *   php tools/audit/audit_posicion_nav_migration.php --path frontend/planning/controller
 *   php tools/audit/audit_posicion_nav_migration.php --only=stack_after_recordar
 *   php tools/audit/audit_posicion_nav_migration.php --strict   # exit 1 si hay hallazgos
 *
 * Referencia de migración: frontend/shared/helpers/ListNavSupport.php
 */

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "Solo CLI.\n");
    exit(1);
}

/** @var list<string> */
const CLEAN_STACK_HELPERS = [
    'ListNavSupport::persistCleanReturnToPosicion',
    'ListNavSupport::persistDossierReturnToPosicion',
    'ListNavSupport::persistActaNotasReturnToPosicion',
    'ListNavSupport::persistActaImprimirParentReturnToPosicion',
    'ListNavSupport::persistActaSelectReturnToPosicion',
    'ListNavSupport::persistTesseraImprimirParentReturnToPosicion',
    'ListNavSupport::persistTesseraReturnToPosicion',
    'ListNavSupport::persistCertificadoImprimirParentReturnToPosicion',
    'ListNavSupport::persistE43ParentReturnToPosicion',
    'ListNavSupport::persistPersonasSelectReturnToPosicion',
    'ListNavSupport::persistRecordarEntry',
    'replaceStackParametros',
];

/** Helpers que limpian `stack` heredado antes de recordar(). */
const CLEAR_STACK_BEFORE_RECORDAR_HELPERS = [
    'ListNavSupport::bootRecordar',
    'ListNavSupport::bootChildFromListRecordar',
    'ListNavSupport::bootActividadSelectChildRecordar',
    'ListNavSupport::bootDossiersFromActividadSelect',
    'ListNavSupport::clearInheritedStackForRecordar',
];

/** @var list<string> */
const STACK_RESTORE_HELPERS = [
    'ListNavSupport::restoreSelectionFromStackPost',
];

/** @var list<string> */
const PRINT_PARENT_HELPERS = [
    'ListNavSupport::persistActaImprimirParentReturnToPosicion',
    'ListNavSupport::persistTesseraImprimirParentReturnToPosicion',
    'ListNavSupport::persistCertificadoImprimirParentReturnToPosicion',
    'ListNavSupport::persistE43ParentReturnToPosicion',
];

/** Controladores ya revisados / excepciones documentadas. */
const KNOWN_OK_CONTROLLERS = [
    'frontend/dossiers/controller/dossiers_ver.php', // patrón canónico stack-before-recordar
    'frontend/notas/controller/acta_ver.php', // incluido desde acta_notas; boot condicional
    'frontend/encargossacd/controller/horario_sacd_ex_select.php', // include parcial
];

/** @return array{path: string, json: bool, strict: bool, only: string} */
function audit_parse_argv(array $argv): array
{
    $root = dirname(__DIR__, 2);
    $opts = [
        'path' => $root . '/frontend',
        'json' => false,
        'strict' => false,
        'only' => '',
    ];

    foreach (array_slice($argv, 1) as $arg) {
        if ($arg === '--json') {
            $opts['json'] = true;
            continue;
        }
        if ($arg === '--strict') {
            $opts['strict'] = true;
            continue;
        }
        if ($arg === '--help' || $arg === '-h') {
            fwrite(STDOUT, <<<'HELP'
Audita migración de navegación ($oPosicion / FrontBootstrap).

Uso: php tools/audit/audit_posicion_nav_migration.php [opciones]

Opciones:
  --path DIR     Directorio o fichero (default: frontend/)
  --only=Tipo    Filtra: stack_after_recordar | recordar_sin_post_limpio | sin_clear_stack
                 | padre_antes_recordar | imprimir_sin_padre_limpio | ok
  --json         Salida JSON
  --strict       Exit 1 si queda algún hallazgo (no --only=ok)
  --help         Esta ayuda

HELP);
            exit(0);
        }
        if (str_starts_with($arg, '--path=')) {
            $opts['path'] = substr($arg, strlen('--path='));
            continue;
        }
        if (str_starts_with($arg, '--only=')) {
            $opts['only'] = substr($arg, strlen('--only='));
            continue;
        }
        fwrite(STDERR, "Opción desconocida: {$arg}\n");
        exit(1);
    }

    return $opts;
}

/** @return list<string> */
function audit_collect_controllers(string $path): array
{
    $root = dirname(__DIR__, 2);
    $abs = str_starts_with($path, '/') ? $path : $root . '/' . ltrim($path, '/');

    if (is_file($abs)) {
        return [$abs];
    }

    if (!is_dir($abs)) {
        fwrite(STDERR, "Ruta no encontrada: {$abs}\n");
        exit(1);
    }

    $files = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($abs, FilesystemIterator::SKIP_DOTS),
    );

    foreach ($iterator as $fileInfo) {
        if (!$fileInfo->isFile() || $fileInfo->getExtension() !== 'php') {
            continue;
        }
        $pathname = $fileInfo->getPathname();
        if (!str_contains($pathname, '/controller/')) {
            continue;
        }
        $files[] = $pathname;
    }

    sort($files);

    return $files;
}

function audit_relative_path(string $absPath): string
{
    $root = dirname(__DIR__, 2) . '/';

    return str_starts_with($absPath, $root) ? substr($absPath, strlen($root)) : $absPath;
}

/** @return list<string> */
function audit_controller_views_with_back_arrow(string $relativeController, string $controllerContent): array
{
    $root = dirname(__DIR__, 2);
    $controllerBase = basename($relativeController, '.php');
    $base = preg_replace('#/controller/[^/]+\.php$#', '', $relativeController);
    if (!is_string($base)) {
        return [];
    }

    $matches = [];

    if (preg_match('/mostrar_(?:back_arrow|NavAtras)\s*\(/', $controllerContent) === 1) {
        $matches[] = $relativeController . ' (inline)';
    }

    $viewDir = $root . '/' . $base . '/view';
    foreach (['.phtml', '.html.twig'] as $ext) {
        $viewPath = $viewDir . '/' . $controllerBase . $ext;
        if (!is_file($viewPath)) {
            continue;
        }
        $content = file_get_contents($viewPath);
        if ($content !== false && preg_match('/mostrar_(?:back_arrow|NavAtras)\s*\(/', $content) === 1) {
            $matches[] = audit_relative_path($viewPath);
        }
    }

    return $matches;
}

/**
 * @return array{line: int, snippet: string}|null
 */
function audit_line_of(string $content, int $offset): int
{
    return substr_count(substr($content, 0, $offset), "\n") + 1;
}

function audit_first_line_matching(string $content, string $pattern): ?array
{
    if (preg_match($pattern, $content, $m, PREG_OFFSET_CAPTURE) !== 1) {
        return null;
    }
    $offset = $m[0][1];
    $line = audit_line_of($content, $offset);
    $snippet = trim($m[0][0]);

    return ['line' => $line, 'snippet' => $snippet];
}

function audit_uses_any(string $content, array $needles): bool
{
    foreach ($needles as $needle) {
        if (str_contains($content, $needle)) {
            return true;
        }
    }

    return false;
}

function audit_first_line_number(string $content, string $pattern): ?int
{
    $match = audit_first_line_matching($content, $pattern);

    return $match['line'] ?? null;
}

/**
 * @return list<int>
 */
function audit_all_line_numbers(string $content, string $pattern): array
{
    $lines = [];
    if (preg_match_all($pattern, $content, $m, PREG_OFFSET_CAPTURE) < 1) {
        return $lines;
    }
    foreach ($m[0] as $hit) {
        $lines[] = audit_line_of($content, $hit[1]);
    }

    return $lines;
}

function audit_has_clear_stack_before_recordar(string $content): bool
{
    return audit_uses_any($content, CLEAR_STACK_BEFORE_RECORDAR_HELPERS);
}

function audit_set_parametros_padre_before_recordar(string $content): bool
{
    $recordarLines = audit_all_line_numbers($content, '/->recordar\s*\(/');
    if ($recordarLines === []) {
        return false;
    }
    $firstRecordar = min($recordarLines);
    $setPadreLines = audit_all_line_numbers($content, '/->setParametros\s*\([^)]+,\s*1\s*\)/');
    foreach ($setPadreLines as $line) {
        if ($line < $firstRecordar) {
            return true;
        }
    }

    return false;
}

function audit_recordar_before_stack_restore(string $content): bool
{
    if (!str_contains($content, "['stack']") && !str_contains($content, '$_POST[\'stack\']')) {
        return false;
    }
    $recordarLine = audit_first_line_number($content, '/->recordar\s*\(/');
    if ($recordarLine === null) {
        return false;
    }
    if (audit_uses_any($content, STACK_RESTORE_HELPERS)) {
        return false;
    }
    $goStackLine = audit_first_line_number($content, '/->goStack\s*\(/');
    if ($goStackLine !== null && $goStackLine < $recordarLine) {
        return false;
    }
    if (preg_match(
        '/new\s+(?:frontend\\\\shared\\\\web\\\\)?Posicion\s*\(\s*\)[\s\S]{0,600}?->goStack\s*\(/',
        $content,
        $m,
        PREG_OFFSET_CAPTURE,
    ) === 1) {
        $offset = $m[0][1];
        if (audit_line_of($content, $offset) < $recordarLine) {
            return false;
        }
    }

    return $recordarLine <= 35;
}

/**
 * @return list<string>
 */
function audit_analyze_controller(string $absPath): array
{
    $relative = audit_relative_path($absPath);
    if (in_array($relative, KNOWN_OK_CONTROLLERS, true)) {
        return ['ok'];
    }

    $content = file_get_contents($absPath);
    if ($content === false) {
        return ['unreadable'];
    }

    $issues = [];

    $hasRecordar = str_contains($content, '->recordar(') || str_contains($content, '->recordar();');
    $hasBootstrap = str_contains($content, 'FrontBootstrap::boot()');

    if (!$hasBootstrap || !$hasRecordar) {
        return $hasRecordar ? ['ok'] : ['ok'];
    }

    $recordarPos = audit_first_line_matching($content, '/->recordar\s*\(/');
    $goStackPos = audit_first_line_matching($content, '/->goStack\s*\(/');
    $newPosicionGoStack = preg_match(
        '/new\s+(?:frontend\\\\shared\\\\web\\\\)?Posicion\s*\(\s*\)[\s\S]{0,400}?->goStack\s*\(/',
        $content,
        $m,
        PREG_OFFSET_CAPTURE,
    ) === 1;

    $usesStackRestoreHelper = audit_uses_any($content, STACK_RESTORE_HELPERS);
    $usesCleanStack = audit_uses_any($content, CLEAN_STACK_HELPERS);

    $mentionsStackPost = str_contains($content, "['stack']") || str_contains($content, '$_POST[\'stack\']');

    if ($mentionsStackPost && !$usesStackRestoreHelper) {
        $stackHandlingAfterRecordar = false;
        if ($goStackPos !== null && $recordarPos !== null && $goStackPos['line'] > $recordarPos['line']) {
            $stackHandlingAfterRecordar = true;
        }
        if ($newPosicionGoStack && $recordarPos !== null) {
            $newPosicionOffset = null;
            if (preg_match(
                '/new\s+(?:frontend\\\\shared\\\\web\\\\)?Posicion\s*\(\s*\)[\s\S]{0,400}?->goStack\s*\(/',
                $content,
                $m,
                PREG_OFFSET_CAPTURE,
            ) === 1) {
                $newPosicionOffset = $m[0][1];
            }
            if ($newPosicionOffset !== null && audit_line_of($content, $newPosicionOffset) > $recordarPos['line']) {
                $stackHandlingAfterRecordar = true;
            }
        }
        if ($stackHandlingAfterRecordar) {
            $issues[] = 'stack_after_recordar';
        }
    }

    if (!$usesCleanStack) {
        $issues[] = 'recordar_sin_post_limpio';
    }

    if (!audit_has_clear_stack_before_recordar($content)) {
        $issues[] = 'sin_clear_stack';
    }

    if (audit_set_parametros_padre_before_recordar($content)) {
        $issues[] = 'padre_antes_recordar';
    }

    if (audit_recordar_before_stack_restore($content)) {
        $issues[] = 'recordar_temprano_sin_restore';
    }

    $backArrowViews = audit_controller_views_with_back_arrow($relative, $content);
    if ($backArrowViews !== [] && !audit_uses_any($content, PRINT_PARENT_HELPERS) && !audit_uses_any($content, [
        'ListNavSupport::persistTesseraReturnToPosicion',
        'ListNavSupport::persistActaNotasReturnToPosicion',
        'ListNavSupport::persistActaSelectReturnToPosicion',
        'ListNavSupport::persistPersonasSelectReturnToPosicion',
        'ListNavSupport::persistCleanReturnToPosicion',
    ])) {
        $issues[] = 'imprimir_sin_padre_limpio';
    }

    if ($issues === []) {
        return ['ok'];
    }

    return $issues;
}

/** @param array<string, list<array{file: string, issues: list<string>}>> $grouped */
function audit_unique_issue_files(array $grouped): int
{
    $files = [];
    foreach ($grouped as $category => $entries) {
        if ($category === 'ok' || $entries === []) {
            continue;
        }
        foreach ($entries as $entry) {
            $files[$entry['file']] = true;
        }
    }

    return count($files);
}

/** @param array<string, list<array{file: string, issues: list<string>}>> $grouped */
function audit_print_report(array $grouped): void
{
    $order = [
        'stack_after_recordar',
        'recordar_temprano_sin_restore',
        'padre_antes_recordar',
        'sin_clear_stack',
        'recordar_sin_post_limpio',
        'imprimir_sin_padre_limpio',
        'ok',
        'unreadable',
    ];
    $totalIssues = 0;

    foreach ($order as $category) {
        if (!isset($grouped[$category]) || $category === 'ok') {
            continue;
        }
        $entries = $grouped[$category];
        if ($entries === []) {
            continue;
        }
        $totalIssues += count($entries);
        fwrite(STDOUT, "\n=== {$category} (" . count($entries) . ") ===\n");
        foreach ($entries as $entry) {
            fwrite(STDOUT, '  - ' . $entry['file'] . "\n");
            if (isset($entry['all_issues']) && count($entry['all_issues']) > 1) {
                fwrite(STDOUT, '      también: ' . implode(', ', array_diff($entry['all_issues'], [$category])) . "\n");
            }
        }
    }

    $okCount = isset($grouped['ok']) ? count($grouped['ok']) : 0;
    $uniqueIssues = audit_unique_issue_files($grouped);
    fwrite(STDOUT, "\n--- Resumen ---\n");
    fwrite(STDOUT, "OK / sin recordar: {$okCount}\n");
    fwrite(STDOUT, "Ficheros con hallazgos (únicos): {$uniqueIssues}\n");
    fwrite(STDOUT, "Entradas por categoría (puede repetir fichero): {$totalIssues}\n");
    fwrite(STDOUT, "\nMigración: ver docs/dev/posicion_nav_post_frontbootstrap.md\n");
    fwrite(STDOUT, "  - ListNavSupport::bootRecordar() antes de recordar()\n");
    fwrite(STDOUT, "  - restaurar stack ANTES de recordar(); persist POST limpio DESPUÉS\n");
    fwrite(STDOUT, "  - setParametros(...,1) solo DESPUÉS de recordar()\n");
    fwrite(STDOUT, "Helpers: frontend/shared/helpers/ListNavSupport.php\n");
}

$opts = audit_parse_argv($argv);
$files = audit_collect_controllers($opts['path']);

/** @var array<string, list<array{file: string, issues: list<string>, all_issues?: list<string>}>> $grouped */
$grouped = [];

foreach ($files as $absPath) {
    $relative = audit_relative_path($absPath);
    $issues = audit_analyze_controller($absPath);

    foreach ($issues as $issue) {
        if ($opts['only'] !== '' && $opts['only'] !== $issue) {
            continue;
        }
        $grouped[$issue][] = [
            'file' => $relative,
            'issues' => $issues,
            'all_issues' => $issues,
        ];
    }
}

if ($opts['json']) {
    $payload = [];
    foreach ($grouped as $category => $entries) {
        $payload[$category] = array_map(
            static fn (array $e): string => $e['file'],
            $entries,
        );
    }
    echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
} else {
    audit_print_report($grouped);
}

$hasFindings = false;
foreach ($grouped as $category => $entries) {
    if ($category === 'ok' || $entries === []) {
        continue;
    }
    if ($opts['only'] !== '' && $opts['only'] !== $category) {
        continue;
    }
    $hasFindings = true;
    break;
}

if ($opts['strict'] && $hasFindings) {
    exit(1);
}

exit(0);
