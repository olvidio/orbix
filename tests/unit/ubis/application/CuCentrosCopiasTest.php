<?php

declare(strict_types=1);

namespace Tests\unit\ubis\application;

use PDO;
use PHPUnit\Framework\MockObject\MockObject;
use src\ubis\application\ResincronizarCuCentrosDl;
use src\ubis\application\ResincronizarCuCentrosDlf;
use src\ubis\application\services\SincronizarCuCentros;
use src\ubis\domain\entity\CentroDl;
use src\ubis\infrastructure\persistence\postgresql\CuCentrosContexto;
use src\ubis\infrastructure\persistence\postgresql\CuCentrosDlfWriter;
use src\ubis\infrastructure\persistence\postgresql\CuCentrosDlWriter;
use src\utils_database\domain\contracts\DbSchemaRepositoryInterface;
use Tests\myTest;

/** Copias de centros en la BD comun (`SincronizarCuCentros` y la base `ResincronizarCuCentros` a través de sus dos reconciliadores): reparto sv/sf por el primer dígito de `id_ubi` y reconciliación desde el origen. */
final class CuCentrosCopiasTest extends myTest
{
    private CuCentrosDlWriter&MockObject $writerSv;
    private CuCentrosDlfWriter&MockObject $writerSf;

    public function setUp(): void
    {
        parent::setUp();
        $this->writerSv = $this->createMock(CuCentrosDlWriter::class);
        $this->writerSf = $this->createMock(CuCentrosDlfWriter::class);
    }

    private function centro(int $idUbi): CentroDl
    {
        $c = new CentroDl();
        $c->setId_ubi($idUbi);
        $c->setNombre_ubi('Centro ' . $idUbi);
        $c->setDl('dlb');
        $c->setActive(true);

        return $c;
    }

    private function sincronizador(): SincronizarCuCentros
    {
        return new SincronizarCuCentros($this->writerSv, $this->writerSf);
    }

    // ---- sincronización incremental ----------------------------------------------------------------------

    public function test_un_centro_de_sv_se_escribe_en_cu_centros_dl(): void
    {
        $this->writerSf->expects($this->never())->method('upsert');
        $fila = null;
        $contexto = null;
        $this->writerSv->expects($this->once())->method('upsert')->willReturnCallback(function ($ctx, array $f) use (&$fila, &$contexto): bool {
            $contexto = $ctx;
            $fila = $f;

            return true;
        });

        $this->assertTrue($this->sincronizador()->sincronizarCentro($this->centro(1003)));

        $this->assertSame('cu_centros_dl', $contexto->nombreTabla());
        $this->assertSame(1003, (int) $fila['id_ubi']);
        $this->assertSame('Centro 1003', $fila['nombre_ubi']);
        $this->assertSame(['id_ubi', 'tipo_ubi', 'nombre_ubi', 'dl', 'pais', 'region', 'active', 'f_active', 'sv', 'sf', 'tipo_ctr', 'tipo_labor', 'cdc', 'id_ctr_padre'], array_keys($fila));
    }

    public function test_un_centro_de_sf_se_escribe_en_cu_centros_dlf(): void
    {
        $this->writerSv->expects($this->never())->method('upsert');
        $contexto = null;
        $this->writerSf->expects($this->once())->method('upsert')->willReturnCallback(function ($ctx) use (&$contexto): bool {
            $contexto = $ctx;

            return true;
        });

        $this->assertTrue($this->sincronizador()->sincronizarCentro($this->centro(2003)));

        $this->assertSame('cu_centros_dlf', $contexto->nombreTabla());
    }

    public function test_un_fallo_del_writer_se_propaga_como_false(): void
    {
        $this->writerSv->method('upsert')->willReturn(false);

        $this->assertFalse($this->sincronizador()->sincronizarCentro($this->centro(1003)));
    }

    public function test_un_centro_sin_id_no_se_copia(): void
    {
        $this->writerSv->expects($this->never())->method('upsert');
        $this->writerSf->expects($this->never())->method('upsert');
        $this->writerSv->expects($this->never())->method('eliminar');

        $this->assertFalse($this->sincronizador()->sincronizarCentro($this->centro(0)));
        $this->assertFalse($this->sincronizador()->eliminarCentro($this->centro(0)));
    }

    public function test_eliminar_quita_el_centro_de_la_copia_que_le_corresponde(): void
    {
        $this->writerSv->expects($this->once())->method('eliminar')->with($this->anything(), 1003)->willReturn(true);
        $this->writerSf->expects($this->once())->method('eliminar')->with($this->anything(), 2003)->willReturn(true);

        $this->assertTrue($this->sincronizador()->eliminarCentro($this->centro(1003)));
        $this->assertTrue($this->sincronizador()->eliminarCentro($this->centro(2003)));
    }

    // ---- reconciliación ----------------------------------------------------------------------

    private function reconciliador(bool $sf): ResincronizarCuCentrosDl|ResincronizarCuCentrosDlf
    {
        $schemas = $this->createMock(DbSchemaRepositoryInterface::class);

        return $sf
            ? new ResincronizarCuCentrosDlf($schemas, $this->createMock(CuCentrosDlfWriter::class))
            : new ResincronizarCuCentrosDl($schemas, $this->createMock(CuCentrosDlWriter::class));
    }

    /**
     * @return mixed
     */
    private function protegido(object $o, string $metodo, mixed ...$args): mixed
    {
        $m = new \ReflectionMethod($o, $metodo);
        $m->setAccessible(true);

        return $m->invoke($o, ...$args);
    }

    public function test_cada_reconciliador_declara_su_base_su_clave_y_el_sufijo_de_esquema(): void
    {
        $sv = $this->reconciliador(false);
        $sf = $this->reconciliador(true);

        $this->assertSame(['publicv', 'sv', 'H-dlbv'], [$this->protegido($sv, 'claveEsquemaOrigen'), $this->protegido($sv, 'nombreBaseOrigen'), $this->protegido($sv, 'esquemaOrigenDe', 'H-dlb')]);
        $this->assertSame(['publicf', 'sf', 'H-dlbf'], [$this->protegido($sf, 'claveEsquemaOrigen'), $this->protegido($sf, 'nombreBaseOrigen'), $this->protegido($sf, 'esquemaOrigenDe', 'H-dlb')]);
    }

    public function test_el_contexto_de_cada_reconciliador_apunta_a_su_tabla(): void
    {
        $pdo = new PDO('sqlite::memory:');

        $this->assertSame('cu_centros_dl', $this->protegido($this->reconciliador(false), 'contextoDe', $pdo, 'H-dlb', 7)->nombreTabla());
        $this->assertSame('cu_centros_dlf', $this->protegido($this->reconciliador(true), 'contextoDe', $pdo, 'H-dlb', 7)->nombreTabla());
        $this->assertSame(7, $this->protegido($this->reconciliador(true), 'contextoDe', $pdo, 'H-dlb', 7)->id_schema);
    }

    private function origen(): PDO
    {
        $pdo = new PDO('sqlite::memory:');
        $pdo->exec('CREATE TABLE u_centros_dl (id_ubi INTEGER, tipo_ubi TEXT, nombre_ubi TEXT, dl TEXT, pais TEXT, region TEXT, active INTEGER, f_active TEXT, sv INTEGER, sf INTEGER, tipo_ctr TEXT, tipo_labor INTEGER, cdc INTEGER, id_ctr_padre INTEGER, n_buzon TEXT)');
        $ins = $pdo->prepare('INSERT INTO u_centros_dl (id_ubi, tipo_ubi, nombre_ubi, dl, region, active, sv, sf, n_buzon) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        foreach ([[1003, 'ctrdl', 'Centro sv', 'dlb', 'H', 1, 1, 0, 'x'], [2003, 'ctrsf', 'Centro sf', 'dlb', 'H', 1, 0, 1, 'y'], [0, 'ctrdl', 'Sin id', 'dlb', 'H', 1, 1, 0, 'z']] as $fila) {
            $ins->execute($fila);
        }

        return $pdo;
    }

    public function test_cada_reconciliador_se_queda_solo_con_los_centros_de_su_lado(): void
    {
        $pdo = $this->origen();
        $contexto = CuCentrosContexto::paraSv($pdo, 'main');

        $sv = $this->protegido($this->reconciliador(false), 'leerOrigen', $pdo, 'main', $contexto);
        $sf = $this->protegido($this->reconciliador(true), 'leerOrigen', $pdo, 'main', $contexto);

        $this->assertSame([1003], array_keys($sv));
        $this->assertSame([2003], array_keys($sf));
        $this->assertSame('Centro sv', $sv[1003]['nombre_ubi']);
        $this->assertArrayNotHasKey('n_buzon', $sv[1003], 'solo las columnas que se copian');
    }

    public function test_si_falta_la_tabla_de_origen_el_esquema_se_marca_como_error_no_como_vacio(): void
    {
        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $contexto = CuCentrosContexto::paraSv($pdo, 'main');

        // SQLite no devuelve los SQLSTATE de PostgreSQL: llega la PDOException original, que tampoco se traga.
        $this->expectException(\PDOException::class);
        $this->protegido($this->reconciliador(false), 'leerOrigen', $pdo, 'main', $contexto);
    }
}
