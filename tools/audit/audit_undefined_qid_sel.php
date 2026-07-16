#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Detecta controladores que usan $Qid_sel en ListNavSupport::persistRecordarEntry
 * sin inicializarlo antes (warning "Undefined variable $Qid_sel").
 *
 * Uso:
 *   php tools/audit/audit_undefined_qid_sel.php
 *   php tools/audit/audit_undefined_qid_sel.php --json
 */

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "Solo CLI.\n");
    exit(1);
}

$root = dirname(__DIR__, 2);
$json = in_array('--json', $argv, true);
$frontend = $root . '/frontend';
$issues = [];

$iter = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($frontend, FilesystemIterator::SKIP_DOTS)
);

/** @return list<int> */
function audit_qid_sel_assignment_lines(array $lines, int $beforeLine): array
{
    $found = [];
    foreach (array_slice($lines, 0, $beforeLine) as $i => $line) {
        if (preg_match('/\$Qid_sel\s*=/', $line)) {
            $found[] = $i + 1;
        }
    }

    return $found;
}

function audit_qid_sel_has_safe_init(array $lines, int $beforeLine): bool
{
    $before = implode("\n", array_slice($lines, 0, $beforeLine));
    if (preg_match('/ListNavSupport::restoreSelectionFromStackPost\s*\(/', $before)) {
        return true;
    }

    foreach (array_slice($lines, 0, $beforeLine) as $line) {
        if (preg_match('/\$Qid_sel\s*=\s*ListNavSupport::idSelFromPost\s*\(/', $line)) {
            return true;
        }
        if (preg_match('/\$Qid_sel\s*=\s*!\s*ListNavSupport::idSelIsEmpty/', $line)) {
            return true;
        }
        if (preg_match('/\$Qid_sel\s*=\s*(null|\'\'|\[\])/', $line)) {
            return true;
        }
        if (preg_match('/\$Qid_sel\s*=\s*\(array\)/', $line)) {
            return true;
        }
        if (preg_match('/\$Qid_sel\s*=\s*\(string\)/', $line)) {
            return true;
        }
        if (preg_match('/\$Qid_sel\s*=\s*planning_post_string/', $line)) {
            return true;
        }
        if (preg_match('/\$Qid_sel\s*=\s*actividades_posicion_string/', $line)) {
            return true;
        }
        if (preg_match('/\$Qid_sel\s*=\s*PayloadCoercion::string/', $line)) {
            return true;
        }
        if (preg_match('/\$Qid_sel\s*=\s*\$restored\[/', $line)) {
            return true;
        }
    }

    return false;
}

foreach ($iter as $file) {
    if (!$file->isFile() || $file->getExtension() !== 'php') {
        continue;
    }

    $abs = $file->getPathname();
    $content = file_get_contents($abs);
    if ($content === false || !str_contains($content, 'ListNavSupport::persistRecordarEntry')) {
        continue;
    }
    if (!preg_match('/\$Qid_sel\b/', $content)) {
        continue;
    }

    $lines = preg_split('/\R/', $content);
    if (!is_array($lines)) {
        continue;
    }

    $persistLine = null;
    foreach ($lines as $i => $line) {
        if (str_contains($line, 'ListNavSupport::persistRecordarEntry') && str_contains($line, '$Qid_sel')) {
            $persistLine = $i;
            break;
        }
    }
    if ($persistLine === null) {
        continue;
    }

    if (audit_qid_sel_has_safe_init($lines, $persistLine)) {
        continue;
    }

    $assignLines = audit_qid_sel_assignment_lines($lines, $persistLine);
    $rel = str_replace($root . '/', '', $abs);
    $issues[] = [
        'file' => $rel,
        'persist_line' => $persistLine + 1,
        'assign_lines' => $assignLines,
        'reason' => $assignLines === []
            ? 'no_assignment_before_persist'
            : 'only_conditional_assignment',
    ];
}

if ($json) {
    echo json_encode(['undefined_qid_sel' => $issues], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
} else {
    foreach ($issues as $issue) {
        $assign = $issue['assign_lines'] === []
            ? 'sin asignación'
            : 'solo condicional en líneas ' . implode(', ', $issue['assign_lines']);
        fwrite(STDOUT, sprintf(
            "%s:%d (%s)\n",
            $issue['file'],
            $issue['persist_line'],
            $assign
        ));
    }
    fwrite(STDOUT, 'Total: ' . count($issues) . "\n");
}

exit(count($issues) > 0 ? 1 : 0);
