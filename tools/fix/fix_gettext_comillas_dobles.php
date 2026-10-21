#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Pasa los literales gettext de comillas simples a dobles: _('…') → _("…").
 *
 * Si el texto contiene ", \, $ o \', los reescribe para que el valor no cambie
 * (" → \", $ → \$, \ → \\, \' → ').
 *
 * Uso:
 *   php tools/fix/fix_gettext_comillas_dobles.php --dry-run
 *   php tools/fix/fix_gettext_comillas_dobles.php --apply
 */

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "Solo CLI.\n");
    exit(1);
}

$apply = in_array('--apply', $argv, true);
$dryRun = in_array('--dry-run', $argv, true);
if ($apply === $dryRun) {
    fwrite(STDERR, "Indica --dry-run o --apply.\n");
    exit(1);
}

$root = dirname(__DIR__, 2);

/**
 * @return array{0: string, 1: bool, 2: bool, 3: bool, 4: bool}
 */
function fix_gettext_single_to_double(string $raw): array
{
    if ($raw === '' || $raw[0] !== "'" || !str_ends_with($raw, "'")) {
        throw new RuntimeException('No es un literal entre comillas simples');
    }
    $inner = substr($raw, 1, -1);
    $decoded = '';
    $len = strlen($inner);
    $hadApostrophe = false;
    for ($i = 0; $i < $len; $i++) {
        if ($inner[$i] === '\\' && $i + 1 < $len) {
            $next = $inner[$i + 1];
            if ($next === '\\' || $next === "'") {
                if ($next === "'") {
                    $hadApostrophe = true;
                }
                $decoded .= $next;
                $i++;
                continue;
            }
        }
        $decoded .= $inner[$i];
    }

    $encoded = '';
    $escapedQuote = false;
    $escapedDollar = false;
    $escapedSlash = false;
    $dlen = strlen($decoded);
    for ($i = 0; $i < $dlen; $i++) {
        $c = $decoded[$i];
        if ($c === '\\' || $c === '"' || $c === '$') {
            $encoded .= '\\' . $c;
            if ($c === '"') {
                $escapedQuote = true;
            } elseif ($c === '$') {
                $escapedDollar = true;
            } else {
                $escapedSlash = true;
            }
            continue;
        }
        $encoded .= $c;
    }

    return ['"' . $encoded . '"', $escapedQuote, $escapedDollar, $escapedSlash, $hadApostrophe];
}

function fix_gettext_same_value(string $singleRaw, string $doubleRaw): bool
{
    try {
        $pair = eval('return [' . $singleRaw . ', ' . $doubleRaw . '];');
    } catch (ParseError) {
        return false;
    }

    return is_array($pair) && $pair[0] === $pair[1];
}

function fix_gettext_skip_ws(array $tokens, int $i, int $n): int
{
    while ($i < $n && is_array($tokens[$i]) && in_array($tokens[$i][0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)) {
        $i++;
    }

    return $i;
}

function fix_gettext_skip_ws_back(array $tokens, int $i): int
{
    while ($i >= 0 && is_array($tokens[$i]) && in_array($tokens[$i][0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)) {
        $i--;
    }

    return $i;
}

/**
 * Índices de literales '…' que son argumento de _(), gettext(), ngettext() o dgettext().
 *
 * @return list<int>
 */
function fix_gettext_single_indexes(array $tokens): array
{
    $n = count($tokens);
    $fns = ['_' => [0], 'gettext' => [0], 'ngettext' => [0, 1], 'dgettext' => [1]];
    $indexes = [];

    for ($i = 0; $i < $n; $i++) {
        $token = $tokens[$i];
        $name = null;
        if (is_array($token) && $token[0] === T_STRING && isset($fns[$token[1]])) {
            $name = $token[1];
        } elseif (is_array($token) && defined('T_NAME_FULLY_QUALIFIED') && $token[0] === T_NAME_FULLY_QUALIFIED) {
            $base = ltrim($token[1], '\\');
            if (isset($fns[$base])) {
                $name = $base;
            }
        }
        if ($name === null) {
            continue;
        }
        $prev = fix_gettext_skip_ws_back($tokens, $i - 1);
        if ($prev >= 0 && is_array($tokens[$prev]) && in_array($tokens[$prev][0], [T_OBJECT_OPERATOR, T_NULLSAFE_OBJECT_OPERATOR, T_DOUBLE_COLON, T_FUNCTION], true)) {
            continue;
        }
        $open = fix_gettext_skip_ws($tokens, $i + 1, $n);
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
                    $current[] = $j;
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
                $current[] = $j;
                continue;
            }
            if ($tk === ',' && $depth === 1) {
                $args[] = $current;
                $current = [];
                continue;
            }
            if ($depth >= 1) {
                $current[] = $j;
            }
        }

        foreach ($fns[$name] as $argIndex) {
            if (!isset($args[$argIndex])) {
                continue;
            }
            foreach ($args[$argIndex] as $tokenIndex) {
                $tk = $tokens[$tokenIndex];
                if (is_array($tk) && $tk[0] === T_CONSTANT_ENCAPSED_STRING && str_starts_with($tk[1], "'")) {
                    $indexes[] = $tokenIndex;
                }
            }
        }
    }

    return $indexes;
}

function fix_gettext_token_text(array|string $token): string
{
    return is_array($token) ? $token[1] : $token;
}

/**
 * @param list<string> $scanRoots
 */
function fix_gettext_php_files(string $root, array $scanRoots): Generator
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
            if (str_contains($path, '/vendor/') || str_contains($path, '/libs/') || str_contains($path, '/node_modules/')) {
                continue;
            }
            $ext = $file->getExtension();
            if (in_array($ext, ['php', 'phtml', 'inc'], true)) {
                yield $path;
            }
        }
    }
}

function fix_gettext_twig_decode(string $inner): string
{
    $decoded = '';
    $len = strlen($inner);
    for ($i = 0; $i < $len; $i++) {
        if ($inner[$i] === '\\' && $i + 1 < $len && ($inner[$i + 1] === '\\' || $inner[$i + 1] === "'")) {
            $decoded .= $inner[$i + 1];
            $i++;
            continue;
        }
        $decoded .= $inner[$i];
    }

    return $decoded;
}

function fix_gettext_twig_encode(string $decoded): string
{
    $encoded = '';
    $len = strlen($decoded);
    for ($i = 0; $i < $len; $i++) {
        $c = $decoded[$i];
        if ($c === '\\' || $c === '"') {
            $encoded .= '\\' . $c;
            continue;
        }
        if ($c === '#' && ($decoded[$i + 1] ?? '') === '{') {
            $encoded .= '\\#{';
            $i++;
            continue;
        }
        $encoded .= $c;
    }

    return $encoded;
}

$fixtures = [
    "'hola'",
    "'de paso \\'x\\''",
    "'di \"hola\"'",
    "'%1\$s con \"c1\"'",
    "'a\\\\b'",
    "'line\\nbreak'",
    "''",
];
foreach ($fixtures as $fixture) {
    [$double] = fix_gettext_single_to_double($fixture);
    if (!fix_gettext_same_value($fixture, $double)) {
        fwrite(STDERR, "Autocomprobación fallida: {$fixture} => {$double}\n");
        exit(1);
    }
}

$stats = [
    'files' => 0,
    'strings' => 0,
    'quotes' => 0,
    'dollars' => 0,
    'slashes' => 0,
    'apostrophes' => 0,
    'twig' => 0,
];
$quoteSamples = [];
$failures = [];

$scanRoots = ['frontend', 'src', 'scripts', 'public', 'tests'];

foreach (fix_gettext_php_files($root, $scanRoots) as $abs) {
    $content = file_get_contents($abs);
    if ($content === false || $content === '') {
        continue;
    }
    if (preg_match('/\b(?:_|gettext|ngettext|dgettext)\s*\(/', $content) !== 1) {
        continue;
    }
    $tokens = token_get_all($content);
    $rebuilt = '';
    foreach ($tokens as $token) {
        $rebuilt .= fix_gettext_token_text($token);
    }
    if ($rebuilt !== $content) {
        $failures[] = str_replace($root . '/', '', $abs) . ': el tokenizador no reproduce el fichero';
        continue;
    }

    $indexes = fix_gettext_single_indexes($tokens);
    if ($indexes === []) {
        continue;
    }

    $replace = [];
    $fileQuotes = [];
    $ok = true;
    foreach ($indexes as $index) {
        $raw = $tokens[$index][1];
        [$double, $escapedQuote, $escapedDollar, $escapedSlash, $hadApostrophe] = fix_gettext_single_to_double($raw);
        if (!fix_gettext_same_value($raw, $double)) {
            $failures[] = str_replace($root . '/', '', $abs) . ':' . $tokens[$index][2] . ' valor distinto tras escapar';
            $ok = false;
            break;
        }
        $replace[$index] = $double;
        $stats['strings']++;
        if ($escapedQuote) {
            $stats['quotes']++;
            $fileQuotes[] = $tokens[$index][2] . ' ' . $double;
        }
        if ($escapedDollar) {
            $stats['dollars']++;
        }
        if ($escapedSlash) {
            $stats['slashes']++;
        }
        if ($hadApostrophe) {
            $stats['apostrophes']++;
        }
    }
    if (!$ok) {
        continue;
    }

    $new = '';
    foreach ($tokens as $index => $token) {
        $new .= $replace[$index] ?? fix_gettext_token_text($token);
    }
    if ($new === $content) {
        continue;
    }

    $rel = str_replace($root . '/', '', $abs);
    if ($apply) {
        $tmp = $abs . '.gettext-tmp';
        file_put_contents($tmp, $new);
        exec('php -l ' . escapeshellarg($tmp) . ' 2>&1', $lintOut, $lintCode);
        if ($lintCode !== 0) {
            @unlink($tmp);
            $failures[] = $rel . ': php -l ha fallado';
            continue;
        }
        rename($tmp, $abs);
    }
    $stats['files']++;
    foreach ($fileQuotes as $sample) {
        $quoteSamples[] = $rel . ':' . $sample;
    }
}

$twigRoots = ['frontend', 'src'];
foreach ($twigRoots as $dir) {
    $base = $root . '/' . $dir;
    if (!is_dir($base)) {
        continue;
    }
    $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base, FilesystemIterator::SKIP_DOTS));
    foreach ($rii as $file) {
        if (!$file->isFile() || !str_ends_with($file->getFilename(), '.twig')) {
            continue;
        }
        $abs = $file->getPathname();
        $content = file_get_contents($abs);
        if ($content === false || !str_contains($content, '|trans')) {
            continue;
        }
        $count = 0;
        $new = preg_replace_callback(
            '/\{\{(\s*)\'((?:\\\\.|[^\'\\\\])*)\'(?=\s*\|\s*trans\b)/',
            static function (array $m) use (&$count): string {
                $count++;
                $encoded = fix_gettext_twig_encode(fix_gettext_twig_decode($m[2]));

                return '{{' . $m[1] . '"' . $encoded . '"';
            },
            $content
        );
        if ($count === 0 || $new === null || $new === $content) {
            continue;
        }
        $rel = str_replace($root . '/', '', $abs);
        if ($apply) {
            file_put_contents($abs, $new);
        }
        $stats['files']++;
        $stats['twig'] += $count;
        $stats['strings'] += $count;
    }
}

$mode = $apply ? 'Aplicado' : 'Simulación';
fwrite(STDOUT, "{$mode}\n");
fwrite(STDOUT, "Ficheros: {$stats['files']}\n");
fwrite(STDOUT, "Literales: {$stats['strings']} (twig: {$stats['twig']})\n");
fwrite(STDOUT, "Con \\\" escapadas: {$stats['quotes']}\n");
fwrite(STDOUT, "Con \\\$ escapado (p. ej. %1\$s): {$stats['dollars']}\n");
fwrite(STDOUT, "Con \\\\ escapada: {$stats['slashes']}\n");
fwrite(STDOUT, "Apóstrofos que estaban como \\': {$stats['apostrophes']}\n");
if ($quoteSamples !== []) {
    fwrite(STDOUT, "\nTextos con comilla doble escapada:\n");
    foreach ($quoteSamples as $sample) {
        fwrite(STDOUT, "  {$sample}\n");
    }
}
if ($failures !== []) {
    fwrite(STDOUT, "\nNo tocados:\n");
    foreach ($failures as $failure) {
        fwrite(STDOUT, "  {$failure}\n");
    }
}

exit($failures === [] ? 0 : 1);
