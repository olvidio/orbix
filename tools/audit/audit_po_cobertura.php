#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Compara las cadenas gettext del código con languages/es_ES.pot y los .po.
 *
 * El español de la interfaz es el msgid. ca/en/de/it deben tener msgstr.
 * Una entrada fuzzy no cuenta como traducción cerrada.
 *
 * Uso:
 *   php tools/audit/audit_po_cobertura.php
 *   php tools/audit/audit_po_cobertura.php --json
 *   php tools/audit/audit_po_cobertura.php --list-fuzzy=en_US.UTF-8
 */

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "Solo CLI.\n");
    exit(1);
}

$root = dirname(__DIR__, 2);
$json = in_array('--json', $argv, true);
$listFuzzy = null;
foreach ($argv as $arg) {
    if (str_starts_with($arg, '--list-fuzzy=')) {
        $listFuzzy = substr($arg, 13);
    }
}

/** @var array<string, string> */
$catalogs = [
    'ca_ES.UTF-8' => $root . '/languages/ca_ES.UTF-8/LC_MESSAGES/orbix.po',
    'en_US.UTF-8' => $root . '/languages/en_US.UTF-8/LC_MESSAGES/orbix.po',
    'de_DE.UTF-8' => $root . '/languages/de_DE.UTF-8/LC_MESSAGES/orbix.po',
    'it_IT.UTF-8' => $root . '/languages/it_IT.UTF-8/LC_MESSAGES/orbix.po',
];

/**
 * @return array{msgid: string, msgstr: string, fuzzy: bool, line: int, refs: list<string>}
 */
function audit_po_new_entry(): array
{
    return ['msgid' => '', 'msgstr' => '', 'fuzzy' => false, 'line' => 0, 'refs' => [], 'seen' => false];
}

function audit_po_unquote(string $s): string
{
    $s = trim($s);
    if ($s === '' || !str_starts_with($s, '"') || !str_ends_with($s, '"')) {
        return '';
    }
    $s = substr($s, 1, -1);
    $out = '';
    $len = strlen($s);
    for ($i = 0; $i < $len; $i++) {
        if ($s[$i] === '\\' && $i + 1 < $len) {
            $n = $s[++$i];
            $out .= match ($n) {
                'n' => "\n",
                't' => "\t",
                'r' => "\r",
                '"', '\\', "'" => $n,
                default => $n,
            };
            continue;
        }
        $out .= $s[$i];
    }

    return $out;
}

/**
 * @return list<array{msgid: string, msgstr: string, fuzzy: bool, line: int, refs: list<string>}>
 */
function audit_po_parse(string $path): array
{
    $lines = file($path, FILE_IGNORE_NEW_LINES);
    if ($lines === false) {
        throw new RuntimeException("No se puede leer $path");
    }
    $entries = [];
    $entry = audit_po_new_entry();
    $field = null;
    $obsolete = false;

    $flush = static function () use (&$entries, &$entry, &$field, &$obsolete): void {
        if (!$obsolete && $entry['seen'] && $entry['msgid'] !== '') {
            unset($entry['seen']);
            $entries[] = $entry;
        }
        $entry = audit_po_new_entry();
        $field = null;
        $obsolete = false;
    };

    foreach ($lines as $idx => $line) {
        if (str_starts_with($line, '#~')) {
            $obsolete = true;
            $line = ltrim(substr($line, 2));
        }
        if ($line === '') {
            $flush();
            continue;
        }
        if (str_starts_with($line, '#')) {
            if (str_starts_with($line, '#,')) {
                if (preg_match('/\bfuzzy\b/', $line) === 1) {
                    $entry['fuzzy'] = true;
                }
            } elseif (str_starts_with($line, '#:')) {
                $entry['refs'][] = trim(substr($line, 2));
            }
            continue;
        }
        if (str_starts_with($line, 'msgid_plural ') || str_starts_with($line, 'msgctxt ')) {
            continue;
        }
        if (str_starts_with($line, 'msgid ')) {
            if ($entry['seen']) {
                $flush();
            }
            $entry['seen'] = true;
            $entry['line'] = $idx + 1;
            $field = 'msgid';
            $entry['msgid'] = audit_po_unquote(substr($line, 6));
            continue;
        }
        if (str_starts_with($line, 'msgstr')) {
            $sp = strpos($line, ' ');
            $field = 'msgstr';
            $entry['msgstr'] .= audit_po_unquote($sp === false ? '' : substr($line, $sp + 1));
            continue;
        }
        if ($field !== null && str_starts_with($line, '"')) {
            $entry[$field] .= audit_po_unquote($line);
        }
    }
    $flush();

    return $entries;
}

/**
 * @return list<string>
 */
function audit_po_specs(string $s): array
{
    $s = str_replace('%%', '', $s);
    if (!preg_match_all('/%(\d+\$)?[-+0# ]*(?:\d+|\*)?(?:\.(?:\d+|\*))?l{0,2}([bcdeEfFgGosuxX])/', $s, $matches, PREG_SET_ORDER)) {
        return [];
    }
    $out = [];
    $auto = 1;
    foreach ($matches as $match) {
        $idx = ($match[1] ?? '') !== '' ? (int) $match[1] : $auto++;
        $out[] = $idx . $match[2];
    }
    sort($out);

    return $out;
}

/**
 * @return array<string, list<string>>
 */
function audit_po_code_msgids(string $root): array
{
    $list = tempnam(sys_get_temp_dir(), 'orbix-php-');
    $pot = tempnam(sys_get_temp_dir(), 'orbix-pot-');
    if ($list === false || $pot === false) {
        throw new RuntimeException('No se pudo crear un temporal');
    }
    $files = [];
    foreach (['frontend', 'src', 'scripts', 'public'] as $dir) {
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
            if (str_contains($path, '/vendor/') || str_contains($path, '/tests/') || str_contains($path, '/libs/')) {
                continue;
            }
            $ext = $file->getExtension();
            if (in_array($ext, ['php', 'phtml', 'inc'], true)) {
                $files[] = str_replace($root . '/', '', $path);
            }
        }
    }
    file_put_contents($list, implode("\n", $files) . "\n");
    $cmd = 'cd ' . escapeshellarg($root)
        . ' && xgettext --from-code=UTF-8 --language=PHP -k_ -kgettext -kngettext:1,2 -kdgettext:2 --no-wrap --files-from='
        . escapeshellarg($list) . ' -o ' . escapeshellarg($pot) . ' 2>/dev/null';
    exec($cmd, $unused, $code);
    if ($code !== 0 || !is_file($pot)) {
        @unlink($list);
        @unlink($pot);
        throw new RuntimeException('xgettext ha fallado');
    }
    $byMsgid = [];
    foreach (audit_po_parse($pot) as $entry) {
        $byMsgid[$entry['msgid']] = $entry['refs'];
    }
    @unlink($list);
    @unlink($pot);

    foreach (['frontend', 'src'] as $dir) {
        $base = $root . '/' . $dir;
        if (!is_dir($base)) {
            continue;
        }
        $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base, FilesystemIterator::SKIP_DOTS));
        foreach ($rii as $file) {
            if (!$file->isFile() || !str_ends_with($file->getFilename(), '.twig')) {
                continue;
            }
            $content = file_get_contents($file->getPathname());
            if ($content === false || !str_contains($content, '|trans')) {
                continue;
            }
            if (!preg_match_all('/\{\{\s*([\'"])((?:\\\\.|(?!\1).)*)\1\s*\|\s*trans\b/', $content, $matches, PREG_OFFSET_CAPTURE)) {
                continue;
            }
            $rel = str_replace($root . '/', '', $file->getPathname());
            foreach ($matches[2] as $i => $cap) {
                $quote = $matches[1][$i][0];
                $inner = $cap[0];
                $text = $quote === "'"
                    ? str_replace(["\\'", '\\\\'], ["'", '\\'], $inner)
                    : stripcslashes($inner);
                if ($text === '') {
                    continue;
                }
                $line = substr_count(substr($content, 0, $cap[1]), "\n") + 1;
                $byMsgid[$text] ??= [];
                $byMsgid[$text][] = $rel . ':' . $line;
            }
        }
    }

    return $byMsgid;
}

function audit_po_msgfmt(string $path): string
{
    $cmd = 'msgfmt --check --statistics -o /dev/null ' . escapeshellarg($path) . ' 2>&1';
    exec($cmd, $out, $code);
    $text = trim(implode("\n", $out));

    return ($code === 0 ? 'ok' : 'error') . ($text !== '' ? ' — ' . $text : '');
}

$code = audit_po_code_msgids($root);
$potEntries = audit_po_parse($root . '/languages/es_ES.pot');
$pot = [];
foreach ($potEntries as $entry) {
    $pot[$entry['msgid']] = $entry;
}

$onlyCode = array_diff_key($code, $pot);
ksort($onlyCode);
$onlyPot = array_diff_key($pot, $code);
ksort($onlyPot);

$report = [
    'code_msgids' => count($code),
    'pot_msgids' => count($pot),
    'only_code' => [],
    'only_pot' => [],
    'languages' => [],
];

foreach ($onlyCode as $msgid => $refs) {
    $report['only_code'][] = ['msgid' => $msgid, 'refs' => $refs];
}
foreach ($onlyPot as $msgid => $entry) {
    $report['only_pot'][] = ['msgid' => $msgid, 'refs' => $entry['refs'], 'line' => $entry['line']];
}

$placeholderTotal = 0;
foreach ($catalogs as $locale => $path) {
    $entries = audit_po_parse($path);
    $byId = [];
    $translated = 0;
    $fuzzy = 0;
    $empty = 0;
    $identical = 0;
    $apostrophes = 0;
    $placeholders = [];
    $fuzzyRows = [];
    $emptyRows = [];
    foreach ($entries as $entry) {
        $byId[$entry['msgid']] = $entry;
        if ($entry['msgstr'] === '') {
            $empty++;
            $emptyRows[] = $entry;
            continue;
        }
        if (str_contains($entry['msgstr'], "'")) {
            $apostrophes++;
        }
        if ($entry['fuzzy']) {
            $fuzzy++;
            $fuzzyRows[] = $entry;
        } else {
            $translated++;
        }
        if ($entry['msgstr'] === $entry['msgid'] && preg_match('/[áéíóúñÁÉÍÓÚÑ¿¡]|\b(?:que|para|los|las|del|una|con)\b/u', $entry['msgid']) === 1) {
            $identical++;
        }
        $srcSpecs = audit_po_specs($entry['msgid']);
        $dstSpecs = audit_po_specs($entry['msgstr']);
        if ($srcSpecs !== [] && $srcSpecs !== $dstSpecs) {
            $placeholders[] = [
                'msgid' => $entry['msgid'],
                'msgstr' => $entry['msgstr'],
                'line' => $entry['line'],
                'fuzzy' => $entry['fuzzy'],
            ];
        }
    }
    $missingFromPo = [];
    foreach ($pot as $msgid => $entry) {
        if (!isset($byId[$msgid])) {
            $missingFromPo[] = $msgid;
        }
    }
    $placeholderTotal += count($placeholders);
    $report['languages'][$locale] = [
        'msgfmt' => audit_po_msgfmt($path),
        'entries' => count($entries),
        'translated' => $translated,
        'fuzzy' => $fuzzy,
        'untranslated' => $empty,
        'missing_vs_pot' => count($missingFromPo),
        'apostrophes' => $apostrophes,
        'identical_spanish' => $identical,
        'placeholder_mismatches' => $placeholders,
        'placeholder_stable' => count(array_filter($placeholders, static fn (array $row): bool => $row['fuzzy'] === false)),
        'untranslated_rows' => array_map(
            static fn (array $e): array => ['line' => $e['line'], 'msgid' => $e['msgid'], 'refs' => $e['refs']],
            $emptyRows
        ),
        'fuzzy_rows' => $listFuzzy === $locale
            ? array_map(
                static fn (array $e): array => ['line' => $e['line'], 'msgid' => $e['msgid'], 'msgstr' => $e['msgstr']],
                $fuzzyRows
            )
            : [],
    ];
}

$rawBackslashQuote = 0;
$caPath = $catalogs['ca_ES.UTF-8'];
$caRaw = file_get_contents($caPath) ?: '';
$rawBackslashQuote = preg_match_all('/\\\\\'/', $caRaw) ?: 0;

if ($json) {
    echo json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n";
} else {
    fwrite(STDOUT, "Fase 2 — cadenas traducidas\n\n");
    fwrite(STDOUT, "msgid en código (PHP + Twig |trans): " . count($code) . "\n");
    fwrite(STDOUT, "msgid en languages/es_ES.pot: " . count($pot) . "\n");
    fwrite(STDOUT, "En código y no en la plantilla: " . count($onlyCode) . "\n");
    fwrite(STDOUT, "En la plantilla y no en el código actual: " . count($onlyPot) . "\n\n");

    if ($onlyCode !== []) {
        fwrite(STDOUT, "Sin entrada en es_ES.pot (no se traducen en ningún idioma):\n");
        foreach ($report['only_code'] as $row) {
            $ref = $row['refs'][0] ?? '';
            $preview = str_replace("\n", ' ', $row['msgid']);
            if (mb_strlen($preview) > 110) {
                $preview = mb_substr($preview, 0, 107) . '...';
            }
            fwrite(STDOUT, "  - {$preview}");
            if ($ref !== '') {
                fwrite(STDOUT, "  [{$ref}]");
            }
            fwrite(STDOUT, "\n");
        }
        fwrite(STDOUT, "\n");
    }

    if ($onlyPot !== []) {
        fwrite(STDOUT, "Obsoletas en la plantilla (ya no están en el código escaneado):\n");
        foreach ($report['only_pot'] as $row) {
            $preview = str_replace("\n", ' ', $row['msgid']);
            if (mb_strlen($preview) > 110) {
                $preview = mb_substr($preview, 0, 107) . '...';
            }
            fwrite(STDOUT, "  - {$preview}\n");
        }
        fwrite(STDOUT, "\n");
    }

    fwrite(STDOUT, "Catálogos (fuzzy = dudosa, no cerrada; el español de pantalla es el msgid):\n");
    foreach ($report['languages'] as $locale => $lang) {
        fwrite(STDOUT, "  {$locale}\n");
        fwrite(STDOUT, "    msgfmt: {$lang['msgfmt']}\n");
        fwrite(STDOUT, "    traducidas: {$lang['translated']}  fuzzy: {$lang['fuzzy']}  vacías: {$lang['untranslated']}  faltan vs pot: {$lang['missing_vs_pot']}\n");
        fwrite(STDOUT, "    msgstr con apóstrofo ': {$lang['apostrophes']}  iguales al español (con marca hispana): {$lang['identical_spanish']}\n");
        fwrite(STDOUT, "    placeholders % distintos: " . count($lang['placeholder_mismatches']) . " (en entradas no fuzzy: {$lang['placeholder_stable']})\n");
        if ($locale === 'ca_ES.UTF-8') {
            fwrite(STDOUT, "    secuencias \\' en el .po: {$rawBackslashQuote}\n");
        }
        foreach ($lang['untranslated_rows'] as $row) {
            $preview = str_replace("\n", ' ', $row['msgid']);
            if (mb_strlen($preview) > 100) {
                $preview = mb_substr($preview, 0, 97) . '...';
            }
            fwrite(STDOUT, "    vacía L{$row['line']}: {$preview}\n");
        }
        foreach (array_slice($lang['placeholder_mismatches'], 0, 15) as $row) {
            fwrite(STDOUT, "    % L{$row['line']}: {$row['msgid']}  =>  {$row['msgstr']}\n");
        }
        if (count($lang['placeholder_mismatches']) > 15) {
            fwrite(STDOUT, "    … y " . (count($lang['placeholder_mismatches']) - 15) . " más (ver --json)\n");
        }
        if ($listFuzzy === $locale) {
            foreach ($lang['fuzzy_rows'] as $row) {
                fwrite(STDOUT, "    fuzzy L{$row['line']}: {$row['msgid']}  =>  {$row['msgstr']}\n");
            }
        }
    }
    fwrite(STDOUT, "\nFuzzy completo: php tools/audit/audit_po_cobertura.php --list-fuzzy=en_US.UTF-8\n");
}

$fail = count($onlyCode) > 0 || $placeholderTotal > 0;
foreach ($report['languages'] as $lang) {
    if ($lang['untranslated'] > 0 || $lang['fuzzy'] > 0 || $lang['missing_vs_pot'] > 0 || str_starts_with($lang['msgfmt'], 'error')) {
        $fail = true;
    }
}

exit($fail ? 1 : 0);
