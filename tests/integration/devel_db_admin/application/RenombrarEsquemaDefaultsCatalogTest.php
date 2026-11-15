<?php

declare(strict_types=1);

namespace Tests\integration\devel_db_admin\application;

use PDO;
use PHPUnit\Framework\Attributes\DataProvider;
use src\devel_db_admin\application\RenombrarEsquemaDefaultsCatalog;
use src\shared\config\ConfigGlobal;
use Tests\myTest;

/**
 * Test-guardia del catálogo de valores por defecto que se reescriben al renombrar un esquema.
 *
 * Las columnas `id_schema`, los ids globales y la dl o la región por defecto llevan el nombre del
 * esquema **como texto** (`idschema('H-dlb'::text)`); al renombrar el esquema hay que reescribirlas, y
 * `RenombrarEsquema` solo reescribe las que están en `RenombrarEsquemaDefaultsCatalog`. Si se añade
 * una tabla de dl y no se apunta ahí, tras un renombre sus altas seguirían pidiendo el id del nombre
 * antiguo. Faltaban las de encargos y misas, pasarela, certificados e inventario.
 *
 * Compara el catálogo con los defaults reales de los esquemas de delegación de la BD (los
 * `nextval(...)` no cuentan: van por el identificador de la secuencia y siguen al esquema solos).
 */
final class RenombrarEsquemaDefaultsCatalogTest extends myTest
{
    /** @return iterable<string, array{string, string}> conexión global y sufijo del esquema */
    public static function basesProvider(): iterable
    {
        yield 'comun' => ['oDBC', ''];
        yield 'sv' => ['oDB', 'v'];
        yield 'sv-e' => ['oDBE', 'v'];
    }

    /**
     * Se miran **todos** los esquemas de delegación de la BD, no solo el de la sesión: las tablas de un
     * módulo que solo tiene instalado alguna dl (p. ej. habitaciones) también cuentan.
     */
    #[DataProvider('basesProvider')]
    public function test_todo_default_con_el_nombre_del_esquema_esta_en_el_catalogo(string $conexion, string $sufijo): void
    {
        $pdo = $GLOBALS[$conexion];
        $this->assertInstanceOf(PDO::class, $pdo);
        $patron = $sufijo === '' ? '^[A-Za-z]+-[A-Za-z]+$' : '^[A-Za-z]+-[A-Za-z]+v$';
        $st = $pdo->query("SELECT nspname FROM pg_namespace WHERE nspname ~ '$patron' AND nspname !~ '^(pg_|zz)' ORDER BY 1");
        $esquemas = $st === false ? [] : $st->fetchAll(PDO::FETCH_COLUMN);
        // `mi_region_dl()` es el esquema de la sesión con su sufijo (p. ej. «H-dlbv»).
        $deLaSesion = (string) preg_replace('/[vf]$/', '', ConfigGlobal::mi_region_dl()) . $sufijo;
        $this->assertContains($deLaSesion, $esquemas, 'el esquema de la sesión está entre los revisados');

        $consulta = $pdo->prepare(
            "SELECT c.table_name || '.' || c.column_name, c.column_default
               FROM information_schema.columns c
               JOIN pg_tables t ON t.schemaname = c.table_schema AND t.tablename = c.table_name
              WHERE c.table_schema = :esquema AND c.column_default NOT LIKE '%nextval(%'
                AND (c.column_default LIKE '%' || :esquema || '%' OR c.column_default IN (:dl, :region))
              ORDER BY 1"
        );
        $faltan = [];
        foreach ($esquemas as $esquema) {
            $esquema = (string) $esquema;
            [$region, $dl] = explode('-', $sufijo === '' ? $esquema : substr($esquema, 0, -1), 2);
            $catalogo = match ($conexion) {
                'oDBC' => RenombrarEsquemaDefaultsCatalog::comun($esquema, $region, $dl),
                'oDB' => RenombrarEsquemaDefaultsCatalog::sv($esquema, $region, $dl),
                default => RenombrarEsquemaDefaultsCatalog::svE($esquema, $dl),
            };
            $enCatalogo = [];
            foreach ($catalogo as $entrada) {
                $enCatalogo[$entrada['tabla'] . '.' . $entrada['campo']] = true;
            }
            $consulta->execute(['esquema' => $esquema, 'dl' => "'$dl'::character varying", 'region' => "'$region'::character varying"]);
            foreach ($consulta->fetchAll(PDO::FETCH_NUM) as [$columna, $default]) {
                if (!isset($enCatalogo[$columna])) {
                    $faltan[(string) $columna] = "$columna = $default  (en «{$esquema}»)";
                }
            }
        }
        ksort($faltan);

        $this->assertSame([], array_values($faltan), "Columnas cuyo default lleva el nombre del esquema, la dl o la región y no están en RenombrarEsquemaDefaultsCatalog (tras un renombre seguirían con el nombre antiguo):\n" . implode("\n", $faltan));
    }
}
