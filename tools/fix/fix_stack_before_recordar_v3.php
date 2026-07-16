#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Reordena stack / recordar(): restauración de stack ANTES de recordar(),
 * o recordar() después del bloque stack si hay lecturas POST entre medias.
 *
 * Uso:
 *   php tools/fix/fix_stack_before_recordar_v3.php [--apply] [--file=PATH]
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

/** @return list<string> */
function v3_target_files(string $root, ?string $singleFile): array
{
    if ($singleFile !== null) {
        $abs = str_starts_with($singleFile, '/') ? $singleFile : $root . '/' . ltrim($singleFile, '/');

        return is_file($abs) ? [$abs] : [];
    }

    exec('php ' . escapeshellarg($root . '/tools/audit/audit_posicion_nav_migration.php') . ' --only=stack_after_recordar --json 2>/dev/null', $jsonOut);
    $decoded = json_decode(implode("\n", $jsonOut), true);
    $files = [];
    if (is_array($decoded['stack_after_recordar'] ?? null)) {
        foreach ($decoded['stack_after_recordar'] as $rel) {
            $files[] = $root . '/' . $rel;
        }
    }

    return $files;
}

function v3_is_significant_line(string $line): bool
{
    $t = trim($line);
    if ($t === '') {
        return false;
    }
    if (str_starts_with($t, '//') || str_starts_with($t, '*') || str_starts_with($t, '/**') || str_starts_with($t, '/*')) {
        return false;
    }

    return true;
}

/**
 * @return array{start: int, end: int}|null inclusive line indices
 */
function v3_find_stack_block_lines(array $lines, int $fromLine = 0): ?array
{
    $count = count($lines);
    for ($i = $fromLine; $i < $count; $i++) {
        if (!preg_match('/if\s*\(\s*isset\s*\(\s*\$_POST\s*\[\s*[\'"]stack[\'"]\s*\]\s*\)\s*\)/', $lines[$i])) {
            continue;
        }
        $depth = 0;
        $started = false;
        for ($j = $i; $j < $count; $j++) {
            if (str_contains($lines[$j], '{')) {
                $started = true;
            }
            $depth += substr_count($lines[$j], '{');
            $depth -= substr_count($lines[$j], '}');
            if ($started && $depth === 0) {
                return ['start' => $i, 'end' => $j];
            }
        }

        return null;
    }

    return null;
}

/** @return int|null */
function v3_find_recordar_line(array $lines, int $fromLine = 0): ?int
{
    $count = count($lines);
    for ($i = $fromLine; $i < $count; $i++) {
        if (preg_match('/\$oPosicion->recordar\s*\(/', $lines[$i])) {
            return $i;
        }
    }

    return null;
}

function v3_has_significant_between(array $lines, int $fromExclusive, int $toExclusive): bool
{
    for ($i = $fromExclusive + 1; $i < $toExclusive; $i++) {
        if (isset($lines[$i]) && v3_is_significant_line($lines[$i])) {
            return true;
        }
    }

    return false;
}

/**
 * @param list<string> $lines
 * @return list<string>
 */
function v3_splice(array $lines, int $start, int $length, array $insert): array
{
    return array_merge(
        array_slice($lines, 0, $start),
        $insert,
        array_slice($lines, $start + $length),
    );
}

function v3_fix_content(string $content): ?string
{
    if (!str_contains($content, '->recordar(') || !str_contains($content, 'goStack')) {
        return null;
    }

    $lines = preg_split('/\R/', $content);
    if (!is_array($lines)) {
        return null;
    }

    $recordarLine = v3_find_recordar_line($lines);
    if ($recordarLine === null) {
        return null;
    }

    $stackBlock = v3_find_stack_block_lines($lines, $recordarLine + 1);
    if ($stackBlock === null) {
        return null;
    }

    $blockLines = array_slice($lines, $stackBlock['start'], $stackBlock['end'] - $stackBlock['start'] + 1);
    $blockLen = $stackBlock['end'] - $stackBlock['start'] + 1;

    if (!v3_has_significant_between($lines, $recordarLine, $stackBlock['start'])) {
        // stack inmediatamente (salvo comentarios) tras recordar → stack antes de recordar
        $withoutBlock = v3_splice($lines, $stackBlock['start'], $blockLen, []);
        $recordarLine2 = v3_find_recordar_line($withoutBlock);
        if ($recordarLine2 === null) {
            return null;
        }
        $result = v3_splice($withoutBlock, $recordarLine2, 0, $blockLines);
    } else {
        // POST u otra lógica entre recordar y stack → recordar después del stack
        $recordarContent = $lines[$recordarLine];
        $withoutRecordar = v3_splice($lines, $recordarLine, 1, []);
        $stackBlock2 = v3_find_stack_block_lines($withoutRecordar, $recordarLine);
        if ($stackBlock2 === null) {
            return null;
        }
        $insertAt = $stackBlock2['end'] + 1;
        $result = v3_splice($withoutRecordar, $insertAt, 0, [$recordarContent]);
    }

    $fixed = implode("\n", $result);

    return $fixed === $content ? null : $fixed;
}

$files = v3_target_files($root, $singleFile);
$changed = 0;
$skipped = 0;

foreach ($files as $abs) {
    $content = file_get_contents($abs);
    if ($content === false) {
        continue;
    }
    $fixed = v3_fix_content($content);
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
