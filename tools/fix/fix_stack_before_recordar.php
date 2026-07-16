#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Mueve bloques de restauración `stack` ANTES de `$oPosicion->recordar()`.
 *
 * DEPRECATED: usar tools/fix/fix_stack_before_recordar_v3.php (este script rompía la sintaxis).
 *
 * Uso:
 *   php tools/fix/fix_stack_before_recordar.php              # dry-run
 *   php tools/fix/fix_stack_before_recordar.php --apply
 *   php tools/fix/fix_stack_before_recordar.php --apply --path frontend/planning/controller
 */

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "Solo CLI.\n");
    exit(1);
}

$apply = in_array('--apply', $argv, true);
$path = dirname(__DIR__, 2) . '/frontend';
foreach ($argv as $arg) {
    if (str_starts_with($arg, '--path=')) {
        $path = substr($arg, strlen('--path='));
        if (!str_starts_with($path, '/')) {
            $path = dirname(__DIR__, 2) . '/' . ltrim($path, '/');
        }
    }
}

/** @return list<string> */
function fix_collect_files(string $path): array
{
    $root = dirname(__DIR__, 2);
    $abs = str_starts_with($path, '/') ? $path : $root . '/' . ltrim($path, '/');
    if (is_file($abs)) {
        return [$abs];
    }
    $files = [];
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($abs, FilesystemIterator::SKIP_DOTS));
    foreach ($it as $f) {
        if ($f->isFile() && str_contains($f->getPathname(), '/controller/') && $f->getExtension() === 'php') {
            $files[] = $f->getPathname();
        }
    }
    sort($files);
    return $files;
}

function fix_line_of(string $content, string $needle): ?int
{
    $pos = strpos($content, $needle);
    if ($pos === false) {
        return null;
    }
    return substr_count(substr($content, 0, $pos), "\n") + 1;
}

/**
 * Mueve el primer bloque if (isset($_POST['stack'])) ... goStack ... olvidar antes de recordar().
 */
function fix_move_stack_block(string $content): ?string
{
    if (!str_contains($content, '->recordar(') || !str_contains($content, 'goStack')) {
        return null;
    }

    $recordarPos = strpos($content, '->recordar(');
    if ($recordarPos === false) {
        return null;
    }

    // Primer goStack después de recordar
    $goAfter = strpos($content, 'goStack', $recordarPos);
    if ($goAfter === false) {
        return null;
    }

    // ¿Hay goStack antes de recordar? Entonces solo arreglar si hay otro después
    $goBefore = strpos($content, 'goStack');
    if ($goBefore !== false && $goBefore < $recordarPos && $goAfter === $goBefore) {
        return null;
    }

    if (!preg_match(
        '/\n(\s*\/\/[^\n]*\n)?\s*if\s*\(\s*isset\s*\(\s*\$_POST\s*\[\s*[\'"]stack[\'"]\s*\]\s*\)\s*\)\s*\{[\s\S]*?->goStack\s*\([\s\S]*?->olvidar\s*\([\s\S]*?\n\s*\}\s*\n\s*\}/',
        $content,
        $m,
        PREG_OFFSET_CAPTURE,
        $recordarPos,
    )) {
        return null;
    }

    $block = $m[0][0];
    $blockStart = $m[0][1];
    if ($blockStart <= $recordarPos) {
        return null;
    }

    $without = substr($content, 0, $blockStart) . substr($content, $blockStart + strlen($block));

    // Insertar bloque justo antes de ->recordar(
    $recordarPos2 = strpos($without, '->recordar(');
    if ($recordarPos2 === false) {
        return null;
    }

    // Buscar inicio de línea de recordar
    $lineStart = strrpos(substr($without, 0, $recordarPos2), "\n");
    $lineStart = $lineStart === false ? 0 : $lineStart + 1;

    return substr($without, 0, $lineStart) . $block . substr($without, $lineStart);
}

/**
 * Mueve bloque `$oPosicion->goStack($stackFromPost)` (mismo objeto) antes de recordar.
 */
function fix_move_same_posicion_gostack(string $content): ?string
{
    if (!preg_match(
        '/\n(\s*if\s*\([^)]*stack[^)]*\)\s*\{[\s\S]*?\$oPosicion->goStack\s*\([^)]+\)[\s\S]*?\}\s*\n)/',
        $content,
        $m,
        PREG_OFFSET_CAPTURE,
    )) {
        return null;
    }

    $block = $m[1][0];
    $blockStart = $m[1][1];
    $recordarPos = strpos($content, '->recordar(');
    if ($recordarPos === false || $blockStart <= $recordarPos) {
        return null;
    }

    $without = substr($content, 0, $blockStart) . substr($content, $blockStart + strlen($block));
    $recordarPos2 = strpos($without, '->recordar(');
    if ($recordarPos2 === false) {
        return null;
    }
    $lineStart = strrpos(substr($without, 0, $recordarPos2), "\n");
    $lineStart = $lineStart === false ? 0 : $lineStart + 1;

    return substr($without, 0, $lineStart) . $block . substr($without, $lineStart);
}

$files = fix_collect_files($path);
$changed = 0;
$skipped = 0;

foreach ($files as $abs) {
    $content = file_get_contents($abs);
    if ($content === false) {
        continue;
    }

    $fixed = fix_move_stack_block($content);
    if ($fixed === null) {
        $fixed = fix_move_same_posicion_gostack($content);
    }
    if ($fixed === null || $fixed === $content) {
        $skipped++;
        continue;
    }

    $rel = str_replace(dirname(__DIR__, 2) . '/', '', $abs);
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
