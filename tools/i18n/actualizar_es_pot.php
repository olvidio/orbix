#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Regenera languages/es_ES.pot desde el código.
 *
 * Poedit solo ve _('…') en PHP. Este script añade también {{ "…"|trans }} de Twig
 * y los .php de scripts/.
 *
 * Uso: php tools/i18n/actualizar_es_pot.php
 */

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "Solo CLI.\n");
    exit(1);
}

$root = dirname(__DIR__, 2);
$potPath = $root . '/languages/es_ES.pot';
$tmp = sys_get_temp_dir() . '/orbix-pot-' . getmypid();
if (!mkdir($tmp) && !is_dir($tmp)) {
    fwrite(STDERR, "No se pudo crear el temporal.\n");
    exit(1);
}

$phpList = $tmp . '/php.txt';
$phpPot = $tmp . '/php.pot';
$twigPhp = $tmp . '/twig.php';
$twigPot = $tmp . '/twig.pot';
$merged = $tmp . '/merged.pot';

/** @var list<string> */
$phpFiles = [];
foreach (['frontend', 'src', 'public', 'scripts'] as $dir) {
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
        if (str_contains($path, '/vendor/') || str_contains($path, '/libs/') || str_contains($path, '/node_modules/') || str_contains($path, '/tests/')) {
            continue;
        }
        $ext = $file->getExtension();
        if (in_array($ext, ['php', 'phtml', 'inc'], true)) {
            $phpFiles[] = str_replace($root . '/', '', $path);
        }
    }
}
sort($phpFiles);
file_put_contents($phpList, implode("\n", $phpFiles) . "\n");

$xgettext = 'xgettext --from-code=UTF-8 --language=PHP --no-wrap'
    . ' -k_ -kgettext -kngettext:1,2 -kdgettext:2'
    . ' --package-name=orbix --package-version=v1.0';

$cmd = 'cd ' . escapeshellarg($root) . ' && ' . $xgettext
    . ' --files-from=' . escapeshellarg($phpList)
    . ' -o ' . escapeshellarg($phpPot);
exec($cmd . ' 2>' . escapeshellarg($tmp . '/xgettext-php.err'), $unused, $code);
if ($code !== 0 || !is_file($phpPot)) {
    fwrite(STDERR, "xgettext (PHP) ha fallado.\n");
    fwrite(STDERR, (string) file_get_contents($tmp . '/xgettext-php.err'));
    exit(1);
}

/** @var array<int, string> $twigLineRef */
$twigLineRef = [];
$beforeIds = pot_msgid_set($potPath);
$twigBody = "<?php\n";
$line = 2;
foreach (['frontend', 'src'] as $dir) {
    $base = $root . '/' . $dir;
    if (!is_dir($base)) {
        continue;
    }
    $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base, FilesystemIterator::SKIP_DOTS));
    $twigFiles = [];
    foreach ($rii as $file) {
        if ($file->isFile() && str_ends_with($file->getFilename(), '.twig')) {
            $twigFiles[] = $file->getPathname();
        }
    }
    sort($twigFiles);
    foreach ($twigFiles as $abs) {
        $content = file_get_contents($abs);
        if ($content === false || !str_contains($content, '|trans')) {
            continue;
        }
        if (!preg_match_all('/\{\{\s*([\'"])((?:\\\\.|(?!\1).)*)\1\s*\|\s*trans\b/', $content, $matches, PREG_OFFSET_CAPTURE)) {
            continue;
        }
        $rel = str_replace($root . '/', '', $abs);
        foreach ($matches[2] as $i => $cap) {
            $text = pot_decode_quoted($cap[0], $matches[1][$i][0]);
            if ($text === '') {
                continue;
            }
            $srcLine = substr_count(substr($content, 0, $cap[1]), "\n") + 1;
            $stmt = '_(' . var_export($text, true) . ");\n";
            $twigBody .= $stmt;
            $twigLineRef[$line] = $rel . ':' . $srcLine;
            $line += substr_count($stmt, "\n");
        }
    }
}
file_put_contents($twigPhp, $twigBody);

exec(
    'cd ' . escapeshellarg($tmp) . ' && ' . $xgettext . ' -o ' . escapeshellarg($twigPot) . ' twig.php 2>' . escapeshellarg($tmp . '/xgettext-twig.err'),
    $unused,
    $twigCode
);
if ($twigCode !== 0 || !is_file($twigPot)) {
    fwrite(STDERR, "xgettext (Twig) ha fallado.\n");
    fwrite(STDERR, (string) file_get_contents($tmp . '/xgettext-twig.err'));
    exit(1);
}

$twigRaw = file_get_contents($twigPot);
if ($twigRaw === false) {
    fwrite(STDERR, "No se lee el pot de Twig.\n");
    exit(1);
}
$twigBase = basename($twigPhp);
$twigRaw = preg_replace_callback(
    '/' . preg_quote($twigBase, '/') . ':(\d+)/',
    static function (array $m) use ($twigLineRef): string {
        return $twigLineRef[(int) $m[1]] ?? ('twig:' . $m[1]);
    },
    $twigRaw
);
file_put_contents($twigPot, $twigRaw);

exec(
    'msgcat --no-wrap --use-first -o ' . escapeshellarg($merged) . ' ' . escapeshellarg($phpPot) . ' ' . escapeshellarg($twigPot),
    $unused,
    $catCode
);
if ($catCode !== 0 || !is_file($merged)) {
    fwrite(STDERR, "msgcat ha fallado.\n");
    exit(1);
}

$mergedRaw = file_get_contents($merged);
if ($mergedRaw === false) {
    fwrite(STDERR, "No se lee el pot unido.\n");
    exit(1);
}
$body = pot_translatable_body($mergedRaw);
if ($body === '') {
    fwrite(STDERR, "El pot unido no tiene entradas.\n");
    exit(1);
}
$date = trim((string) shell_exec('date +%Y-%m-%d\ %H:%M%z'));
$header = <<<POT
msgid ""
msgstr ""
"Project-Id-Version: orbix v1.0\\n"
"POT-Creation-Date: {$date}\\n"
"PO-Revision-Date: 2018-06-08 09:00+0200\\n"
"Last-Translator: Daniel Serrabou <dani@moneders.net>\\n"
"Language-Team: \\n"
"Language: es_ES\\n"
"MIME-Version: 1.0\\n"
"Content-Type: text/plain; charset=UTF-8\\n"
"Content-Transfer-Encoding: 8bit\\n"
"Plural-Forms: nplurals=2; plural=(n != 1);\\n"
"X-Generator: xgettext\\n"
"X-Poedit-Basepath: ..\\n"
"X-Poedit-KeywordsList: _\\n"
"X-Poedit-SourceCharset: UTF-8\\n"
"X-Poedit-SearchPath-0: frontend\\n"
"X-Poedit-SearchPath-1: src\\n"
"X-Poedit-SearchPath-2: public\\n"
"X-Poedit-SearchPath-3: scripts\\n"

POT;
$header .= "\n";
file_put_contents($potPath, $header . $body);

exec('msgfmt --check -o /dev/null ' . escapeshellarg($potPath) . ' 2>&1', $fmtOut, $fmtCode);
if ($fmtCode !== 0) {
    fwrite(STDERR, "msgfmt --check ha fallado.\n");
    fwrite(STDERR, implode("\n", $fmtOut) . "\n");
    exit(1);
}

$afterIds = pot_msgid_set($potPath);
$added = array_diff($afterIds, $beforeIds);
$removed = array_diff($beforeIds, $afterIds);
fwrite(STDOUT, "Escrito languages/es_ES.pot\n");
fwrite(STDOUT, 'PHP: ' . count($phpFiles) . " ficheros; Twig |trans: " . count($twigLineRef) . " apariciones\n");
fwrite(STDOUT, 'msgid: ' . count($beforeIds) . ' → ' . count($afterIds) . '  (+' . count($added) . ' / -' . count($removed) . ")\n");
fwrite(STDOUT, implode(' ', $fmtOut) . "\n");
$errPhp = trim((string) file_get_contents($tmp . '/xgettext-php.err'));
if ($errPhp !== '') {
    fwrite(STDOUT, "Avisos xgettext:\n{$errPhp}\n");
}

function pot_translatable_body(string $raw): string
{
    $lines = preg_split("/\n/", $raw) ?: [];
    $blocks = [];
    $current = [];
    $flush = static function () use (&$blocks, &$current): void {
        if ($current === []) {
            return;
        }
        $text = implode("\n", $current);
        if (preg_match('/^msgid "(.*)"$/m', $text, $match) === 1 && $match[1] !== '') {
            $blocks[] = $text;
        } elseif (preg_match('/^msgid ""\n"/m', $text) === 1) {
            $blocks[] = $text;
        }
        $current = [];
    };
    foreach ($lines as $line) {
        if ($line === '') {
            $flush();
            continue;
        }
        $current[] = $line;
    }
    $flush();

    return $blocks === [] ? '' : implode("\n\n", $blocks) . "\n";
}

/**
 * @return list<string>
 */
function pot_msgid_set(string $path): array
{
    if (!is_file($path)) {
        return [];
    }
    $lines = file($path, FILE_IGNORE_NEW_LINES);
    if ($lines === false) {
        return [];
    }
    $ids = [];
    $field = null;
    $buf = '';
    $flush = static function () use (&$ids, &$field, &$buf): void {
        if ($field === 'msgid' && $buf !== '') {
            $ids[] = $buf;
        }
        $field = null;
        $buf = '';
    };
    foreach ($lines as $line) {
        if ($line === '' || str_starts_with($line, '#')) {
            if ($line === '') {
                $flush();
            }
            continue;
        }
        if (str_starts_with($line, 'msgid ')) {
            $flush();
            $field = 'msgid';
            $buf = pot_unquote(substr($line, 6));
            continue;
        }
        if (str_starts_with($line, 'msgstr')) {
            $flush();
            continue;
        }
        if ($field === 'msgid' && str_starts_with($line, '"')) {
            $buf .= pot_unquote($line);
        }
    }
    $flush();

    return array_values(array_unique($ids));
}

function pot_unquote(string $s): string
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

function pot_decode_quoted(string $inner, string $quote): string
{
    $decoded = '';
    $len = strlen($inner);
    for ($i = 0; $i < $len; $i++) {
        if ($inner[$i] === '\\' && $i + 1 < $len) {
            $next = $inner[$i + 1];
            if ($quote === "'") {
                if ($next === '\\' || $next === "'") {
                    $decoded .= $next;
                    $i++;
                    continue;
                }
            } elseif ($next === '\\' || $next === '"' || $next === 'n' || $next === 't' || $next === 'r' || $next === '$') {
                $decoded .= match ($next) {
                    'n' => "\n",
                    't' => "\t",
                    'r' => "\r",
                    default => $next,
                };
                $i++;
                continue;
            }
        }
        $decoded .= $inner[$i];
    }

    return $decoded;
}
