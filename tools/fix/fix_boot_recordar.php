#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Sustituye `$oPosicion->recordar(...)` por `ListNavSupport::bootRecordar($oPosicion, ...)`
 * en controladores que aún no limpian `stack` heredado del POST.
 *
 * Uso:
 *   php tools/fix/fix_boot_recordar.php              # dry-run
 *   php tools/fix/fix_boot_recordar.php --apply
 *   php tools/fix/fix_boot_recordar.php --apply --file frontend/planning/controller/planning_persona_select.php
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

const SKIP_MARKERS = [
    'ListNavSupport::bootRecordar',
    'ListNavSupport::bootChildFromListRecordar',
    'ListNavSupport::bootActividadSelectChildRecordar',
    'ListNavSupport::bootDossiersFromActividadSelect',
    'ListNavSupport::clearInheritedStackForRecordar',
];

/** @return list<string> */
function fix_br_target_files(string $root, ?string $singleFile): array
{
    if ($singleFile !== null) {
        $abs = str_starts_with($singleFile, '/') ? $singleFile : $root . '/' . ltrim($singleFile, '/');

        return is_file($abs) ? [$abs] : [];
    }

    exec(
        'php ' . escapeshellarg($root . '/tools/audit/audit_posicion_nav_migration.php') . ' --only=sin_clear_stack --json 2>/dev/null',
        $jsonOut,
    );
    $decoded = json_decode(implode("\n", $jsonOut), true);
    $files = [];
    if (is_array($decoded['sin_clear_stack'] ?? null)) {
        foreach ($decoded['sin_clear_stack'] as $rel) {
            $files[] = $root . '/' . $rel;
        }
    }

    return $files;
}

function fix_br_should_skip(string $content): bool
{
    foreach (SKIP_MARKERS as $marker) {
        if (str_contains($content, $marker)) {
            return true;
        }
    }

    return false;
}

function fix_br_ensure_use(string $content): string
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

function fix_br_transform(string $content): ?string
{
    if (!str_contains($content, 'FrontBootstrap::boot()') || !str_contains($content, '->recordar(')) {
        return null;
    }
    if (fix_br_should_skip($content)) {
        return null;
    }

    $new = preg_replace(
        '/\$oPosicion->recordar\s*\(\s*\)\s*;/',
        'ListNavSupport::bootRecordar($oPosicion);',
        $content,
    );
    if (!is_string($new)) {
        return null;
    }
    $new = preg_replace(
        '/\$oPosicion->recordar\s*\(\s*([^)]+)\s*\)\s*;/',
        'ListNavSupport::bootRecordar($oPosicion, $1);',
        $new,
    );
    if (!is_string($new) || $new === $content) {
        return null;
    }

    return $new;
}

$files = fix_br_target_files($root, $singleFile);
$changed = 0;

foreach ($files as $absPath) {
    $content = file_get_contents($absPath);
    if ($content === false) {
        continue;
    }
    $transformed = fix_br_transform($content);
    if ($transformed === null) {
        continue;
    }
    $transformed = fix_br_ensure_use($transformed);
    $rel = str_replace($root . '/', '', $absPath);
    if ($apply) {
        file_put_contents($absPath, $transformed);
        fwrite(STDOUT, "OK {$rel}\n");
    } else {
        fwrite(STDOUT, "would fix {$rel}\n");
    }
    $changed++;
}

fwrite(STDOUT, ($apply ? 'Aplicados' : 'Pendientes') . ": {$changed}\n");
exit(0);
