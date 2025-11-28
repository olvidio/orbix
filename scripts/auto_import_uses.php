#!/usr/bin/env php
<?php
declare(strict_types=1);
/**
 * Script: auto_import_uses.php
 *
 * Busca controladores PHP (por defecto: rutas tipo "src/<modulo>/infrastructure/controllers/*.php"),
 * detecta referencias a clases usadas sin "use" y añade las sentencias necesarias.
 *
 * Requiere Composer autoload (vendor/composer/*). Utiliza el classmap y PSR-4
 * para resolver el FQCN. Sólo inserta imports cuando la resolución es UNÍVOCA.
 * De lo contrario, reporta la ambigüedad y no modifica el archivo.
 *
 * Opciones:
 *   --module <mod>    Limitar a "src/<mod>/infrastructure/controllers"
 *   --path <ruta>     Archivo o directorio a procesar (sobrescribe --module)
 *   --apply           Aplica los cambios (por defecto: dry-run)
 *   --backup          Crea copia .bak.<timestamp> antes de modificar
 *   --verbose         Muestra información detallada
 *   --help            Muestra ayuda
 *   --show-unresolved Muestra un resumen final detallado de referencias sin resolución (aunque no se use --verbose)
 */
// Polyfill para str_starts_with en PHP < 8
if (!function_exists('str_starts_with')) {
    function str_starts_with(string $haystack, string $needle): bool {
        return $needle === '' || strpos($haystack, $needle) === 0;
    }
}

const EXIT_OK = 0;
const EXIT_ERR = 1;

// ---------------------------------------------------------
// Utilidades de CLI
// ---------------------------------------------------------

function println(string $msg = ''): void { fwrite(STDOUT, $msg."\n"); }
function eprintln(string $msg = ''): void { fwrite(STDERR, $msg."\n"); }

function usage(): void {
    println("Uso: php scripts/auto_import_uses.php [--module <mod>] [--path <ruta>] [--apply] [--backup] [--verbose]");
}

// ---------------------------------------------------------
// Parseo de argumentos
// ---------------------------------------------------------

$args = $argv;
array_shift($args);
$argsCount = count($args);
$opts = [
    'module'  => null,
    'path'    => null,
    'apply'   => false,
    'backup'  => false,
    'verbose' => false,
    'show-unresolved' => false,
];

for ($i = 0; $i < $argsCount; $i++) {
    $a = $args[$i];
    switch ($a) {
        case '--help':
        case '-h':
            usage();
            exit(EXIT_OK);
        case '--apply':
            $opts['apply'] = true; break;
        case '--backup':
            $opts['backup'] = true; break;
        case '--verbose':
        case '-v':
            $opts['verbose'] = true; break;
        case '--show-unresolved':
            $opts['show-unresolved'] = true; break;
        case '--module':
            $opts['module'] = $args[++$i] ?? null; break;
        case '--path':
            $opts['path'] = $args[++$i] ?? null; break;
        default:
            eprintln("Argumento no reconocido: $a");
            usage();
            exit(EXIT_ERR);
    }
}

$root = realpath(__DIR__ . '/..');
if ($root === false) { $root = getcwd(); }

// ---------------------------------------------------------
// Construir el índice de clases disponibles (FQCN → file) y por nombre corto
// ---------------------------------------------------------

$composerDir = $root . '/libs/vendor/composer';
if (!is_dir($composerDir)) {
    eprintln("Error: No se encontró vendor/composer. Ejecuta 'composer install' y vuelve a intentar.");
    exit(EXIT_ERR);
}

/** @var array<string,string> $classmap */
$classmap = [];
$classmapFile = $composerDir . '/autoload_classmap.php';
if (file_exists($classmapFile)) {
    /** @noinspection PhpIncludeInspection */
    $map = include $classmapFile;
    if (is_array($map)) { $classmap = $map; }
}

// También expandimos PSR-4 a un classmap aproximado (derivado de rutas)
$psr4File = $composerDir . '/autoload_psr4.php';
if (file_exists($psr4File)) {
    /** @noinspection PhpIncludeInspection */
    $psr4 = include $psr4File;
    if (is_array($psr4)) {
        foreach ($psr4 as $prefix => $dirs) {
            $dirs = (array)$dirs;
            foreach ($dirs as $dir) {
                $absDir = $dir;
                if (!preg_match('~^/|^[A-Za-z]:[\\/]~', $absDir)) { // no absoluto
                    $absDir = $root . '/' . $absDir;
                }
                if (!is_dir($absDir)) { continue; }
                // Recorrer *.php y derivar FQCN: prefix + path_relativo(sin .php) con barras como \
                $rii = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($absDir, FilesystemIterator::SKIP_DOTS)
                );
                /** @var SplFileInfo $file */
                foreach ($rii as $file) {
                    if (!$file->isFile()) continue;
                    if (strtolower($file->getExtension()) !== 'php') continue;
                    $rel = str_replace('\\', '/', substr($file->getPathname(), strlen($absDir) + 1));
                    $rel = preg_replace('~\\.php$~i', '', $rel);
                    $rel = str_replace('/', '\\', $rel);
                    $fqcn = rtrim($prefix, '\\') . '\\' . $rel;
                    $classmap[$fqcn] = $file->getPathname();
                }
            }
        }
    }
}

// Índice por nombre corto → lista de FQCN
$shortIndex = [];
foreach ($classmap as $fqcn => $file) {
    if (!is_string($fqcn)) continue;
    $short = ($p = strrpos($fqcn, '\\')) !== false ? substr($fqcn, $p + 1) : $fqcn;
    if ($short === '') continue;
    $shortIndex[$short][] = $fqcn;
}

if ($opts['verbose']) {
    println("Clases indexadas: " . count($classmap) . " (nombres cortos: " . count($shortIndex) . ")");
}

// ---------------------------------------------------------
// Descubrir archivos a procesar
// ---------------------------------------------------------

/**
 * @return list<string>
 */
function findTargets(string $root, ?string $module, ?string $pathOpt): array {
    $targets = [];
    if ($pathOpt) {
        $abs = realpath($pathOpt) ?: $pathOpt;
        if (is_dir($abs)) {
            $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($abs, FilesystemIterator::SKIP_DOTS));
            /** @var SplFileInfo $fi */
            foreach ($rii as $fi) {
                if ($fi->isFile() && strtolower($fi->getExtension()) === 'php') {
                    $targets[] = $fi->getPathname();
                }
            }
        } elseif (is_file($abs)) {
            $targets[] = $abs;
        }
        return $targets;
    }

    if ($module) {
        $dir = $root . "/src/$module/infrastructure/controllers";
        if (is_dir($dir)) {
            foreach (glob($dir . '/*.php') ?: [] as $f) { $targets[] = $f; }
        }
        return $targets;
    }

    // por defecto, todos los módulos
    foreach (glob($root . '/src/*/infrastructure/controllers') ?: [] as $dir) {
        foreach (glob($dir . '/*.php') ?: [] as $f) { $targets[] = $f; }
    }
    return $targets;
}

$targets = findTargets($root, $opts['module'], $opts['path']);
sort($targets);
if ($opts['verbose']) println('Archivos a procesar: ' . count($targets));

if (empty($targets)) {
    eprintln('No se encontraron archivos a procesar.');
    exit(EXIT_ERR);
}

// ---------------------------------------------------------
// Análisis de cada archivo con token_get_all
// ---------------------------------------------------------

/**
 * Representa el resultado de inspeccionar un archivo PHP.
 */
class PhpFileInfo {
    public string $namespace = '';
    /** @var array<string,string> alias (lower) → FQCN */
    public array $uses = [];
    /** @var array<int,array{type:string,name:string,line:int}> */
    public array $shortClassRefs = [];
}

/**
 * @return PhpFileInfo
 */
function analyzePhpFile(string $file): PhpFileInfo {
    $code = file_get_contents($file);
    $info = new PhpFileInfo();
    if ($code === false) return $info;

    $tokens = token_get_all($code);
    $count  = count($tokens);

    $i = 0;
    $currentNamespace = '';
    $collectingNs = false;
    $collectingUse = false;
    $currentUse = '';
    $currentAlias = '';

    $lastMeaningful = null;

    while ($i < $count) {
        $t = $tokens[$i];
        $id = is_array($t) ? $t[0] : null;
        $text = is_array($t) ? $t[1] : $t;
        $line = is_array($t) ? $t[2] : null;

        // namespace
        if ($id === T_NAMESPACE) {
            $collectingNs = true;
            $currentNamespace = '';
        } elseif ($collectingNs) {
            if ($text === ';' || $text === '{') {
                $collectingNs = false;
                $info->namespace = trim($currentNamespace, " \\\t\n");
            } else {
                $currentNamespace .= is_array($t) ? $t[1] : $t;
            }
        }

        // use Foo\Bar as Baz;
        if ($id === T_USE) {
            // Evitar confundir con "use (closure)" usando la heurística: si aparece un '(' tras T_USE pronto, es closure
            $j = $i + 1; $isClosureUse = false;
            while ($j < $count) {
                $tj = $tokens[$j];
                $txt = is_array($tj) ? $tj[1] : $tj;
                $tid = is_array($tj) ? $tj[0] : null;
                if ($tid === T_WHITESPACE) { $j++; continue; }
                if ($txt === '(') { $isClosureUse = true; }
                break;
            }
            if (!$isClosureUse) {
                $collectingUse = true;
                $currentUse = '';
                $currentAlias = '';
            }
        } elseif ($collectingUse) {
            if ($text === ';') {
                $collectingUse = false;
                $fqcn = trim($currentUse, " \\\t\n");
                $alias = $currentAlias ?: (($p = strrpos($fqcn, '\\')) !== false ? substr($fqcn, $p + 1) : $fqcn);
                if ($fqcn !== '') {
                    $info->uses[strtolower($alias)] = ltrim($fqcn, '\\');
                }
            } else {
                if (is_array($t) && $t[0] === T_AS) {
                    // recoger alias tras T_AS
                    $k = $i + 1; $aliasName = '';
                    while ($k < $count) {
                        $tk = $tokens[$k];
                        if (is_array($tk)) {
                            if (in_array($tk[0], [T_STRING, T_NAME_QUALIFIED, T_NAME_FULLY_QUALIFIED], true)) {
                                $aliasName .= $tk[1];
                            } elseif ($tk[0] === T_WHITESPACE) {
                                // skip
                            } else { break; }
                        } else {
                            if ($tk === ';' || $tk === ',') { break; }
                        }
                        $k++;
                    }
                    $currentAlias = $aliasName;
                } else {
                    $currentUse .= is_array($t) ? $t[1] : $t;
                }
            }
        }

        // Detectar referencias a clases cortas en:
        // - new X, instanceof X, catch (X), static calls X::
        // - extends/implements X
        // - type hints en params y returns (heurística simple)
        $meaningful = !is_array($t) || $id !== T_WHITESPACE ? $text : null;
        if ($meaningful !== null) { $lastMeaningful = $t; }

        // helper para leer nombre potencial de clase (una secuencia de T_STRING y separadores de namespace)
        $readName = function(int $start) use ($tokens, $count): array {
            $name = '';
            $line = is_array($tokens[$start]) ? $tokens[$start][2] : null;
            $i = $start;
            while ($i < $count) {
                $tt = $tokens[$i];
                if (is_array($tt)) {
                    $tid = $tt[0]; $txt = $tt[1];
                    if (in_array($tid, [T_STRING, defined('T_NAME_QUALIFIED')?T_NAME_QUALIFIED:99999, defined('T_NAME_FULLY_QUALIFIED')?T_NAME_FULLY_QUALIFIED:99998], true)) {
                        $name .= $txt;
                    } elseif ($tid === T_NS_SEPARATOR) {
                        $name .= '\\';
                    } elseif ($tid === T_WHITESPACE) {
                        // permitir espacios en nombres compuestos (no debería)
                        $name .= '';
                    } else { break; }
                } else {
                    if ($tt === '\\') { $name .= '\\'; }
                    else { break; }
                }
                $i++;
            }
            return [$name, $i, $line ?? 0];
        };

        // Patrón: new X
        if ($id === T_NEW) {
            // saltar posibles espacios
            $j = $i + 1; while ($j < $count && is_array($tokens[$j]) && $tokens[$j][0] === T_WHITESPACE) $j++;
            [$name, $i2, $ln] = $readName($j);
            if ($name !== '' && $name[0] !== '\\') {
                $info->shortClassRefs[] = ['type' => 'new', 'name' => $name, 'line' => $ln ?? $line ?? 0];
            }
        }

        // Patrón: instanceof X
        if ($id === T_INSTANCEOF) {
            $j = $i + 1; while ($j < $count && is_array($tokens[$j]) && $tokens[$j][0] === T_WHITESPACE) $j++;
            [$name, $i2, $ln] = $readName($j);
            if ($name !== '' && $name[0] !== '\\') {
                $info->shortClassRefs[] = ['type' => 'instanceof', 'name' => $name, 'line' => $ln ?? $line ?? 0];
            }
        }

        // Patrón: catch (X $e)
        if ($id === T_CATCH) {
            // avanzar hasta '(' y leer posibles nombres separados por '|'
            $j = $i; while ($j < $count && (!is_string($tokens[$j]) || $tokens[$j] !== '(')) $j++;
            $j++;
            while ($j < $count) {
                if (is_string($tokens[$j]) && $tokens[$j] === ')') break;
                if (is_array($tokens[$j]) && in_array($tokens[$j][0], [T_STRING, defined('T_NAME_QUALIFIED')?T_NAME_QUALIFIED:99999], true)) {
                    [$name, $j2, $ln] = $readName($j);
                    if ($name !== '' && $name[0] !== '\\') {
                        $info->shortClassRefs[] = ['type' => 'catch', 'name' => $name, 'line' => $ln ?? 0];
                    }
                    $j = $j2; continue;
                }
                $j++;
            }
        }

        // Patrón: X:: (static calls/const)
        if (is_array($lastMeaningful) && $lastMeaningful[0] === T_STRING) {
            // mirar si el siguiente token significativo es '::'
            $j = $i + 1;
            while ($j < $count && is_array($tokens[$j]) && $tokens[$j][0] === T_WHITESPACE) $j++;
            if ($j < $count && $tokens[$j] === '::') {
                $name = $lastMeaningful[1];
                if ($name !== '' && $name[0] !== '\\') {
                    $info->shortClassRefs[] = ['type' => 'static', 'name' => $name, 'line' => $lastMeaningful[2] ?? 0];
                }
            }
        }

        // Patrón simple: extends/implements X (nota: puede ser lista separada por comas)
        if ($id === T_EXTENDS || $id === T_IMPLEMENTS) {
            $j = $i + 1;
            while ($j < $count) {
                if (is_array($tokens[$j]) && in_array($tokens[$j][0], [T_STRING, defined('T_NAME_QUALIFIED')?T_NAME_QUALIFIED:99999], true)) {
                    [$name, $j2, $ln] = $readName($j);
                    if ($name !== '' && $name[0] !== '\\') {
                        $info->shortClassRefs[] = ['type' => ($id === T_EXTENDS ? 'extends' : 'implements'), 'name' => $name, 'line' => $ln ?? 0];
                    }
                    $j = $j2; continue;
                }
                if (is_string($tokens[$j]) && ($tokens[$j] === '{' || $tokens[$j] === ';')) break;
                $j++;
            }
        }

        $i++;
    }

    return $info;
}

/**
 * Inserta sentencias use en el contenido del archivo.
 * Retorna el nuevo contenido.
 *
 * Estrategia: localizar namespace; luego el último bloque de "use" existente;
 * insertar tras él; si no hay, tras namespace y una línea en blanco; si no hay namespace,
 * tras el tag <?php y una línea en blanco.
 */
function insertUses(string $code, array $newUses): string {
    if (empty($newUses)) return $code;
    sort($newUses, SORT_STRING);
    $insertion = '';
    foreach ($newUses as $fqcn) {
        $insertion .= 'use ' . ltrim($fqcn, '\\') . ";\n";
    }
    $insertion .= "\n";

    $tokens = token_get_all($code);
    $count = count($tokens);
    $pos = 0;
    $namespaceEndPos = null; // posición en string para insertar tras namespace/uses
    $lastUseEndPos = null;

    // obtener posiciones byte de tokens interesantes
    $offsets = [];
    $cursor = 0;
    foreach ($tokens as $tok) {
        $txt = is_array($tok) ? $tok[1] : $tok;
        $offsets[] = $cursor;
        $cursor += strlen($txt);
    }

    // Buscar namespace y último use
    for ($i = 0; $i < $count; $i++) {
        $tok = $tokens[$i];
        if (is_array($tok)) {
            if ($tok[0] === T_NAMESPACE) {
                // avanzar hasta ';' o '{'
                for ($j = $i; $j < $count; $j++) {
                    if ($tokens[$j] === ';' || $tokens[$j] === '{') {
                        $namespaceEndPos = $offsets[$j] + 1; // después del separador
                        break;
                    }
                }
            }
            if ($tok[0] === T_USE) {
                // avanzar hasta ';'
                for ($j = $i; $j < $count; $j++) {
                    if ($tokens[$j] === ';') {
                        $lastUseEndPos = $offsets[$j] + 1;
                        break;
                    }
                }
            }
        }
    }

    $insertAt = $lastUseEndPos ?? $namespaceEndPos;
    if ($insertAt === null) {
        // tras <?php
        $p = strpos($code, '<?php');
        if ($p === false) { $p = -5; }
        $insertAt = $p + 5;
        // asegurar salto de línea
        $prefix = substr($code, 0, $insertAt);
        $suffix = substr($code, $insertAt);
        if ($suffix !== '' && $suffix[0] !== "\n") {
            $insertion = "\n" . $insertion;
        }
        return $prefix . "\n" . $insertion . $suffix;
    }

    // Insertar con una línea en blanco después
    $prefix = substr($code, 0, $insertAt);
    $suffix = substr($code, $insertAt);
    return $prefix . "\n" . $insertion . $suffix;
}

// ---------------------------------------------------------
// Proceso principal
// ---------------------------------------------------------

$totalFiles = 0;
$changedFiles = 0;
$ambiguous = 0;
$nores = 0;
// Detalle de no resueltas: array<array{file:string,line:int,name:string}>
$unresolvedDetails = [];

foreach ($targets as $file) {
    $totalFiles++;
    $info = analyzePhpFile($file);
    $existingAliases = array_change_key_case($info->uses, CASE_LOWER);
    $ns = ltrim($info->namespace, '\\');

    $missing = [];
    $ambigForFile = [];
    $unresolvedForFile = [];

    foreach ($info->shortClassRefs as $ref) {
        $name = $ref['name'];
        // ignorar nombres cualificados (contienen '\\') porque ya traen namespace relativo/completo
        if (strpos($name, '\\') !== false) continue;

        $aliasKey = strtolower($name);
        if (isset($existingAliases[$aliasKey])) continue; // ya importado con ese alias

        // Si la clase existe en el mismo namespace (ns\name) en classmap, no hace falta use
        $candidateSameNs = $ns !== '' ? $ns . '\\' . $name : $name;
        if (isset($classmap[$candidateSameNs])) {
            continue;
        }

        $candidates = $shortIndex[$name] ?? [];
        if (count($candidates) === 1) {
            $missing[$name] = $candidates[0];
        } elseif (count($candidates) > 1) {
            // si hay una que empieza por 'core\\' o por 'src\\', intentamos elegir de forma heurística
            $prefer = array_values(array_filter($candidates, fn($fq) => str_starts_with($fq, 'src\\') || str_starts_with($fq, 'core\\') || str_starts_with($fq, 'permisos\\')));
            $prefer = array_values($prefer);
            if (count($prefer) === 1) {
                $missing[$name] = $prefer[0];
            } else {
                $ambigForFile[$name] = $candidates;
            }
        } else {
            $nores++;
            $unresolvedForFile[] = [
                'name' => $name,
                'line' => $ref['line'] ?? 0,
            ];
        }
    }

    if ($opts['verbose']) {
        if (!empty($missing) || !empty($ambigForFile)) {
            println("\n[{$file}] namespace={$ns}");
        }
        foreach ($missing as $short => $fq) {
            println("  + Falta 'use $fq' (ref: $short)");
        }
        foreach ($ambigForFile as $short => $list) {
            println("  ! Ambiguo '$short' → {" . implode(', ', $list) . "}");
        }
        foreach ($unresolvedForFile as $item) {
            $ln = (int)($item['line'] ?? 0);
            $lnTxt = $ln > 0 ? ":$ln" : '';
            println("  - Sin resolución '{$item['name']}'$lnTxt");
        }
    }

    // acumular ambigüedades
    if (!empty($ambigForFile)) {
        $ambiguous += count($ambigForFile);
    }

    // acumular no resueltas con detalle (evitar duplicados por misma (file,name,line))
    if (!empty($unresolvedForFile)) {
        foreach ($unresolvedForFile as $item) {
            $unresolvedDetails[] = [
                'file' => $file,
                'line' => (int)($item['line'] ?? 0),
                'name' => $item['name'],
            ];
        }
    }

    if (empty($missing)) {
        continue; // nada que hacer
    }

    if (!$opts['apply']) {
        continue; // dry-run
    }

    // Aplicar inserción
    $code = file_get_contents($file);
    if ($code === false) { eprintln("No se pudo leer $file"); continue; }

    $newCode = insertUses($code, array_values($missing));
    if ($newCode !== $code) {
        if ($opts['backup']) {
            $ts = date('Ymd-His');
            @copy($file, $file . '.bak.' . $ts);
        }
        $tmp = $file . '.tmp.' . getmypid();
        file_put_contents($tmp, $newCode);
        rename($tmp, $file);
        $changedFiles++;
    }
}

println("\nArchivos analizados: $totalFiles");
println("Archivos modificados: $changedFiles");
println("Referencias ambiguas: $ambiguous");
println("Referencias sin resolución: $nores");

// Resumen detallado de no resueltas
if ($nores > 0 && ($opts['verbose'] || $opts['show-unresolved'])) {
    println("\nDetalle de referencias sin resolución:");
    // Deduplicar y ordenar por archivo y línea
    $seen = [];
    $dedup = [];
    foreach ($unresolvedDetails as $d) {
        $key = $d['file'].'#'.($d['line'] ?? 0).'#'.$d['name'];
        if (isset($seen[$key])) continue;
        $seen[$key] = true;
        $dedup[] = $d;
    }
    usort($dedup, function ($a, $b) {
        return [$a['file'], $a['line'], $a['name']] <=> [$b['file'], $b['line'], $b['name']];
    });
    foreach ($dedup as $d) {
        $ln = (int)($d['line'] ?? 0);
        $lnTxt = $ln > 0 ? ":$ln" : '';
        println("  - {$d['file']}{$lnTxt} → '{$d['name']}'");
    }
}

exit(EXIT_OK);
