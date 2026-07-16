#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Detecta cadenas de texto orientadas al usuario que no están envueltas en gettext (_()).
 *
 * Heurísticas: echo/print, sprintf/printf, claves de UI en arrays, alert() en .js.php,
 * y literales en contexto HTML (?>...<?php).
 *
 * Uso:
 *   php tools/audit/audit_untranslated_strings.php
 *   php tools/audit/audit_untranslated_strings.php --json
 *   php tools/audit/audit_untranslated_strings.php --path=frontend/usuarios
 *   php tools/audit/audit_untranslated_strings.php --min-length=4
 *   php tools/audit/audit_untranslated_strings.php --include-errors
 */

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "Solo CLI.\n");
    exit(1);
}

$root = dirname(__DIR__, 2);
$json = in_array('--json', $argv, true);
$includeErrors = in_array('--include-errors', $argv, true);
$minLength = 3;
$pathFilter = null;

foreach ($argv as $arg) {
    if (str_starts_with($arg, '--path=')) {
        $pathFilter = substr($arg, 7);
    }
    if (str_starts_with($arg, '--min-length=')) {
        $minLength = max(1, (int) substr($arg, 13));
    }
}

/** @var list<string> */
$scanRoots = ['frontend', 'src', 'scripts'];
/** @var list<string> */
$extraFiles = ['index.php'];

/** @var list<string> */
$skipPathFragments = [
    '/vendor/',
    '/libs/',
    '/tests/',
    '/proves/',
    '/node_modules/',
    '/devel_codegen/',
    '_imprimir_mpdf.php',
    'tessera_imprimir.php',
    'acta_imprimir_mpdf.php',
];

/** @var list<string> */
$uiArrayKeys = [
    'txt',
    'mensaje',
    'titulo',
    'label',
    'placeholder',
    'aviso',
    'descripcion',
    'tooltip',
    'confirm',
    'cabecera',
    'leyenda',
    'hint',
    'ayuda',
];

if ($includeErrors) {
    $uiArrayKeys[] = 'error';
}

/**
 * @return Generator<string>
 */
function audit_i18n_scan_files(string $root, array $scanRoots, array $extraFiles, ?string $pathFilter): Generator
{
    if ($pathFilter !== null) {
        $abs = str_starts_with($pathFilter, '/')
            ? $pathFilter
            : $root . '/' . ltrim($pathFilter, '/');
        if (is_file($abs) && str_ends_with($abs, '.php')) {
            yield $abs;
        } elseif (is_dir($abs)) {
            $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($abs, FilesystemIterator::SKIP_DOTS));
            foreach ($rii as $file) {
                if ($file->isFile() && $file->getExtension() === 'php') {
                    yield $file->getPathname();
                }
            }
        }
        return;
    }

    foreach ($scanRoots as $dir) {
        $base = $root . '/' . $dir;
        if (!is_dir($base)) {
            continue;
        }
        $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base, FilesystemIterator::SKIP_DOTS));
        foreach ($rii as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }
            $path = $file->getPathname();
            if ($dir === 'scripts' && !str_ends_with($path, '.js.php')) {
                continue;
            }
            yield $path;
        }
    }

    foreach ($extraFiles as $rel) {
        $abs = $root . '/' . $rel;
        if (is_file($abs)) {
            yield $abs;
        }
    }
}

function audit_i18n_should_skip_file(string $abs, string $root, array $skipPathFragments): bool
{
    $rel = str_replace($root . '/', '', $abs);
    foreach ($skipPathFragments as $fragment) {
        if (str_contains($rel, ltrim($fragment, '/'))) {
            return true;
        }
    }

    return false;
}

function audit_i18n_strip_quotes(string $literal): string
{
    if ($literal === '') {
        return '';
    }
    $q = $literal[0];
    if (($q === "'" || $q === '"') && str_ends_with($literal, $q)) {
        return stripcslashes(substr($literal, 1, -1));
    }

    return stripcslashes($literal);
}

function audit_i18n_line_has_gettext(string $line): bool
{
    return preg_match('/\b(?:_|gettext|ngettext|dgettext)\s*\(/', $line) === 1;
}

function audit_i18n_line_is_comment_only(string $trimmed): bool
{
    return str_starts_with($trimmed, '//')
        || str_starts_with($trimmed, '#')
        || str_starts_with($trimmed, '*')
        || str_starts_with($trimmed, '/*');
}

function audit_i18n_looks_user_facing(string $text, int $minLength): bool
{
    $text = trim($text);
    if (mb_strlen($text) < $minLength) {
        return false;
    }
    if (!preg_match('/[a-zA-ZáéíóúñÁÉÍÓÚÑüÜ]/u', $text)) {
        return false;
    }

    // Identificadores técnicos (snake_case, camelCase sin espacios).
    if (preg_match('/^[a-z][a-z0-9_]*$/i', $text) && !str_contains($text, '_')) {
        return false;
    }
    if (preg_match('/^[a-z]+(?:[A-Z][a-z0-9]+)+$/', $text)) {
        return false;
    }

    // Rutas, extensiones, URLs.
    if (preg_match('#^(?:https?://|/|\.\./|[a-z]:\\\\)#i', $text)) {
        return false;
    }
    if (preg_match('/\.(?:php|js|css|html|json|xml|sql|po|pot|map|svg|png|jpg|gif|woff2?)$/i', $text)) {
        return false;
    }

    // SQL / consultas.
    if (preg_match('/\b(?:SELECT|INSERT|UPDATE|DELETE|FROM|WHERE|JOIN)\b/i', $text)) {
        return false;
    }

    // Comandos shell / validación interna en inglés.
    if (preg_match('/\bcommand -v\b/', $text)) {
        return false;
    }
    if (preg_match('/\b(?:Invalid|does not allow|was not created|not found|not valid)\b/i', $text)) {
        return false;
    }
    if (preg_match('/^<%s>/', $text)) {
        return false;
    }

    // Solo formato / números / puntuación.
    if (preg_match('/^[%\s\d\-:.,\/\\\[\]{}()]+$/', $text)) {
        return false;
    }
    if (preg_match('/^%[-+0-9*.]*[sdufcoxXbeEgG%]$/', $text)) {
        return false;
    }

    if (preg_match('/^<script\b/i', $text)) {
        return false;
    }
    if (preg_match('/\b(?:AND|OR)\s*[=:]/', $text)) {
        return false;
    }

    // HTML sin texto visible.
    if (str_contains($text, '<')) {
        $plain = trim(strip_tags($text));
        if ($plain === '' || !preg_match('/[a-zA-ZáéíóúñÁÉÍÓÚÑüÜ]{3,}/u', $plain)) {
            return false;
        }
    }

    // Mensajes de depuración / trazas internas.
    if (preg_match('/\b(?:__FILE__|__LINE__|__METHOD__|var_dump|print_r|Exception)\b/', $text)) {
        return false;
    }
    if (preg_match('/\b(?:id_nom|id_sel|no encontrado en)\b/i', $text) && preg_match('/__FILE__|:\s*line\b/i', $text)) {
        return false;
    }

    // Códigos técnicos cortos (UTF-8, JSON, etc.).
    if (preg_match('/^[A-Z0-9][A-Z0-9._-]{0,12}$/', $text)) {
        return false;
    }
    if (preg_match('/^[a-z][a-z0-9_]{0,30}$/i', $text) && !preg_match('/[áéíóúñÁÉÍÓÚÑ]/u', $text)) {
        return false;
    }

    return true;
}

function audit_i18n_looks_user_facing_sprintf(string $text, int $minLength): bool
{
    if (!audit_i18n_looks_user_facing($text, $minLength)) {
        return false;
    }

    // Plantillas con solo placeholders y palabras técnicas en inglés.
    $withoutPlaceholders = preg_replace('/%[-+0-9*.]*[sdufcoxXbeEgG%]/', '', $text) ?? $text;
    $withoutPlaceholders = trim($withoutPlaceholders);
    if ($withoutPlaceholders !== '' && !preg_match('/[áéíóúñÁÉÍÓÚÑüÜ]/u', $withoutPlaceholders)) {
        if (!preg_match('/\b(?:debe|debes|error|aviso|seguro|guardar|eliminar|seleccion|ningun|ningún|ninguna)\b/iu', $withoutPlaceholders)) {
            return false;
        }
    }

    return true;
}

/**
 * @return list<array{category: string, line: int, text: string, snippet: string}>
 */
function audit_i18n_analyze_file(string $abs, int $minLength, array $uiArrayKeys): array
{
    $content = file_get_contents($abs);
    if ($content === false || $content === '') {
        return [];
    }

    $issues = [];
    $lines = preg_split('/\R/', $content);
    if (!is_array($lines)) {
        return [];
    }

    $uiKeyPattern = implode('|', array_map(static fn (string $k): string => preg_quote($k, '/'), $uiArrayKeys));

    foreach ($lines as $i => $line) {
        $lineNo = $i + 1;
        $trimmed = ltrim($line);
        if ($trimmed === '' || audit_i18n_line_is_comment_only($trimmed)) {
            continue;
        }

        $checks = [
            [
                'category' => 'echo_print',
                'regex' => '/\b(?:echo|print)\s+((?:\'(?:\\\\.|[^\'])*\'|"(?:\\\\.|[^"])*")(?:\s*\.\s*(?:\'(?:\\\\.|[^\'])*\'|"(?:\\\\.|[^"])*"|[^;]+))*)+/',
            ],
            [
                'category' => 'sprintf_printf',
                'regex' => '/(?<![_\w])(?:sprintf|printf)\s*\(\s*(\'(?:\\\\.|[^\'])*\'|"(?:\\\\.|[^"])*")/',
            ],
            [
                'category' => 'array_ui_key',
                'regex' => '/[\'"](?:' . $uiKeyPattern . ')[\'"]\s*=>\s*(\'(?:\\\\.|[^\'])*\'|"(?:\\\\.|[^"])*")/',
            ],
            [
                'category' => 'js_alert',
                'regex' => '/\balert\s*\(\s*(\'(?:\\\\.|[^\'])*\'|"(?:\\\\.|[^"])*")/',
            ],
            [
                'category' => 'html_title',
                'regex' => '/<title[^>]*>\s*([^<{\$][^<]{2,}?)\s*<\/title>/iu',
            ],
        ];

        foreach ($checks as $check) {
            if (!preg_match_all($check['regex'], $line, $matches, PREG_OFFSET_CAPTURE)) {
                continue;
            }

            foreach ($matches[1] as $match) {
                $raw = $match[0];
                if ($raw === '') {
                    continue;
                }

                if ($check['category'] === 'echo_print') {
                    if (audit_i18n_line_has_gettext($line)) {
                        continue;
                    }
                    if (!preg_match_all('/\'(?:\\\\.|[^\'])*\'|"(?:\\\\.|[^"])*"/', $raw, $literals)) {
                        continue;
                    }
                    foreach ($literals[0] as $literal) {
                        $text = audit_i18n_strip_quotes($literal);
                        if (str_contains($text, '$') && !preg_match('/>([^<$]+)</', $text)) {
                            continue;
                        }
                        if (!audit_i18n_looks_user_facing($text, $minLength)) {
                            continue;
                        }
                        $issues[] = [
                            'category' => $check['category'],
                            'line' => $lineNo,
                            'text' => $text,
                            'snippet' => trim($line),
                        ];
                    }
                    continue;
                }

                if ($check['category'] === 'sprintf_printf' || $check['category'] === 'array_ui_key' || $check['category'] === 'js_alert') {
                    if ($check['category'] === 'js_alert' && audit_i18n_line_has_gettext($line)) {
                        continue;
                    }
                    $text = audit_i18n_strip_quotes($raw);
                    $looksFacing = $check['category'] === 'sprintf_printf'
                        ? audit_i18n_looks_user_facing_sprintf($text, $minLength)
                        : audit_i18n_looks_user_facing($text, $minLength);
                    if (!$looksFacing) {
                        continue;
                    }
                    $issues[] = [
                        'category' => $check['category'],
                        'line' => $lineNo,
                        'text' => $text,
                        'snippet' => trim($line),
                    ];
                    continue;
                }

                if ($check['category'] === 'html_title') {
                    $text = html_entity_decode(trim($raw), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    if (!audit_i18n_looks_user_facing($text, $minLength)) {
                        continue;
                    }
                    if (audit_i18n_line_has_gettext($line)) {
                        continue;
                    }
                    $issues[] = [
                        'category' => $check['category'],
                        'line' => $lineNo,
                        'text' => $text,
                        'snippet' => trim($line),
                    ];
                }
            }
        }
    }

    // Bloques HTML fuera de PHP (cierre/apertura de etiquetas PHP alternas, sin script).
    if (preg_match_all('/\?>\s*(.*?)\s*<\?php/s', $content, $htmlBlocks, PREG_OFFSET_CAPTURE)) {
        foreach ($htmlBlocks[1] as $block) {
            $segment = $block[0];
            $offset = $block[1];
            $lineNo = substr_count(substr($content, 0, $offset), "\n") + 1;

            if (preg_match('/<script\b/i', $segment)) {
                continue;
            }

            if (!preg_match_all('/>([^<{$][^<]{2,})</u', $segment, $textMatches)) {
                continue;
            }
            foreach ($textMatches[1] as $textRaw) {
                $text = trim(html_entity_decode($textRaw, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                if (str_contains($text, '<?=') || str_contains($text, '<?php')) {
                    continue;
                }
                if (preg_match('/\b(?:classList|function|onclick|addEventListener|querySelector)\b/', $text)) {
                    continue;
                }
                if (!audit_i18n_looks_user_facing($text, $minLength)) {
                    continue;
                }
                $issues[] = [
                    'category' => 'html_inline',
                    'line' => $lineNo,
                    'text' => $text,
                    'snippet' => trim(preg_replace('/\s+/', ' ', $textRaw) ?? $textRaw),
                ];
            }
        }
    }

    return $issues;
}

$allIssues = [];

foreach (audit_i18n_scan_files($root, $scanRoots, $extraFiles, $pathFilter) as $abs) {
    if (audit_i18n_should_skip_file($abs, $root, $skipPathFragments)) {
        continue;
    }

    $issues = audit_i18n_analyze_file($abs, $minLength, $uiArrayKeys);
    if ($issues === []) {
        continue;
    }

    $rel = str_replace($root . '/', '', $abs);
    foreach ($issues as $issue) {
        $allIssues[] = [
            'file' => $rel,
            'category' => $issue['category'],
            'line' => $issue['line'],
            'text' => $issue['text'],
            'snippet' => $issue['snippet'],
        ];
    }
}

usort(
    $allIssues,
    static fn (array $a, array $b): int => [$a['file'], $a['line'], $a['category']] <=> [$b['file'], $b['line'], $b['category']]
);

if ($json) {
    echo json_encode(['untranslated_strings' => $allIssues], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n";
} else {
    $byFile = [];
    foreach ($allIssues as $issue) {
        $byFile[$issue['file']][] = $issue;
    }

    foreach ($byFile as $file => $issues) {
        fwrite(STDOUT, $file . "\n");
        foreach ($issues as $issue) {
            $preview = mb_strlen($issue['text']) > 72
                ? mb_substr($issue['text'], 0, 69) . '...'
                : $issue['text'];
            fwrite(STDOUT, '  L' . $issue['line'] . ' [' . $issue['category'] . '] ' . $preview . "\n");
        }
        fwrite(STDOUT, "\n");
    }

    $byCategory = [];
    foreach ($allIssues as $issue) {
        $byCategory[$issue['category']] = ($byCategory[$issue['category']] ?? 0) + 1;
    }
    ksort($byCategory);

    fwrite(STDOUT, "Total: " . count($allIssues) . " cadena(s) en " . count($byFile) . " fichero(s)\n");
    foreach ($byCategory as $category => $count) {
        fwrite(STDOUT, "  - $category: $count\n");
    }
}

exit(count($allIssues) > 0 ? 1 : 0);
