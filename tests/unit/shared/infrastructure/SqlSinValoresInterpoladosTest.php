<?php

declare(strict_types=1);

namespace Tests\unit\shared\infrastructure;

use PHPUnit\Framework\TestCase;

/**
 * Test-guardia: ninguna sentencia SQL de `src/` lleva una variable de PHP dentro de un literal entre
 * comillas simples (`'$x'`, `'%$x%'`, `'^$x'`, `'" . $x . "'`). Los valores van como parámetros
 * (`:param` con `prepareAndExecute`, o en el array de `execute`).
 *
 * Las líneas que ya existían, revisadas como seguras (enteros, value objects que validan el formato,
 * valores del propio código o de la configuración), están en `sql_interpolado_baseline.txt`
 * (fichero|línea sin sangría). Al convertir una a parámetro, se quita de la lista; la lista solo encoge.
 *
 * Revisión de seguridad del 2026-11-18 (plan de tests, sección 38).
 */
final class SqlSinValoresInterpoladosTest extends TestCase
{
    private const SQL = '/\b(SELECT|UPDATE|DELETE|INSERT|WHERE|AND|OR|SET|VALUES|FROM|JOIN|ILIKE|LIKE|IN)\b/';

    /** @return list<string> «fichero|línea» de las líneas que lo incumplen */
    public static function lineasConValoresInterpolados(string $raiz): array
    {
        $hits = [];
        $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($raiz . '/src', \FilesystemIterator::SKIP_DOTS));
        foreach ($it as $fichero) {
            /** @var \SplFileInfo $fichero */
            if ($fichero->getExtension() !== 'php') {
                continue;
            }
            $ruta = substr($fichero->getPathname(), strlen($raiz) + 1);
            foreach (file($fichero->getPathname()) ?: [] as $linea) {
                $codigo = trim($linea);
                if ($codigo === '' || str_starts_with($codigo, '*') || str_starts_with($codigo, '//') || str_starts_with($codigo, '#')) {
                    continue;
                }
                if (self::interpolaUnValor($linea)) {
                    $hits[] = $ruta . '|' . $codigo;
                }
            }
        }
        sort($hits);

        return array_values(array_unique($hits));
    }

    private static function interpolaUnValor(string $linea): bool
    {
        // '" . $var  (literal SQL cerrado alrededor de una concatenación)
        if (preg_match('/\'"\s*\.\s*\$/', $linea) === 1 && preg_match(self::SQL, $linea) === 1) {
            return true;
        }
        // '...$var dentro de un string PHP entre comillas dobles
        preg_match_all('/"((?:[^"\\\\]|\\\\.)*)"/', $linea, $m);
        foreach ($m[1] as $dentro) {
            if (preg_match('/\'[^\'"]*(\$\w|\{\$)/', $dentro) === 1 && preg_match(self::SQL, $dentro . ' ' . $linea) === 1) {
                return true;
            }
        }

        return false;
    }

    public function test_ninguna_sentencia_sql_lleva_un_valor_interpolado_fuera_de_la_lista(): void
    {
        $raiz = dirname(__DIR__, 4);
        $actuales = self::lineasConValoresInterpolados($raiz);
        $base = array_values(array_filter(
            array_map('trim', file(__DIR__ . '/sql_interpolado_baseline.txt') ?: []),
            static fn (string $l): bool => $l !== '' && !str_starts_with($l, '#')
        ));

        $nuevas = array_values(array_diff($actuales, $base));
        $this->assertSame([], $nuevas, "SQL con un valor interpolado entre comillas (usar un parámetro):\n" . implode("\n", $nuevas));

        $sobran = array_values(array_diff($base, $actuales));
        $this->assertSame([], $sobran, "Quitar de sql_interpolado_baseline.txt (ya no existen):\n" . implode("\n", $sobran));
    }
}
