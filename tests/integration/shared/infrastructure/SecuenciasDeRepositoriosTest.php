<?php

declare(strict_types=1);

namespace Tests\integration\shared\infrastructure;

use PHPUnit\Framework\Attributes\DataProvider;
use src\shared\infrastructure\GlobalPdo;
use Tests\myTest;

/**
 * Guardia: cada repositorio toma los ids nuevos (`getNewId()`) de la secuencia de **su** tabla.
 *
 * `Guardar` actualiza la fila si el id ya existe. Si un repositorio usa la secuencia de otra tabla,
 * tarde o temprano devuelve un id que ya está en la suya y un alta sobrescribe una fila real. Pasó con
 * los horarios de sacd (usaban la secuencia de las propuestas), con las personas sssc y nax (las de
 * supernumerarios y numerarios) y con los procesos de sf (la de sv).
 *
 * Para cada `Pg*Repository` que usa `nextval('…')`, la secuencia tiene que ser el valor por defecto de
 * alguna columna de alguna de sus tablas (`setNomTabla`), mirado en el catálogo de la BD con la conexión
 * del propio repositorio. Las excepciones legítimas van en {@see self::EXCEPCIONES} con su motivo.
 *
 * Límite: en un repositorio que sirve a varias tablas (p. ej. procesos sv / sf) solo se comprueba que cada
 * secuencia sea de alguna de ellas, no que se use con la tabla que toca.
 */
final class SecuenciasDeRepositoriosTest extends myTest
{
    private const RAIZ = __DIR__ . '/../../../../';

    /** Secuencias que no son el DEFAULT de ninguna columna de la tabla del repositorio, con el motivo. */
    private const EXCEPCIONES = [
        'xu_dl_id_dl_seq' => 'xu_dl.id_dl no tiene DEFAULT; la secuencia existe en public y solo la usa este repositorio',
        'xd_desc_teleco_id_item_seq' => 'xd_desc_teleco.id_item no tiene DEFAULT; la secuencia existe en public',
        'xd_tipo_teleco_id_seq' => 'xd_tipo_teleco.id no tiene DEFAULT; la secuencia existe en public',
        'd_teleco_cdc_id_item_seq' => 'tabla padre d_teleco_cdc: sin DEFAULT; la secuencia existe en public',
        'd_teleco_ctr_id_item_seq' => 'tabla padre d_teleco_ctr (solo lectura con obj «Centro»): la secuencia no existe; un alta por este repositorio fallaría',
    ];

    /** @return iterable<string, array{string, string, list<string>, list<string>}> */
    public static function repositoriosProvider(): iterable
    {
        $raiz = realpath(self::RAIZ) . '/';
        foreach (glob($raiz . 'src/*/infrastructure/persistence/postgresql/Pg*Repository.php') ?: [] as $f) {
            $php = (string) file_get_contents($f);
            if (preg_match_all("/nextval\\('\"?([\\w.\"-]+?)\"?'/", $php, $ms) === 0) {
                continue;
            }
            $secuencias = array_values(array_unique(array_map(
                static fn (string $s): string => (string) preg_replace('/^.*\\./', '', str_replace('"', '', $s)),
                $ms[1]
            )));
            preg_match_all("/setNomTabla\\('([^']+)'\\)/", $php, $mt);
            $tablas = array_values(array_unique(array_map(
                static fn (string $t): string => (string) preg_replace('/^.*\\./', '', str_replace('"', '', $t)),
                $mt[1]
            )));
            if (preg_match("/GlobalPdo::get\\('(\\w+)'\\)/", $php, $mp) !== 1 || $tablas === []) {
                continue;
            }
            yield substr($f, strlen($raiz . 'src/')) => [$mp[1], basename($f), $tablas, $secuencias];
        }
    }

    /**
     * @param list<string> $tablas
     * @param list<string> $secuencias
     */
    #[DataProvider('repositoriosProvider')]
    public function test_la_secuencia_es_la_de_una_tabla_del_repositorio(string $pdo, string $repo, array $tablas, array $secuencias): void
    {
        $oDbl = GlobalPdo::get($pdo);
        $defaults = [];
        $existeAlguna = false;
        foreach ($tablas as $tabla) {
            $stmt = $oDbl->prepare('SELECT pg_get_expr(d.adbin, d.adrelid) FROM pg_attrdef d WHERE d.adrelid = to_regclass(:tabla)');
            $stmt->execute(['tabla' => $tabla]);
            $existe = $oDbl->prepare('SELECT to_regclass(:tabla) IS NOT NULL');
            $existe->execute(['tabla' => $tabla]);
            $existeAlguna = $existeAlguna || $existe->fetchColumn() === true;
            foreach ($stmt->fetchAll(\PDO::FETCH_COLUMN) as $expr) {
                $defaults[] = (string) $expr;
            }
        }
        if (!$existeAlguna) {
            $this->markTestSkipped("$repo: sus tablas (" . implode(', ', $tablas) . ') no están en el esquema de la sesión de los tests');
        }

        foreach ($secuencias as $secuencia) {
            if (isset(self::EXCEPCIONES[$secuencia])) {
                continue;
            }
            $esDefault = false;
            foreach ($defaults as $expr) {
                if (preg_match('/nextval\\(.*\\b' . preg_quote($secuencia, '/') . '\\b/', $expr) === 1) {
                    $esDefault = true;
                    break;
                }
            }
            $this->assertTrue(
                $esDefault,
                "$repo toma ids de «{$secuencia}», que no es el valor por defecto de ninguna columna de "
                . implode(' / ', $tablas) . '. Un id de otra secuencia puede existir ya en la tabla y sobrescribir una fila.'
            );
        }
        $this->addToAssertionCount(1);
    }

    public function test_las_excepciones_siguen_haciendo_falta(): void
    {
        $usadas = [];
        foreach (self::repositoriosProvider() as $caso) {
            foreach ($caso[3] as $secuencia) {
                $usadas[$secuencia] = true;
            }
        }
        $this->assertSame([], array_values(array_diff(array_keys(self::EXCEPCIONES), array_keys($usadas))), 'excepciones que ya no usa ningún repositorio');
    }
}
