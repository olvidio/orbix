<?php

declare(strict_types=1);

/**
 * Prepara la rotación de las contraseñas de los roles de PostgreSQL de una instalación.
 *
 * Lee los ficheros de conexión (`*.inc` con entradas `'user' => ..., 'password' => ...`) de uno o varios
 * directorios, genera una contraseña nueva por rol (la misma en todos los ficheros donde aparece) y
 * escribe en un directorio de salida:
 *
 *   - una copia de cada fichero con las contraseñas nuevas (misma ruta relativa, mismo formato y comentarios);
 *   - `rotar_roles.sql`, con un `ALTER ROLE ... PASSWORD ...` por rol.
 *
 * NO toca los ficheros originales ni la base de datos. Sin `--salida` solo lista lo que encontraría.
 *
 * Uso:
 *   php tools/fix/rotar_passwords_roles.php --conf=/ruta/conf [--conf=/otra/ruta] --salida=/ruta/fuera/de/la/web
 *       [--excluir=rol1,rol2] [--solo=rol1,rol2] [--largo=32]
 *
 * Después, a mano y en este orden, en una ventana de mantenimiento:
 *   1. psql -U <superusuario> -h <host> -d postgres -f <salida>/rotar_roles.sql   (en cada clúster)
 *   2. copiar los `.inc` de <salida> sobre los de la instalación
 *   3. comprobar que la aplicación entra y borrar <salida> (contiene las contraseñas en claro)
 *
 * La contraseña de un rol es del clúster: si el mismo rol existe en varios, el SQL va a todos.
 * Las contraseñas nuevas solo llevan letras y cifras, así que no necesitan escaparse en SQL ni en PHP.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);

    return;
}

/** @return array{conf: list<string>, salida: ?string, excluir: list<string>, solo: list<string>, largo: int} */
function opciones(array $argv): array
{
    $o = ['conf' => [], 'salida' => null, 'excluir' => [], 'solo' => [], 'largo' => 32];
    foreach (array_slice($argv, 1) as $arg) {
        if (preg_match('/^--(conf|salida|excluir|solo|largo)=(.+)$/', $arg, $m) !== 1) {
            fwrite(STDERR, "Opción desconocida: $arg\n");
            exit(2);
        }
        match ($m[1]) {
            'conf' => $o['conf'][] = rtrim($m[2], '/'),
            'salida' => $o['salida'] = rtrim($m[2], '/'),
            'excluir' => $o['excluir'] = array_values(array_filter(explode(',', $m[2]))),
            'solo' => $o['solo'] = array_values(array_filter(explode(',', $m[2]))),
            'largo' => $o['largo'] = max(24, (int) $m[2]),
        };
    }
    if ($o['conf'] === []) {
        fwrite(STDERR, "Falta --conf=/ruta/al/directorio/de/los/.inc\n");
        exit(2);
    }

    return $o;
}

function passwordNueva(int $largo): string
{
    $alfabeto = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789';
    $max = strlen($alfabeto) - 1;
    $p = '';
    for ($i = 0; $i < $largo; $i++) {
        $p .= $alfabeto[random_int(0, $max)];
    }

    return $p;
}

/** Valor de un literal de cadena PHP sencillo (`'...'` o `"..."` sin interpolación). */
function valorDeLiteral(string $literal): ?string
{
    if (strlen($literal) < 2) {
        return null;
    }
    $comilla = $literal[0];
    $dentro = substr($literal, 1, -1);
    if ($comilla === "'") {
        return str_replace(["\\'", '\\\\'], ["'", '\\'], $dentro);
    }
    if ($comilla === '"' && !str_contains($dentro, '$')) {
        return stripcslashes($dentro);
    }

    return null;
}

/**
 * Parejas usuario/contraseña de un fichero: cada array que tiene las dos claves.
 *
 * @return array{tokens: list<mixed>, parejas: list<array{user: string, indice: int}>, sueltas: int}
 */
function analizar(string $codigo): array
{
    $tokens = token_get_all($codigo);
    $pila = [];
    $parejas = [];
    $sueltas = 0;
    $n = count($tokens);
    $cerrar = static function () use (&$pila, &$parejas, &$sueltas): void {
        $marco = array_pop($pila);
        if ($marco === null) {
            return;
        }
        if ($marco['pwd'] !== null && $marco['user'] !== null) {
            $parejas[] = ['user' => $marco['user'], 'indice' => $marco['pwd']];
        } elseif ($marco['pwd'] !== null) {
            $sueltas++;
        }
    };
    for ($i = 0; $i < $n; $i++) {
        $t = $tokens[$i];
        if ($t === '[' || $t === '(') {
            $pila[] = ['user' => null, 'pwd' => null];
            continue;
        }
        if ($t === ']' || $t === ')') {
            $cerrar();
            continue;
        }
        if (!is_array($t) || $t[0] !== T_CONSTANT_ENCAPSED_STRING || $pila === []) {
            continue;
        }
        $clave = valorDeLiteral($t[1]);
        if ($clave !== 'user' && $clave !== 'password') {
            continue;
        }
        // clave => 'valor'
        $j = $i + 1;
        while ($j < $n && is_array($tokens[$j]) && $tokens[$j][0] === T_WHITESPACE) {
            $j++;
        }
        if ($j >= $n || !is_array($tokens[$j]) || $tokens[$j][0] !== T_DOUBLE_ARROW) {
            continue;
        }
        $j++;
        while ($j < $n && is_array($tokens[$j]) && $tokens[$j][0] === T_WHITESPACE) {
            $j++;
        }
        if ($j >= $n || !is_array($tokens[$j]) || $tokens[$j][0] !== T_CONSTANT_ENCAPSED_STRING) {
            continue;
        }
        $valor = valorDeLiteral($tokens[$j][1]);
        if ($valor === null) {
            continue;
        }
        $marco = &$pila[count($pila) - 1];
        if ($clave === 'user') {
            $marco['user'] = $valor;
        } else {
            $marco['pwd'] = $j;
        }
        unset($marco);
        $i = $j;
    }

    return ['tokens' => $tokens, 'parejas' => $parejas, 'sueltas' => $sueltas];
}

$o = opciones($argv);
$nuevas = [];
$porFichero = [];
foreach ($o['conf'] as $dir) {
    if (!is_dir($dir)) {
        fwrite(STDERR, "No es un directorio: $dir\n");
        exit(2);
    }
    foreach ((array) glob($dir . '/*.inc') as $fichero) {
        $a = analizar((string) file_get_contents((string) $fichero));
        $roles = [];
        foreach ($a['parejas'] as $pareja) {
            $rol = $pareja['user'];
            if (in_array($rol, $o['excluir'], true) || ($o['solo'] !== [] && !in_array($rol, $o['solo'], true))) {
                continue;
            }
            $nuevas[$rol] ??= passwordNueva($o['largo']);
            $a['tokens'][$pareja['indice']][1] = "'" . $nuevas[$rol] . "'";
            $roles[$rol] = ($roles[$rol] ?? 0) + 1;
        }
        if ($roles === [] && $a['sueltas'] === 0) {
            continue;
        }
        $porFichero[(string) $fichero] = ['dir' => $dir, 'tokens' => $a['tokens'], 'roles' => $roles, 'sueltas' => $a['sueltas']];
    }
}

ksort($nuevas, SORT_NATURAL | SORT_FLAG_CASE);
echo 'Roles: ' . count($nuevas) . "\n";
foreach ($porFichero as $fichero => $info) {
    echo sprintf("  %s: %d entradas de %d roles", $fichero, array_sum($info['roles']), count($info['roles']));
    if ($info['sueltas'] > 0) {
        echo sprintf(' (%d contraseñas sin usuario al lado: no se tocan)', $info['sueltas']);
    }
    echo "\n";
}
echo '  ' . implode(' ', array_keys($nuevas)) . "\n";

if ($o['salida'] === null) {
    echo "\nSolo listado. Con --salida=/ruta se escriben los ficheros nuevos y el SQL (los originales no se tocan).\n";
    exit(0);
}

$salida = $o['salida'];
foreach ($o['conf'] as $dir) {
    $realConf = realpath($dir);
    $realSalida = realpath($salida) ?: $salida;
    if ($realConf !== false && str_starts_with($realSalida . '/', $realConf . '/')) {
        fwrite(STDERR, "La salida no puede estar dentro de $dir: los originales no se sobrescriben.\n");
        exit(2);
    }
}
if (is_dir($salida) && count((array) scandir($salida)) > 2) {
    fwrite(STDERR, "El directorio de salida ya tiene contenido: $salida\n");
    exit(2);
}
if (!is_dir($salida) && !mkdir($salida, 0700, true)) {
    fwrite(STDERR, "No se puede crear $salida\n");
    exit(1);
}
chmod($salida, 0700);

$varios = count($o['conf']) > 1;
foreach ($porFichero as $fichero => $info) {
    if ($info['roles'] === []) {
        continue;
    }
    $sub = $varios ? $salida . '/' . basename(dirname($info['dir'])) . '_' . basename($info['dir']) : $salida;
    if (!is_dir($sub)) {
        mkdir($sub, 0700, true);
    }
    $codigo = '';
    foreach ($info['tokens'] as $t) {
        $codigo .= is_array($t) ? $t[1] : $t;
    }
    $destino = $sub . '/' . basename($fichero);
    file_put_contents($destino, $codigo);
    chmod($destino, 0600);
}

$sql = "-- Rotación de contraseñas de roles. Ejecutar como superusuario en cada clúster.\n"
    . "-- Un rol que no exista en un clúster da error en su línea y se sigue con el resto.\n";
foreach ($nuevas as $rol => $password) {
    $sql .= sprintf("ALTER ROLE \"%s\" WITH PASSWORD '%s';\n", str_replace('"', '""', $rol), $password);
}
file_put_contents($salida . '/rotar_roles.sql', $sql);
chmod($salida . '/rotar_roles.sql', 0600);

echo "\nEscrito en $salida (solo lectura para su dueño): los .inc nuevos y rotar_roles.sql.\n"
    . "Pasos: 1) ejecutar el SQL en cada clúster; 2) copiar los .inc sobre los de la instalación;\n"
    . "3) comprobar que la aplicación entra y borrar ese directorio.\n";
