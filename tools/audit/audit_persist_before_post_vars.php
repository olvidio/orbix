#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Detecta ListNavSupport::persistRecordarEntry() con array explícito de $Q*
 * antes de que esas variables estén asignadas en el fichero.
 *
 * Uso:
 *   php tools/audit/audit_persist_before_post_vars.php
 *   php tools/audit/audit_persist_before_post_vars.php --json
 */

$root = dirname(__DIR__, 2);
$json = in_array('--json', $argv, true);
$issues = [];

foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/frontend', FilesystemIterator::SKIP_DOTS)) as $file) {
    if ($file->getExtension() !== 'php') {
        continue;
    }
    $content = file_get_contents($file->getPathname());
    if ($content === false || !preg_match('/ListNavSupport::persistRecordarEntry\s*\([^;]*\[[\s\S]*?\$(Q[a-zA-Z0-9_]+|aGoBack)\b/', $content)) {
        continue;
    }

    $lines = preg_split('/\R/', $content);
    if (!is_array($lines)) {
        continue;
    }

    $persistLine = null;
    foreach ($lines as $i => $line) {
        if (str_contains($line, 'ListNavSupport::persistRecordarEntry')) {
            $persistLine = $i;
            break;
        }
    }
    if ($persistLine === null) {
        continue;
    }

    $call = '';
    for ($i = $persistLine; $i < min($persistLine + 20, count($lines)); $i++) {
        $call .= $lines[$i] . "\n";
        if (str_contains($lines[$i], '));')) {
            break;
        }
    }
    if (!preg_match('/ListNavSupport::mergeSelectionIntoReturnParametros\s*\(\s*\[/', $call)) {
        continue;
    }
    if (!preg_match_all('/\$(Q[a-zA-Z0-9_]+|aGoBack)\b/', $call, $m)) {
        continue;
    }

    $undefined = [];
    foreach (array_unique($m[1]) as $var) {
        $before = implode("\n", array_slice($lines, 0, $persistLine));
        if (!preg_match('/\$' . preg_quote($var, '/') . '\s*=/', $before)) {
            $undefined[] = $var;
        }
    }
    if ($undefined !== []) {
        $issues[] = [
            'file' => str_replace($root . '/', '', $file->getPathname()),
            'line' => $persistLine + 1,
            'vars' => $undefined,
        ];
    }
}

if ($json) {
    echo json_encode(['persist_before_post_vars' => $issues], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
} else {
    foreach ($issues as $issue) {
        echo $issue['file'] . ':' . $issue['line'] . ' -> ' . implode(', ', $issue['vars']) . "\n";
    }
    echo 'Total: ' . count($issues) . "\n";
}

exit(count($issues) > 0 ? 1 : 0);
