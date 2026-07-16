#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Inserta ListNavSupport::persistRecordarEntry() tras recordar() en controladores del audit.
 *
 * Uso:
 *   php tools/fix/fix_recordar_sin_post_limpio.php              # dry-run
 *   php tools/fix/fix_recordar_sin_post_limpio.php --apply
 *   php tools/fix/fix_recordar_sin_post_limpio.php --apply --file frontend/usuarios/controller/usuario_lista.php
 */

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "Solo CLI.\n");
    exit(1);
}

$apply = in_array('--apply', $argv, true);
$singleFile = null;
foreach ($argv as $arg) {
    if (str_starts_with($arg, '--file=')) {
        $singleFile = substr($arg, strlen('--file='));
    }
}

$root = dirname(__DIR__, 2);

const CLEAN_MARKERS = [
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

/** @return list<string> */
function fix_rec_target_files(string $root, ?string $singleFile): array
{
    if ($singleFile !== null) {
        $abs = str_starts_with($singleFile, '/') ? $singleFile : $root . '/' . ltrim($singleFile, '/');

        return is_file($abs) ? [$abs] : [];
    }

    exec('php ' . escapeshellarg($root . '/tools/audit/audit_posicion_nav_migration.php') . ' --only=recordar_sin_post_limpio --json 2>/dev/null', $jsonOut);
    $decoded = json_decode(implode("\n", $jsonOut), true);
    $files = [];
    if (is_array($decoded['recordar_sin_post_limpio'] ?? null)) {
        foreach ($decoded['recordar_sin_post_limpio'] as $rel) {
            $files[] = $root . '/' . $rel;
        }
    }

    return $files;
}

function fix_rec_has_clean_helper(string $content): bool
{
    foreach (CLEAN_MARKERS as $marker) {
        if (str_contains($content, $marker)) {
            return true;
        }
    }

    return false;
}

function fix_rec_ensure_use(string $content): string
{
    $useLine = 'use frontend\\shared\\helpers\\ListNavSupport;';
    if (str_contains($content, $useLine) || !str_contains($content, 'ListNavSupport::')) {
        return $content;
    }
    if (preg_match('/^<\?php\s*\n((?:use [^\n]+\n)+)/m', $content, $m)) {
        return preg_replace('/^(<\?php\s*\n(?:use [^\n]+\n)+)/m', '$1' . $useLine . "\n", $content, 1);
    }

    return preg_replace('/^<\?php\s*\n/m', "<?php\n\n$useLine\n", $content, 1);
}

function fix_rec_has_safe_qid_sel_init(string $content): bool
{
    return preg_match('/\$Qid_sel\s*=\s*ListNavSupport::idSelFromPost\s*\(/', $content) === 1
        || preg_match('/ListNavSupport::restoreSelectionFromStackPost\s*\(/', $content) === 1
        || preg_match('/\$Qid_sel\s*=\s*!\s*ListNavSupport::idSelIsEmpty/', $content) === 1;
}

/** @return list<string> */
function fix_rec_restore_init_lines(string $indent): array
{
    return [
        $indent . '$restored = ListNavSupport::restoreSelectionFromStackPost();',
        '',
        $indent . '/** @var string|list<string> $Qid_sel */',
        $indent . '$Qid_sel = !ListNavSupport::idSelIsEmpty($restored[\'id_sel\']) ? $restored[\'id_sel\'] : ListNavSupport::idSelFromPost();',
        $indent . '$Qscroll_id = $restored[\'scroll_id\'] !== \'\' ? $restored[\'scroll_id\'] : ListNavSupport::scrollIdFromPost();',
    ];
}

function fix_rec_id_sel_expr(string $content): string
{
    if (preg_match('/\$id_sel\b/', $content) && !preg_match('/\$Qid_sel\b/', $content)) {
        return '$id_sel';
    }
    if (fix_rec_has_safe_qid_sel_init($content)) {
        return '$Qid_sel';
    }

    return 'ListNavSupport::idSelFromPost()';
}

function fix_rec_scroll_expr(string $content): string
{
    if (preg_match('/\$scroll_id\b/', $content) && !preg_match('/\$Qscroll_id\b/', $content)) {
        return 'isset($scroll_id) ? (string) $scroll_id : \'\'';
    }
    if (fix_rec_has_safe_qid_sel_init($content) && preg_match('/\$Qscroll_id\b/', $content)) {
        return '$Qscroll_id';
    }

    return 'ListNavSupport::scrollIdFromPost()';
}

/**
 * @return array{line: int, arg: string}|null
 */
function fix_rec_set_parametros_after_recordar(string $content, int $recordarLine, array $lines): ?array
{
    $start = 0;
    for ($i = 0; $i <= $recordarLine; $i++) {
        $start += strlen($lines[$i]) + 1;
    }
    $tail = substr($content, $start, 4000);
    if (!preg_match('/\$oPosicion->setParametros\s*\(\s*(\[[\s\S]*?\]|\$\w+)\s*,\s*1\s*\)/', $tail, $m, PREG_OFFSET_CAPTURE)) {
        return null;
    }
    $line = $recordarLine + 1 + substr_count(substr($tail, 0, $m[0][1]), "\n");

    return ['line' => $line, 'arg' => trim($m[1][0])];
}

function fix_rec_build_insert(string $content, int $recordarLine, array $lines): ?array
{
    $idSel = fix_rec_id_sel_expr($content);
    $scroll = fix_rec_scroll_expr($content);
    $mergeArgs = "ListNavSupport::mergeSelectionIntoReturnParametros(%s, {$idSel}, {$scroll})";

    if (preg_match('/\$aGoBack\s*=\s*\[/', $content)) {
        $base = '($aGoBack ?? ListNavSupport::buildReturnParametrosFromPost())';

        return [
            'insert' => 'ListNavSupport::persistRecordarEntry($oPosicion, ' . sprintf($mergeArgs, $base) . ');',
            'after_line' => $recordarLine,
        ];
    }

    $setParam = fix_rec_set_parametros_after_recordar($content, $recordarLine, $lines);
    if ($setParam !== null) {
        return [
            'insert' => 'ListNavSupport::persistRecordarEntry($oPosicion, ' . sprintf($mergeArgs, $setParam['arg']) . ');',
            'after_line' => $setParam['line'],
        ];
    }

    $recordarOffset = 0;
    for ($i = 0; $i <= $recordarLine; $i++) {
        $recordarOffset += strlen($lines[$i]) + 1;
    }
    if (str_contains(substr($content, 0, $recordarOffset), 'goStack')) {
        return [
            'insert' => 'ListNavSupport::persistRecordarEntry($oPosicion, ' . sprintf($mergeArgs, 'ListNavSupport::buildReturnParametrosFromPost()') . ');',
            'after_line' => $recordarLine,
        ];
    }

    return [
        'insert' => 'ListNavSupport::persistRecordarEntry($oPosicion, ListNavSupport::buildReturnParametrosFromPost());',
        'after_line' => $recordarLine,
    ];
}

function fix_rec_file(string $content, string $absPath, string $root): ?string
{
    if (!str_contains($content, '->recordar(') || fix_rec_has_clean_helper($content)) {
        return null;
    }

    $lines = preg_split('/\R/', $content);
    if (!is_array($lines)) {
        return null;
    }

    $recordarLine = null;
    foreach ($lines as $i => $line) {
        if (preg_match('/\$oPosicion->recordar\s*\(/', $line)) {
            $recordarLine = $i;
            break;
        }
    }
    if ($recordarLine === null) {
        return null;
    }

    $built = fix_rec_build_insert($content, $recordarLine, $lines);
    if ($built === null) {
        return null;
    }
    $insert = $built['insert'];
    $insertAfterLine = $built['after_line'];

    $indent = '';
    if (preg_match('/^(\s*)/', $lines[$insertAfterLine], $m)) {
        $indent = $m[1];
    }

    $insertLines = [$indent . $insert, ''];
    if (preg_match('/\$Qid_sel\b/', $content) && !fix_rec_has_safe_qid_sel_init($content)) {
        array_splice($lines, $recordarLine, 0, [...fix_rec_restore_init_lines($indent), '']);
        if ($insertAfterLine >= $recordarLine) {
            $insertAfterLine += count(fix_rec_restore_init_lines($indent)) + 1;
        }
    }

    // No duplicar si ya está justo después
    $next = $lines[$insertAfterLine + 1] ?? '';
    if (str_contains($next, 'ListNavSupport::persistRecordarEntry') || str_contains($next, 'ListNavSupport::persistCleanReturnToPosicion')) {
        return null;
    }

    array_splice($lines, $insertAfterLine + 1, 0, $insertLines);

    $fixed = fix_rec_ensure_use(implode("\n", $lines));

    return $fixed === $content ? null : $fixed;
}

$files = fix_rec_target_files($root, $singleFile);
$changed = 0;
$skipped = 0;

foreach ($files as $abs) {
    $content = file_get_contents($abs);
    if ($content === false) {
        continue;
    }
    $fixed = fix_rec_file($content, $abs, $root);
    if ($fixed === null) {
        $skipped++;
        continue;
    }
    $rel = str_replace($root . '/', '', $abs);
    if ($apply) {
        file_put_contents($abs, $fixed);
        fwrite(STDOUT, "fixed: {$rel}\n");
    } else {
        fwrite(STDOUT, "would fix: {$rel}\n");
    }
    $changed++;
}

fwrite(STDOUT, ($apply ? 'Fixed' : 'Would fix') . ": {$changed}, skipped: {$skipped}\n");
exit(0);
