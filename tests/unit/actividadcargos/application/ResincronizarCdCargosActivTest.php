<?php

declare(strict_types=1);

namespace Tests\unit\actividadcargos\application;

use PDO;
use PHPUnit\Framework\TestCase;
use src\actividadcargos\application\ResincronizarCdCargosActiv;
use src\actividadcargos\infrastructure\persistence\postgresql\CdCargosActivContexto;
use src\actividadcargos\infrastructure\persistence\postgresql\CdCargosActivWriter;
use src\utils_database\domain\contracts\DbSchemaRepositoryInterface;

/**
 * Lo propio de la copia `cd_cargos_activ_dl` dentro del motor de reconciliación
 * (el motor en sí se prueba en `tests/unit/shared/application/copias`).
 */
final class ResincronizarCdCargosActivTest extends TestCase
{
    private function sut(): ResincronizarCdCargosActiv
    {
        return new ResincronizarCdCargosActiv(
            $this->createMock(DbSchemaRepositoryInterface::class),
            $this->createMock(CdCargosActivWriter::class),
        );
    }

    private function protegido(object $o, string $metodo, mixed ...$args): mixed
    {
        $m = new \ReflectionMethod($o, $metodo);
        $m->setAccessible(true);

        return $m->invoke($o, ...$args);
    }

    private function origen(): PDO
    {
        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec('CREATE TABLE d_cargos_activ_dl (id_schema INTEGER, id_item INTEGER, id_activ INTEGER, id_cargo INTEGER, id_nom INTEGER, puede_agd INTEGER, observ TEXT)');
        $ins = $pdo->prepare('INSERT INTO d_cargos_activ_dl VALUES (?, ?, ?, ?, ?, ?, ?)');
        foreach ([
            [3004, 11, 3001, 1, 10011, 1, 'uno'],
            [3004, 12, 3001, 2, null, 0, null],
            [3004, 0, 3002, 1, 10012, 0, 'sin id_item'],
            [3004, null, 3003, 1, 10013, 0, 'id_item nulo'],
        ] as $fila) {
            $ins->execute($fila);
        }

        return $pdo;
    }

    public function test_lee_de_la_base_sv_e_y_del_esquema_con_sufijo_v(): void
    {
        $sut = $this->sut();

        $this->assertSame('publicv-e', $this->protegido($sut, 'claveEsquemaOrigen'));
        $this->assertSame('sv-e', $this->protegido($sut, 'nombreBaseOrigen'));
        $this->assertSame('H-dlbv', $this->protegido($sut, 'esquemaOrigenDe', 'H-dlb'));
    }

    public function test_las_bajas_se_aplican_antes_que_las_altas(): void
    {
        $this->assertTrue($this->protegido($this->sut(), 'bajasAntesDeAltas'));
    }

    public function test_el_contexto_apunta_a_la_copia_del_esquema_y_nunca_lo_omite(): void
    {
        $pdo = new PDO('sqlite::memory:');

        $contexto = $this->protegido($this->sut(), 'contextoDe', $pdo, 'H-dlb', 3004);

        $this->assertInstanceOf(CdCargosActivContexto::class, $contexto);
        $this->assertSame('"H-dlb".cd_cargos_activ_dl', $contexto->tabla());
        $this->assertSame(3004, $contexto->id_schema);
        $this->assertSame($pdo, $contexto->pdoComun);
    }

    public function test_el_origen_son_todos_los_cargos_con_id_item_indexados_por_el(): void
    {
        $pdo = $this->origen();
        $contexto = new CdCargosActivContexto($pdo, 'main', 3004);

        $filas = $this->protegido($this->sut(), 'leerOrigen', $pdo, 'main', $contexto);

        $this->assertSame([11, 12], array_keys($filas), 'las filas sin id_item utilizable no se copian');
        $this->assertSame(['id_item', 'id_activ', 'id_cargo', 'id_nom', 'puede_agd', 'observ'], array_keys($filas[11]), 'solo las columnas de la copia (sin id_schema)');
        $this->assertSame(3001, (int) $filas[11]['id_activ']);
        $this->assertSame(10011, (int) $filas[11]['id_nom']);
        $this->assertSame('uno', $filas[11]['observ']);
        $this->assertNull($filas[12]['id_nom']);
        $this->assertNull($filas[12]['observ']);
    }

    public function test_un_origen_sin_filas_devuelve_lista_vacia(): void
    {
        $pdo = $this->origen();
        $pdo->exec('DELETE FROM d_cargos_activ_dl');

        $this->assertSame([], $this->protegido($this->sut(), 'leerOrigen', $pdo, 'main', new CdCargosActivContexto($pdo, 'main')));
    }

    /**
     * Una tabla que falta no puede confundirse con un origen vacío (con `--aplicar`
     * se borraría la copia entera). SQLite no da los SQLSTATE de PostgreSQL, así
     * que aquí llega la PDOException original; lo que importa es que no se traga.
     */
    public function test_si_falta_la_tabla_de_origen_se_lanza_excepcion(): void
    {
        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->expectException(\PDOException::class);
        $this->protegido($this->sut(), 'leerOrigen', $pdo, 'main', new CdCargosActivContexto($pdo, 'main'));
    }

    /** Con el SQLSTATE de PostgreSQL («no existe la tabla / el esquema») el mensaje dice qué falta y dónde. */
    public function test_tabla_o_esquema_inexistentes_en_postgres_dan_un_mensaje_legible(): void
    {
        foreach (['42P01', '3F000'] as $sqlstate) {
            $e = new \PDOException('relation does not exist');
            (new \ReflectionProperty(\Exception::class, 'code'))->setValue($e, $sqlstate);
            $pdo = $this->createMock(PDO::class);
            $pdo->method('query')->willThrowException($e);

            try {
                $this->protegido($this->sut(), 'leerOrigen', $pdo, 'H-dlbv', new CdCargosActivContexto($pdo, 'H-dlb'));
                $this->fail('debía lanzar');
            } catch (\RuntimeException $r) {
                $this->assertNotInstanceOf(\PDOException::class, $r);
                $this->assertSame('no existe H-dlbv.d_cargos_activ_dl en sv-e', $r->getMessage());
                $this->assertSame($e, $r->getPrevious());
            }
        }
    }

    public function test_otro_error_de_postgres_se_propaga_tal_cual(): void
    {
        $e = new \PDOException('permission denied');
        (new \ReflectionProperty(\Exception::class, 'code'))->setValue($e, '42501');
        $pdo = $this->createMock(PDO::class);
        $pdo->method('query')->willThrowException($e);

        try {
            $this->protegido($this->sut(), 'leerOrigen', $pdo, 'H-dlbv', new CdCargosActivContexto($pdo, 'H-dlb'));
            $this->fail('debía lanzar');
        } catch (\PDOException $r) {
            $this->assertSame($e, $r);
        }
    }

    public function test_si_query_devuelve_false_se_lanza_excepcion(): void
    {
        $pdo = $this->createMock(PDO::class);
        $pdo->method('query')->willReturn(false);

        $this->expectException(\RuntimeException::class);
        $this->protegido($this->sut(), 'leerOrigen', $pdo, 'H-dlbv', new CdCargosActivContexto($pdo, 'H-dlb'));
    }
}
