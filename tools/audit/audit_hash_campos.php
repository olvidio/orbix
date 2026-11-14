<?php

declare(strict_types=1);

/**
 * Auditoría estática de los hashes «sin valores» de las peticiones AJAX.
 *
 * Con `HashF::linkSinValParams()` / `linkSinVal()` el servidor firma la URL y **los nombres** de los
 * campos (`setCamposForm('a!b')`). Al validar compara con los nombres que llegan en el POST: si la
 * vista envía un campo de más o de menos, la petición se rechaza (302 al inicio) sin mensaje.
 *
 * Este script busca en `frontend/<modulo>/controller/*.php` cada `$h = $oHash->linkSinVal…()`, toma los
 * campos de su último `setCamposForm('…')`, localiza en las vistas del módulo dónde se usa la variable
 * (`<?= $h ?>` o `{{ h|raw }}`) y extrae de esa sentencia JavaScript los nombres de los parámetros
 * (`'a=' + … + '&b=' + …`). Informa de las diferencias.
 *
 * Es heurístico: lo que no puede analizar (campos no literales, parámetros montados con `serialize()`
 * o con variables) lo lista aparte para revisarlo a mano.
 *
 * Uso (desde la raíz del repo):
 *   php tools/audit/audit_hash_campos.php            # resumen y diferencias
 *   php tools/audit/audit_hash_campos.php --todo     # también los que coinciden y los no analizables
 *   php tools/audit/audit_hash_campos.php --strict   # código de salida 1 si hay diferencias
 */

$root = dirname(__DIR__, 2);
$verTodo = in_array('--todo', $argv, true);
$strict = in_array('--strict', $argv, true);

/** Campos que el servidor descarta antes de comparar (ver `HashF::validatePost`). */
const CAMPOS_IGNORADOS = ['PHPSESSID', 'atras', 'h', 'hc', 'horig', 'hh', 'hhc', 'hhorig', 'hno', 'hchk', 'hnov', 'hpos'];

$diferencias = [];
$coinciden = [];
$noAnalizables = [];
$sinUso = [];

foreach (glob($root . '/frontend/*/controller/*.php') ?: [] as $controlador) {
    $php = (string) file_get_contents($controlador);
    $modulo = basename(dirname($controlador, 2));
    $rel = substr($controlador, strlen($root) + 1);

    if (preg_match_all('/\$(\w+)\s*=\s*\$(\w+)->linkSinVal(?:Params)?\(\)\s*;/', $php, $usos, PREG_OFFSET_CAPTURE | PREG_SET_ORDER) === 0) {
        continue;
    }
    foreach ($usos as $uso) {
        $varHash = $uso[1][0];
        $obj = $uso[2][0];
        $pos = $uso[0][1];
        $linea = substr_count($php, "\n", 0, $pos) + 1;

        // Último setCamposForm del objeto antes de esta sentencia.
        $antes = substr($php, 0, $pos);
        if (preg_match_all('/\$' . preg_quote($obj, '/') . '->setCamposForm\((.*?)\)\s*;/is', $antes, $sets, PREG_SET_ORDER) === 0) {
            $campos = [];
        } else {
            $arg = trim(end($sets)[1]);
            if (preg_match('/^([\'"])([^\'"$]*)\1$/', $arg, $lit) !== 1) {
                $noAnalizables[] = "$rel:$linea  \$$varHash: setCamposForm no es un literal ($arg)";
                continue;
            }
            $campos = array_values(array_filter(explode('!', $lit[2]), static fn (string $c): bool => $c !== ''));
        }
        // Campos que el servidor quita del POST antes de comparar (`setCamposNo`, viajan en `hno`).
        $camposNo = [];
        if (preg_match_all('/\$' . preg_quote($obj, '/') . '->setCamposNo\(\s*([\'"])([^\'"$]*)\1\s*\)\s*;/i', $antes, $nos, PREG_SET_ORDER) > 0) {
            $camposNo = array_values(array_filter(explode('!', end($nos)[2]), static fn (string $c): bool => $c !== ''));
        }
        $ignorados = array_merge(CAMPOS_IGNORADOS, $camposNo);
        $enHashYEnNo = array_values(array_intersect($campos, $camposNo));
        if ($enHashYEnNo !== []) {
            $noAnalizables[] = "$rel:$linea  \$$varHash: campos a la vez en setCamposForm y setCamposNo (" . implode(', ', $enHashYEnNo) . ')';
        }
        $campos = array_values(array_diff(array_unique($campos), CAMPOS_IGNORADOS));
        sort($campos);

        // Nombre(s) con que llega a la vista: 'clave' => $var (o el propio nombre de la variable).
        $claves = [$varHash];
        if (preg_match_all('/[\'"](\w+)[\'"]\s*=>\s*\$' . preg_quote($varHash, '/') . '\b/', $php, $m) > 0) {
            $claves = array_values(array_unique(array_merge($claves, $m[1])));
        }

        $encontrado = false;
        $vistas = vistasDelControlador($root, $modulo, $controlador, $php);
        foreach ($vistas as $vista) {
            $txt = (string) file_get_contents($vista);
            $relVista = substr($vista, strlen($root) + 1);
            foreach ($claves as $clave) {
                $patron = '/<\?=\s*\$' . preg_quote($clave, '/') . '\s*;?\s*\?>|\{\{\s*' . preg_quote($clave, '/') . '\s*(?:\|\s*raw\s*)?\}\}/';
                if (preg_match_all($patron, $txt, $ocurrencias, PREG_OFFSET_CAPTURE) === 0) {
                    continue;
                }
                /* Las etiquetas de plantilla (PHP y Twig) se sustituyen por un relleno de la misma
                   longitud: sus llaves, comillas y `;` no son JavaScript. */
                $js = (string) preg_replace_callback(
                    '/<\?(?:=|php)\s.*?\?>|\{\{.*?\}\}|\{%.*?%\}/s',
                    static fn (array $t): string => str_repeat('X', strlen($t[0])),
                    $txt
                );
                foreach ($ocurrencias[0] as [$_, $posUso]) {
                    $encontrado = true;
                    $lineaVista = substr_count($txt, "\n", 0, $posUso) + 1;
                    $sentencia = sentenciaJs($js, $posUso);
                    $donde = "$rel:$linea  \$$varHash → $relVista:$lineaVista";
                    if ($sentencia === null) {
                        $noAnalizables[] = "$donde: no se reconoce la sentencia JavaScript";
                        continue;
                    }
                    [$faltan, $sobran] = comparar($campos, nombresDeParametros($sentencia), $ignorados);
                    if ($faltan === [] && $sobran === []) {
                        $coinciden[] = $donde;
                        continue;
                    }
                    // Segunda oportunidad: toda la función que contiene el uso (parámetros montados en
                    // varias sentencias, en otra variable o en un objeto `{campo: valor}`).
                    $funcion = funcionJs($js, $posUso);
                    if (preg_match('/serialize|\$\.param\s*\(/i', $funcion) === 1) {
                        $noAnalizables[] = "$donde: parámetros con serialize() (en el hash: " . implode(', ', $campos) . ')';
                        continue;
                    }
                    [$faltanF, $sobranF] = comparar($campos, nombresDeParametros($funcion), $ignorados);
                    // Si los campos del hash que faltan son claves de un objeto literal, se envían así;
                    // entonces lo que «sobra» son otras cadenas con `=` (p. ej. opciones de window.open).
                    $clavesObjeto = clavesDeObjetos($funcion);
                    if ($faltanF === [] ? $sobranF === [] : array_diff($faltanF, $clavesObjeto) === []) {
                        $coinciden[] = $donde;
                        continue;
                    }
                    $diferencias[] = ['donde' => $donde, 'hash' => $campos, 'faltan' => $faltanF, 'sobran' => $sobranF];
                }
            }
        }
        if (!$encontrado) {
            $sinUso[] = "$rel:$linea  \$$varHash (" . implode(', ', $campos) . ')';
        }
    }
}

/**
 * @param list<string> $campos
 * @param list<string> $enviados
 * @param list<string> $ignorados
 * @return array{0: list<string>, 1: list<string>} campos del hash que no se envían y campos enviados de más
 */
function comparar(array $campos, array $enviados, array $ignorados): array
{
    $enviados = array_values(array_diff(array_unique($enviados), $ignorados));

    return [array_values(array_diff($campos, $enviados)), array_values(array_diff($enviados, $campos))];
}

/**
 * Texto de la función JavaScript que contiene la posición: desde el `function` (o `=>`) anterior hasta
 * el siguiente `function` (o 1500 caracteres).
 */
function funcionJs(string $js, int $pos): string
{
    $antes = substr($js, 0, $pos);
    $ini = 0;
    // Preferir una función con nombre (`fnjs_x = function`, `function x(`): las anónimas intermedias
    // (callbacks, utilidades) no delimitan dónde se montan los parámetros.
    if (preg_match_all('/\b\w+\s*=\s*(?:async\s+)?function\b|\bfunction\s+\w+\s*\(/', $antes, $m, PREG_OFFSET_CAPTURE) > 0) {
        $ini = (int) end($m[0])[1];
        if (preg_match('/^(?:var|let|const)?\s*\w+\s*=\s*function\s*\(\s*\w*\s*\)\s*\{[^}]{0,200}\}/s', substr($js, $ini, 400)) === 1 && count($m[0]) > 1) {
            // Una utilidad corta declarada dentro (p. ej. `var decodeQP = function (s) {…}`): subir una más.
            $ini = (int) $m[0][count($m[0]) - 2][1];
        }
    } elseif (preg_match_all('/\bfunction\b|=>\s*\{/', $antes, $m, PREG_OFFSET_CAPTURE) > 0) {
        $ini = (int) end($m[0])[1];
    }
    $fin = strlen($js);
    if (preg_match('/\bfunction\b/', $js, $m2, PREG_OFFSET_CAPTURE, $pos) === 1) {
        $fin = (int) $m2[0][1];
    }

    return substr($js, $ini, min($fin, $pos + 1500) - $ini);
}

/**
 * Claves de objetos literales (`{dl_org: x, ssfsv: y}`), por si los parámetros se montan así.
 *
 * @return list<string>
 */
function clavesDeObjetos(string $js): array
{
    return preg_match_all('/[{,]\s*([A-Za-z_]\w*)\s*:/', $js, $m) > 0 ? array_values(array_unique($m[1])) : [];
}

/**
 * El propio controlador, las plantillas que nombra (`'x.phtml'`, `'x.html.twig'`) y, de forma
 * recursiva, las que estas incluyen (`{% include %}`, `require`), buscadas en la carpeta de vistas del
 * módulo y en `frontend/shared/view`.
 *
 * @return list<string>
 */
function vistasDelControlador(string $root, string $modulo, string $controlador, string $php): array
{
    $dirs = ["$root/frontend/$modulo/view", "$root/frontend/shared/view"];
    $vistas = [$controlador];
    $pendientes = [$php];
    $vistos = [];
    while ($pendientes !== []) {
        $txt = array_pop($pendientes);
        if (preg_match_all('/[\'"]([@\w\/.\-]*?[\w\-]+\.(?:inc\.)?(?:phtml|html\.twig))[\'"]/', $txt, $m) === 0) {
            continue;
        }
        foreach ($m[1] as $nombre) {
            $base = basename($nombre);
            foreach ($dirs as $dir) {
                $f = "$dir/$base";
                if (isset($vistos[$f]) || !is_file($f)) {
                    continue;
                }
                $vistos[$f] = true;
                $vistas[] = $f;
                $pendientes[] = (string) file_get_contents($f);
            }
        }
    }

    return $vistas;
}

/**
 * Texto de la sentencia JavaScript que contiene la posición `$pos`: desde el `;`, `{` o `}` anterior
 * (o el inicio de una propiedad `data:` / `url:`) hasta la posición.
 */
function sentenciaJs(string $txt, int $pos): ?string
{
    $inicio = 0;
    for ($i = $pos - 1; $i >= 0 && $pos - $i < 4000; $i--) {
        $c = $txt[$i];
        if ($c === ';' || $c === '{' || $c === '}') {
            $inicio = $i + 1;
            break;
        }
    }
    $fin = strpos($txt, ';', $pos);
    $fin = $fin === false ? $pos : min($fin, $pos + 600);
    $s = substr($txt, $inicio, $fin - $inicio);

    return trim($s) === '' ? null : $s;
}

/**
 * Nombres de los parámetros de una cadena de consulta montada a trozos: `'a=' + x + '&b=' + y`.
 *
 * @return list<string>
 */
function nombresDeParametros(string $sentencia): array
{
    // Un nombre de parámetro va pegado al inicio de una cadena o a un `&` / `?`: `'a='`, `&b=`.
    if (preg_match_all('/[\'"`&?]([A-Za-z_]\w*)(?:\[\])?=(?!=)/', $sentencia, $m) === 0) {
        return [];
    }

    return array_values($m[1]);
}

echo 'Hashes sin valores analizados: ' . (count($diferencias) + count($coinciden)) . "\n";
echo '  coinciden: ' . count($coinciden) . "\n";
echo '  con diferencias: ' . count($diferencias) . "\n";
echo '  no analizables (revisar a mano): ' . count($noAnalizables) . "\n";
echo '  variable sin uso en las vistas del módulo: ' . count($sinUso) . "\n";

if ($diferencias !== []) {
    echo "\n== Diferencias (el servidor rechazará la petición) ==\n";
    foreach ($diferencias as $d) {
        echo $d['donde'] . "\n";
        echo '    hash:   ' . implode(', ', $d['hash']) . "\n";
        if ($d['faltan'] !== []) {
            echo '    la vista NO envía: ' . implode(', ', $d['faltan']) . "\n";
        }
        if ($d['sobran'] !== []) {
            echo '    la vista envía de más: ' . implode(', ', $d['sobran']) . "\n";
        }
    }
}
if ($verTodo) {
    foreach (['No analizables' => $noAnalizables, 'Sin uso en las vistas' => $sinUso, 'Coinciden' => $coinciden] as $titulo => $lista) {
        echo "\n== $titulo ==\n" . implode("\n", $lista) . "\n";
    }
}

exit($strict && $diferencias !== [] ? 1 : 0);
