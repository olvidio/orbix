<?php

declare(strict_types=1);

/**
 * Busca llamadas a funciones que exigen string cuyo primer argumento (u otros
 * configurados) puede ser null — p. ej. mb_substr($persona->getNom(), 0, 1)
 * en PHP 8.1+ — y opcionalmente añade coalesce a cadena vacía.
 *
 * Alcance por defecto: src/ (excluye libs/, vendor/, scripts de terceros).
 * Solo auto-corrige expresiones seguras: cadenas de llamadas $var->get*() / $this->…
 * sin ?? ni (string) ya presentes.
 *
 * Uso:
 *   php tools/fix/fix_string_functions_null_args.php --dry-run
 *   php tools/fix/fix_string_functions_null_args.php --apply
 *   php tools/fix/fix_string_functions_null_args.php --dry-run --path src/misas
 *   php tools/fix/fix_string_functions_null_args.php --dry-run --include-tests
 */

/** @var array<string, list<int>> índice del argumento que debe ser string */
const STRING_FUNCTIONS = [
    'mb_substr' => [0],
    'mb_strlen' => [0],
    'mb_strtoupper' => [0],
    'mb_strtolower' => [0],
    'substr' => [0],
    'strlen' => [0],
    'trim' => [0],
    'ltrim' => [0],
    'rtrim' => [0],
    'strtoupper' => [0],
    'strtolower' => [0],
    'ucfirst' => [0],
    'ucwords' => [0],
    'htmlspecialchars' => [0],
    'htmlentities' => [0],
    'strip_tags' => [0],
    'nl2br' => [0],
    'md5' => [0],
    'sha1' => [0],
    'crc32' => [0],
    'base64_encode' => [0],
    'urlencode' => [0],
    'rawurlencode' => [0],
    'addslashes' => [0],
    'stripslashes' => [0],
    'str_pad' => [0],
    'wordwrap' => [0],
    'explode' => [1],
    'str_split' => [0],
    'preg_match' => [1],
    'preg_match_all' => [1],
    'preg_replace' => [2],
    'preg_replace_callback' => [2],
    'strpos' => [0, 1],
    'strrpos' => [0, 1],
    'str_contains' => [0, 1],
    'str_starts_with' => [0, 1],
    'str_ends_with' => [0, 1],
    'strcmp' => [0, 1],
    'strcasecmp' => [0, 1],
];

const SKIP_DIR_NAMES = ['vendor', 'node_modules', 'libs'];

function repoRoot(): string
{
    return dirname(__DIR__, 2);
}

function relPath(string $absolute): string
{
    $root = repoRoot() . '/';
    return str_starts_with($absolute, $root) ? substr($absolute, strlen($root)) : $absolute;
}

/** @return list<string> */
function discoverPhpFiles(?string $pathFilter, bool $includeTests): array
{
    $root = repoRoot();
    $bases = [$root . '/src'];
    if ($includeTests) {
        $bases[] = $root . '/tests';
        $bases[] = $root . '/frontend';
    }

    $files = [];
    foreach ($bases as $base) {
        if (!is_dir($base)) {
            continue;
        }
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($base, FilesystemIterator::SKIP_DOTS)
        );
        foreach ($iterator as $fileInfo) {
            if (!$fileInfo->isFile() || $fileInfo->getExtension() !== 'php') {
                continue;
            }
            $path = $fileInfo->getPathname();
            foreach (SKIP_DIR_NAMES as $skip) {
                if (str_contains($path, '/' . $skip . '/')) {
                    continue 2;
                }
            }
            $files[] = $path;
        }
    }

    $files = array_values(array_unique($files));
    sort($files);

    if ($pathFilter === null || $pathFilter === '') {
        return $files;
    }

    $filter = str_replace('\\', '/', trim($pathFilter, '/'));

    return array_values(array_filter(
        $files,
        static fn (string $file): bool => pathMatchesFilter($file, $filter)
    ));
}

function pathMatchesFilter(string $absolutePath, string $filter): bool
{
    $normalized = str_replace('\\', '/', $absolutePath);
    $root = str_replace('\\', '/', repoRoot()) . '/';
    if (str_starts_with($normalized, $root)) {
        $normalized = substr($normalized, strlen($root));
    }
    $filter = trim(str_replace('\\', '/', $filter), '/');

    return $normalized === $filter || str_starts_with($normalized, $filter . '/');
}

function isWhitespace(mixed $token): bool
{
    return is_string($token) && trim($token) === '';
}

function tokenSource(mixed $token): string
{
    return is_array($token) ? $token[1] : (string) $token;
}

/**
 * @param list<mixed> $tokens
 */
function tokensToSource(array $tokens): string
{
    $out = '';
    foreach ($tokens as $token) {
        $out .= tokenSource($token);
    }
    return $out;
}

/**
 * @param list<mixed> $tokens
 * @return array{start: int, end: int}|null
 */
function parseCallArguments(array $tokens, int $openParenIndex): ?array
{
    $depth = 0;
    $args = [];
    $argStart = $openParenIndex + 1;
    $len = count($tokens);

    for ($i = $openParenIndex; $i < $len; $i++) {
        $t = $tokens[$i];
        if ($t === '(') {
            $depth++;
            if ($depth === 1) {
                $argStart = $i + 1;
            }
            continue;
        }
        if ($t === ')') {
            $depth--;
            if ($depth === 0) {
                $args[] = ['start' => $argStart, 'end' => $i];
                return ['args' => $args, 'close' => $i];
            }
            continue;
        }
        if ($t === ',' && $depth === 1) {
            $args[] = ['start' => $argStart, 'end' => $i];
            $argStart = $i + 1;
        }
    }

    return null;
}

function argumentAlreadySafe(string $source): bool
{
    $trimmed = trim($source);
    if ($trimmed === '') {
        return true;
    }
    if (preg_match('/^\(string\)\s/u', $trimmed)) {
        return true;
    }
    if (str_contains($trimmed, '??')) {
        return true;
    }
    if (preg_match('/\?\s*->/', $trimmed)) {
        return true;
    }
    // Literales y gettext: no pueden ser null en tiempo de ejecución
    if (preg_match("/^'[^'\\\\]*(?:\\\\.[^'\\\\]*)*'$/s", $trimmed)
        || preg_match('/^"[^"\\\\]*(?:\\\\.[^"\\\\]*)*"$/s', $trimmed)) {
        return true;
    }
    if (preg_match('/^_?\s*\(\s*["\']/', $trimmed)) {
        return true;
    }
    if (preg_match('/^-?\d+$/', $trimmed)) {
        return true;
    }
    return false;
}

/**
 * @param list<mixed> $argTokens
 */
function argumentTokensAlreadySafe(array $argTokens): bool
{
    $meaningful = [];
    foreach ($argTokens as $t) {
        if (isWhitespace($t)) {
            continue;
        }
        $meaningful[] = $t;
    }
    if ($meaningful === []) {
        return true;
    }
    $first = $meaningful[0];
    if (is_array($first) && $first[0] === T_CONSTANT_ENCAPSED_STRING) {
        return true;
    }
    if (is_array($first) && in_array($first[0], [T_LNUMBER, T_DNUMBER], true)) {
        return true;
    }
    // _("texto") o constantes
    if (is_array($first) && $first[0] === T_STRING && strtolower($first[1]) === '_') {
        return true;
    }
    if ($first === '"' || $first === "'") {
        return true;
    }

    return argumentAlreadySafe(tokensToSource($argTokens));
}

/**
 * Expresión autocorregible: solo llamadas encadenadas desde $var o $this.
 */
function isAutoFixableExpression(string $source): bool
{
    $trimmed = trim($source);
    if (!preg_match('/^(?:\$this|\$[a-zA-Z_][\w]*)(?:->[a-zA-Z_][\w]*\([^)]*\))+$/', $trimmed)) {
        return false;
    }
    return str_contains($trimmed, '->get') || str_contains($trimmed, '->value(');
}

function wrapWithEmptyString(string $source): string
{
    return trim($source) . " ?? ''";
}

/**
 * @return list<array{line: int, func: string, argIndex: int, before: string, after: string, fixable: bool, byteStart: int, byteEnd: int}>
 */
function analyzeFile(string $path): array
{
    $code = file_get_contents($path);
    if ($code === false) {
        throw new RuntimeException("No se puede leer: {$path}");
    }

    $tokens = token_get_all($code);
    $findings = [];
    $len = count($tokens);
    $funcNames = array_map('strtolower', array_keys(STRING_FUNCTIONS));

    for ($i = 0; $i < $len; $i++) {
        $token = $tokens[$i];
        if (!is_array($token) || $token[0] !== T_STRING) {
            continue;
        }

        $funcLower = strtolower($token[1]);
        if (!in_array($funcLower, $funcNames, true)) {
            continue;
        }

        // Evitar Foo\mb_substr — debe ser llamada global o sin namespace en el nombre
        $prev = $i - 1;
        while ($prev >= 0 && isWhitespace($tokens[$prev])) {
            $prev--;
        }
        if ($prev >= 0) {
            $prevToken = $tokens[$prev];
            if ($prevToken === '\\' || (is_array($prevToken) && $prevToken[0] === T_NS_SEPARATOR)) {
                continue;
            }
            if (is_array($prevToken) && in_array($prevToken[0], [T_OBJECT_OPERATOR, T_DOUBLE_COLON, T_NEW, T_FUNCTION], true)) {
                continue;
            }
        }

        $j = $i + 1;
        while ($j < $len && isWhitespace($tokens[$j])) {
            $j++;
        }
        if ($j >= $len || $tokens[$j] !== '(') {
            continue;
        }

        $parsed = parseCallArguments($tokens, $j);
        if ($parsed === null) {
            continue;
        }

        $func = $token[1];
        $argIndexes = STRING_FUNCTIONS[$funcLower];

        foreach ($parsed['args'] as $argPos => $argRange) {
            if (!in_array($argPos, $argIndexes, true)) {
                continue;
            }

            $argTokens = array_slice($tokens, $argRange['start'], $argRange['end'] - $argRange['start']);
            $before = trim(tokensToSource($argTokens));
            if ($before === '' || argumentTokensAlreadySafe($argTokens)) {
                continue;
            }

            $fixable = isAutoFixableExpression($before);
            $after = $fixable ? wrapWithEmptyString($before) : $before;

            $line = $token[2];
            foreach ($argTokens as $at) {
                if (is_array($at)) {
                    $line = $at[2];
                    break;
                }
            }

            $byteStart = $argRange['start'] < $len && is_array($tokens[$argRange['start']])
                ? $tokens[$argRange['start']][1]
                : null;
            // Offset en fichero: reconstruir posición acumulando longitudes
            $offset = 0;
            for ($k = 0; $k < $argRange['start']; $k++) {
                $offset += strlen(tokenSource($tokens[$k]));
            }
            $argLen = 0;
            for ($k = $argRange['start']; $k < $argRange['end']; $k++) {
                $argLen += strlen(tokenSource($tokens[$k]));
            }

            $findings[] = [
                'line' => $line,
                'func' => $func,
                'argIndex' => $argPos,
                'before' => $before,
                'after' => $after,
                'fixable' => $fixable,
                'offset' => $offset,
                'length' => $argLen,
            ];
        }
    }

    return $findings;
}

/**
 * @param list<array{line: int, func: string, argIndex: int, before: string, after: string, fixable: bool, offset: int, length: int}> $findings
 */
function applyFindings(string $code, array $findings): string
{
    $fixable = array_values(array_filter($findings, static fn (array $f): bool => $f['fixable']));
    if ($fixable === []) {
        return $code;
    }

    usort($fixable, static fn (array $a, array $b): int => $b['offset'] <=> $a['offset']);

    foreach ($fixable as $finding) {
        $before = $finding['before'];
        $after = $finding['after'];
        $offset = $finding['offset'];
        $length = $finding['length'];

        $segment = substr($code, $offset, $length);
        if (trim($segment) !== $before) {
            throw new RuntimeException(
                "Desajuste al aplicar en offset {$offset}: esperado argumento distinto al del fichero"
            );
        }

        $replacement = $after;
        if (preg_match('/^(\s*)(.*?)(\s*)$/s', $segment, $m)) {
            $replacement = $m[1] . $after . $m[3];
        }

        $code = substr($code, 0, $offset) . $replacement . substr($code, $offset + $length);
    }

    return $code;
}

$args = array_slice($argv, 1);
$apply = in_array('--apply', $args, true);
$dryRun = in_array('--dry-run', $args, true) || !$apply;
$includeTests = in_array('--include-tests', $args, true);
$pathFilter = null;
$onlyFixable = in_array('--only-fixable', $args, true);
foreach ($args as $i => $arg) {
    if (str_starts_with($arg, '--path=')) {
        $pathFilter = substr($arg, strlen('--path='));
    } elseif ($arg === '--path' && isset($args[$i + 1])) {
        $pathFilter = $args[$i + 1];
    }
}
if (in_array('--help', $args, true) || in_array('-h', $args, true)) {
    echo <<<'TXT'
Detecta funciones de cadena con argumentos que pueden ser null (PHP 8.1+ Deprecated).

Opciones:
  --dry-run         Lista hallazgos sin escribir (por defecto)
  --apply           Aplica coalesce ?? '' en expresiones autocorregibles
  --path=SUBRUTA    Limita el barrido (ej. src/misas); también --path SUBRUTA
  --only-fixable    Oculta hallazgos que requieren revisión manual
  --include-tests   Incluye tests/ y frontend/

Solo se modifican expresiones del tipo $obj->getX() o $this->foo->getX()->value().
El resto se listan como "revisar manual".

TXT;
    exit(0);
}

$files = discoverPhpFiles($pathFilter, $includeTests);
if ($files === []) {
    fwrite(STDERR, "No se encontraron ficheros PHP.\n");
    exit(1);
}

$totalFiles = 0;
$totalFixable = 0;
$totalManual = 0;

foreach ($files as $file) {
    $findings = analyzeFile($file);
    if ($findings === []) {
        continue;
    }

    $fixable = array_values(array_filter($findings, static fn (array $f): bool => $f['fixable']));
    $manual = array_values(array_filter($findings, static fn (array $f): bool => !$f['fixable']));

    if ($onlyFixable && $fixable === []) {
        continue;
    }
    if ($fixable === [] && $manual === []) {
        continue;
    }

    $totalFiles++;
    echo relPath($file) . "\n";

    foreach ($findings as $f) {
        if ($onlyFixable && !$f['fixable']) {
            continue;
        }
        $tag = $f['fixable'] ? 'FIX' : 'MANUAL';
        echo "  [{$tag}] L{$f['line']} {$f['func']}() arg#{$f['argIndex']}: {$f['before']}\n";
        if ($f['fixable']) {
            echo "         -> {$f['after']}\n";
        }
    }

    $totalFixable += count($fixable);
    $totalManual += count($manual);

    if ($apply && $fixable !== []) {
        $original = file_get_contents($file);
        if ($original === false) {
            throw new RuntimeException("No se puede leer: {$file}");
        }
        $updated = applyFindings($original, $findings);
        if ($updated !== $original && file_put_contents($file, $updated) === false) {
            throw new RuntimeException("No se puede escribir: {$file}");
        }
    }
}

echo "\n";
echo ($apply ? 'Aplicado' : 'Dry-run') . ": {$totalFiles} ficheros";
echo ", {$totalFixable} autocorregibles, {$totalManual} revisar manual\n";

if ($dryRun && !$apply && $totalFixable > 0) {
    echo "Ejecuta con --apply para escribir los cambios autocorregibles.\n";
}

exit(0);
