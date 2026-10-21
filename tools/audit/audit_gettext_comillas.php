#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Comprueba que las cadenas gettext usan comillas dobles _("…") o escapes.
 *
 * En catalán (msgstr) hay apóstrofos (l', d', n'). Dentro de un literal PHP
 * van en comillas dobles, o en comillas simples con \' .
 *
 * Uso:
 *   php tools/audit/audit_gettext_comillas.php
 *   php tools/audit/audit_gettext_comillas.php --json
 *   php tools/audit/audit_gettext_comillas.php --list-single
 *   php tools/audit/audit_gettext_comillas.php --strict
 */

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "Solo CLI.\n");
    exit(1);
}

$root = dirname(__DIR__, 2);
$json = in_array('--json', $argv, true);
$listSingle = in_array('--list-single', $argv, true);
$strict = in_array('--strict', $argv, true);

/** @var list<string> */
$scanRoots = ['frontend', 'src', 'scripts', 'public'];

/** @var list<string> */
$skipFragments = ['/vendor/', '/libs/', '/tests/', '/node_modules/', '/proves/'];

/**
 * @return Generator<string>
 */
function audit_comillas_files(string $root, array $scanRoots, array $skipFragments): Generator
{
    foreach ($scanRoots as $dir) {
        $base = $root . '/' . $dir;
        if (!is_dir($base)) {
            continue;
        }
        $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base, FilesystemIterator::SKIP_DOTS));
        foreach ($rii as $file) {
            if (!$file->isFile()) {
                continue;
            }
            $path = $file->getPathname();
            $rel = str_replace($root . '/', '', $path);
            $skip = false;
            foreach ($skipFragments as $fragment) {
                if (str_contains('/' . $rel, $fragment)) {
                    $skip = true;
                    break;
                }
            }
            if ($skip) {
                continue;
            }
            $ext = $file->getExtension();
            $name = $file->getFilename();
            if (in_array($ext, ['php', 'phtml', 'inc'], true) || str_ends_with($name, '.twig')) {
                yield $path;
            }
        }
    }
}

function audit_comillas_skip_ws(array $tokens, int $i, int $n): int
{
    while ($i < $n && is_array($tokens[$i]) && in_array($tokens[$i][0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)) {
        $i++;
    }

    return $i;
}

/**
 * @return array{quote: string, escaped_delimiter: bool, text: string}
 */
function audit_comillas_classify_literal(string $raw): array
{
    $quote = $raw[0];
    $inner = substr($raw, 1, -1);
    $escaped = $quote === "'"
        ? str_contains($inner, "\\'")
        : str_contains($inner, '\\"');

    return [
        'quote' => $quote === '"' ? 'double' : 'single',
        'escaped_delimiter' => $escaped,
        'text' => stripcslashes($inner),
    ];
}

/**
 * @param list<mixed> $argTokens
 * @return array{kind: string, parts: list<array{quote: string, escaped_delimiter: bool, text: string, line: int}>}
 */
function audit_comillas_classify_arg(array $argTokens): array
{
    $parts = [];
    $other = false;
    foreach ($argTokens as $token) {
        if ($token === '.') {
            continue;
        }
        if (is_array($token) && $token[0] === T_CONSTANT_ENCAPSED_STRING) {
            $class = audit_comillas_classify_literal($token[1]);
            $class['line'] = $token[2];
            $parts[] = $class;
            continue;
        }
        $other = true;
    }

    if ($parts !== [] && !$other) {
        return ['kind' => count($parts) === 1 ? 'literal' : 'concat', 'parts' => $parts];
    }

    return ['kind' => 'non_literal', 'parts' => $parts];
}

/**
 * @return list<array{fn: string, line: int, kind: string, parts: list<array{quote: string, escaped_delimiter: bool, text: string, line: int}>, snippet: string}>
 */
function audit_comillas_php_calls(string $content): array
{
    $tokens = token_get_all($content);
    $n = count($tokens);
    $calls = [];
    $fns = ['_' => [0], 'gettext' => [0], 'ngettext' => [0, 1], 'dgettext' => [1]];

    for ($i = 0; $i < $n; $i++) {
        $token = $tokens[$i];
        if (!is_array($token) || $token[0] !== T_STRING || !isset($fns[$token[1]])) {
            continue;
        }
        $prev = audit_comillas_skip_ws_back($tokens, $i - 1);
        if ($prev >= 0 && is_array($tokens[$prev]) && in_array($tokens[$prev][0], [T_OBJECT_OPERATOR, T_NULLSAFE_OBJECT_OPERATOR, T_DOUBLE_COLON, T_FUNCTION], true)) {
            continue;
        }
        $open = audit_comillas_skip_ws($tokens, $i + 1, $n);
        if ($open >= $n || $tokens[$open] !== '(') {
            continue;
        }

        $args = [];
        $current = [];
        $depth = 0;
        for ($j = $open; $j < $n; $j++) {
            $tk = $tokens[$j];
            if ($tk === '(') {
                if ($depth > 0) {
                    $current[] = $tk;
                }
                $depth++;
                continue;
            }
            if ($tk === ')') {
                $depth--;
                if ($depth === 0) {
                    $args[] = $current;
                    break;
                }
                $current[] = $tk;
                continue;
            }
            if ($tk === ',' && $depth === 1) {
                $args[] = $current;
                $current = [];
                continue;
            }
            if ($depth >= 1 && !(is_array($tk) && in_array($tk[0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true))) {
                $current[] = $tk;
            }
        }

        $wanted = $fns[$token[1]];
        foreach ($wanted as $index) {
            if (!isset($args[$index])) {
                continue;
            }
            $class = audit_comillas_classify_arg($args[$index]);
            $calls[] = [
                'fn' => $token[1],
                'line' => $token[2],
                'kind' => $class['kind'],
                'parts' => $class['parts'],
                'snippet' => audit_comillas_snippet($content, $token[2]),
            ];
        }
    }

    return $calls;
}

function audit_comillas_skip_ws_back(array $tokens, int $i): int
{
    while ($i >= 0 && is_array($tokens[$i]) && in_array($tokens[$i][0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)) {
        $i--;
    }

    return $i;
}

function audit_comillas_snippet(string $content, int $line): string
{
    $lines = preg_split('/\R/', $content) ?: [];
    $text = trim($lines[$line - 1] ?? '');
    if (mb_strlen($text) > 160) {
        return mb_substr($text, 0, 157) . '...';
    }

    return $text;
}

/**
 * @return list<array{line: int, quote: string, escaped_delimiter: bool, text: string}>
 */
function audit_comillas_twig(string $content): array
{
    $found = [];
    if (!preg_match_all('/\{\{\s*([\'"])((?:\\\\.|(?!\1).)*)\1\s*\|\s*trans\b/', $content, $matches, PREG_OFFSET_CAPTURE)) {
        return $found;
    }
    foreach ($matches[2] as $i => $cap) {
        $quote = $matches[1][$i][0];
        $inner = $cap[0];
        $escaped = $quote === "'"
            ? str_contains($inner, "\\'")
            : str_contains($inner, '\\"');
        $text = $quote === "'"
            ? str_replace(["\\'", '\\\\'], ["'", '\\'], $inner)
            : stripcslashes($inner);
        $found[] = [
            'line' => substr_count(substr($content, 0, $cap[1]), "\n") + 1,
            'quote' => $quote === '"' ? 'double' : 'single',
            'escaped_delimiter' => $escaped,
            'text' => $text,
        ];
    }

    return $found;
}

$stats = [
    'php_double' => 0,
    'php_double_escaped' => 0,
    'php_single' => 0,
    'php_single_escaped' => 0,
    'php_concat' => 0,
    'php_non_literal' => 0,
    'twig_double' => 0,
    'twig_double_escaped' => 0,
    'twig_single' => 0,
    'twig_single_escaped' => 0,
];
$nonLiterals = [];
$escapedSingles = [];
$singleSamples = [];

foreach (audit_comillas_files($root, $scanRoots, $skipFragments) as $abs) {
    $rel = str_replace($root . '/', '', $abs);
    $content = file_get_contents($abs);
    if ($content === false || $content === '') {
        continue;
    }

    if (str_ends_with($abs, '.twig')) {
        foreach (audit_comillas_twig($content) as $item) {
            if ($item['quote'] === 'double') {
                $stats['twig_double']++;
                if ($item['escaped_delimiter']) {
                    $stats['twig_double_escaped']++;
                }
            } else {
                $stats['twig_single']++;
                if ($item['escaped_delimiter']) {
                    $stats['twig_single_escaped']++;
                    $escapedSingles[] = ['file' => $rel, 'line' => $item['line'], 'text' => $item['text'], 'where' => 'twig'];
                } elseif ($listSingle) {
                    $singleSamples[] = ['file' => $rel, 'line' => $item['line'], 'text' => $item['text'], 'where' => 'twig'];
                }
            }
        }
        continue;
    }

    foreach (audit_comillas_php_calls($content) as $call) {
        if ($call['kind'] === 'non_literal') {
            $stats['php_non_literal']++;
            $nonLiterals[] = ['file' => $rel, 'line' => $call['line'], 'fn' => $call['fn'], 'snippet' => $call['snippet']];
            continue;
        }
        if ($call['kind'] === 'concat') {
            $stats['php_concat']++;
        }
        foreach ($call['parts'] as $part) {
            if ($part['quote'] === 'double') {
                $stats['php_double']++;
                if ($part['escaped_delimiter']) {
                    $stats['php_double_escaped']++;
                }
                continue;
            }
            $stats['php_single']++;
            if ($part['escaped_delimiter'] || str_contains($part['text'], "'")) {
                $stats['php_single_escaped']++;
                $escapedSingles[] = ['file' => $rel, 'line' => $part['line'], 'text' => $part['text'], 'where' => 'php'];
            } elseif ($listSingle) {
                $singleSamples[] = ['file' => $rel, 'line' => $part['line'], 'text' => $part['text'], 'where' => 'php'];
            }
        }
    }
}

$problems = $stats['php_non_literal'];
if ($strict) {
    $problems += $stats['php_single'] + $stats['twig_single'];
}

if ($json) {
    echo json_encode([
        'stats' => $stats,
        'non_literals' => $nonLiterals,
        'single_escaped' => $escapedSingles,
        'singles' => $listSingle ? $singleSamples : [],
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n";
    exit($problems > 0 ? 1 : 0);
}

fwrite(STDOUT, "Fase 1 — comillas en gettext\n");
fwrite(STDOUT, "Forma pedida: _(\"texto\") o escapes (\\' / \\\").\n");
fwrite(STDOUT, "Los apóstrofos del catalán viven en el msgstr del .po, entre comillas dobles del formato PO.\n\n");
fwrite(STDOUT, "PHP _() / gettext / ngettext / dgettext\n");
fwrite(STDOUT, "  comillas dobles: {$stats['php_double']}\n");
fwrite(STDOUT, "  de ellas con \\\" escapadas: {$stats['php_double_escaped']}\n");
fwrite(STDOUT, "  comillas simples: {$stats['php_single']}\n");
fwrite(STDOUT, "  de ellas con \\' (escapes): {$stats['php_single_escaped']}\n");
fwrite(STDOUT, "  concatenación de literales: {$stats['php_concat']}\n");
fwrite(STDOUT, "  argumento no literal: {$stats['php_non_literal']}\n");
fwrite(STDOUT, "Twig |trans\n");
fwrite(STDOUT, "  comillas dobles: {$stats['twig_double']}\n");
fwrite(STDOUT, "  de ellas con escapes: {$stats['twig_double_escaped']}\n");
fwrite(STDOUT, "  comillas simples: {$stats['twig_single']}\n");
fwrite(STDOUT, "  de ellas con escapes: {$stats['twig_single_escaped']}\n\n");

if ($escapedSingles !== []) {
    fwrite(STDOUT, "Literales con apóstrofo escapado (válidos; la forma preferida es comillas dobles):\n");
    foreach ($escapedSingles as $row) {
        fwrite(STDOUT, "  {$row['file']}:{$row['line']} {$row['text']}\n");
    }
    fwrite(STDOUT, "\n");
}

if ($nonLiterals !== []) {
    fwrite(STDOUT, "Llamadas cuyo argumento no es un literal (xgettext no las cataloga):\n");
    foreach ($nonLiterals as $row) {
        fwrite(STDOUT, "  {$row['file']}:{$row['line']} {$row['snippet']}\n");
    }
    fwrite(STDOUT, "\n");
}

if ($listSingle && $singleSamples !== []) {
    fwrite(STDOUT, "Comillas simples sin apóstrofo:\n");
    foreach ($singleSamples as $row) {
        $preview = mb_strlen($row['text']) > 80 ? mb_substr($row['text'], 0, 77) . '...' : $row['text'];
        fwrite(STDOUT, "  {$row['file']}:{$row['line']} {$preview}\n");
    }
    fwrite(STDOUT, "\n");
}

fwrite(STDOUT, "Desviación de estilo (comilla simple, sin apóstrofo interno): " . ($stats['php_single'] - $stats['php_single_escaped'] + $stats['twig_single'] - $stats['twig_single_escaped']) . "\n");
fwrite(STDOUT, "Listado completo: php tools/audit/audit_gettext_comillas.php --list-single\n");

exit($problems > 0 ? 1 : 0);
