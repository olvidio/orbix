<?php

declare(strict_types=1);

namespace Tests\integration\tools\codegen;

use InvalidArgumentException;
use PDO;
use PHPUnit\Framework\Attributes\DataProvider;
use src\shared\infrastructure\GlobalPdo;
use Tests\myTest;
use tools\codegen\TablaEstructuraPg;

require_once dirname(__DIR__, 4) . '/tools/codegen/TablaEstructuraPg.php';

/**
 * El lector de la estructura de una tabla da lo mismo que las consultas que hacía
 * el generador de código (copiadas abajo tal cual, con `query()` y el nombre de la tabla en el SQL)
 * cuando era una pantalla (`frontend/devel_codegen/controller/factory.php`).
 */
final class TablaEstructuraPgTest extends myTest
{
    /** @return iterable<string, array{string, string, string}> */
    public static function tablasProvider(): iterable
    {
        yield 'comun, casas de la dl' => ['comun', 'oDBC', 'H-dlb.u_cdc_dl'];
        yield 'sv, asignaturas de actividad' => ['sv', 'oDB', 'H-dlbv.d_asignaturas_activ_dl'];
    }

    #[DataProvider('tablasProvider')]
    public function test_da_lo_mismo_que_las_consultas_de_antes(string $db, string $global, string $tablaConEsquema): void
    {
        $oDbl = GlobalPdo::get($global);
        [$schema, $tabla] = explode('.', $tablaConEsquema, 2);

        $nueva = (new TablaEstructuraPg())->leer($db, $tablaConEsquema);

        $this->assertSame(self::primaryKeyAntes($oDbl, $tablaConEsquema), $nueva['primary_key']);
        $antes = self::columnasAntes($oDbl, $schema, $tabla);
        $this->assertNotEmpty($antes);
        $this->assertSame($antes, $nueva['columnas']);
    }

    public function test_base_de_datos_desconocida(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new TablaEstructuraPg())->leer('otra', 'public.x');
    }

    /** @return list<string> primaryKey() del antiguo frontend/devel_codegen/controller/func_factory.php */
    private static function primaryKeyAntes(PDO $oDB, string $tabla): array
    {
        $schema = strtok($tabla, '.');
        if ($schema !== $tabla) {
            $tabla = strtok('.');
            $schema = "\"" . $schema . "\"";
        }
        $schema_tabla = $schema . '.' . $tabla;
        $query_primaria = " SELECT a.attname
            FROM   pg_index i
            JOIN   pg_attribute a ON a.attrelid = i.indrelid
            AND a.attnum = ANY(i.indkey)
            WHERE  i.indrelid = '$schema_tabla'::regclass
            AND    i.indisprimary; ";
        $row = $oDB->query($query_primaria)->fetch(PDO::FETCH_ASSOC);

        return [(string) $row['attname']];
    }

    /** @return list<array<string, mixed>> columnas y valor por defecto como en factory.php */
    private static function columnasAntes(PDO $oDbl, string $schema, string $tabla): array
    {
        $sql = "SELECT a.attnum, a.attname AS field, t.typname AS type, a.attlen AS length,
                a.atttypmod AS lengthvar, a.attnotnull AS notnull
            FROM pg_catalog.pg_class c, pg_catalog.pg_attribute a, pg_catalog.pg_type t, pg_catalog.pg_namespace n
            WHERE c.relname = '$tabla' and a.attnum > 0 and a.attrelid = c.oid and a.atttypid = t.oid
                and n.oid = c.relnamespace and n.nspname='$schema'
            ORDER BY a.attnum";
        $out = [];
        foreach ($oDbl->query($sql)->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $sql_get_default = "SELECT pg_get_expr(adbin, adrelid) AS rowdefault
                FROM pg_catalog.pg_attrdef d, pg_catalog.pg_class c, pg_catalog.pg_namespace n
                WHERE c.relname = '$tabla' and c.oid = d.adrelid and n.oid = c.relnamespace
                    and n.nspname='$schema' and d.adnum =" . $row['attnum'];
            $default = $oDbl->query($sql_get_default)->fetchColumn();
            $out[] = [
                'attnum' => (int) $row['attnum'],
                'field' => (string) $row['field'],
                'type' => (string) $row['type'],
                'length' => (int) $row['length'],
                'lengthvar' => (int) $row['lengthvar'],
                'notnull' => (bool) $row['notnull'],
                'default' => is_string($default) ? $default : '',
            ];
        }

        return $out;
    }
}
