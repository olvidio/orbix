<?php

declare(strict_types=1);

namespace Tests\unit\menus\infrastructure;

use PDO;
use PDOException;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use src\menus\infrastructure\persistence\postgresql\PgMenusPlantillaImportador;

/**
 * `PgMenusPlantillaImportador` con conexiones simuladas: sin menús en la plantilla no toca nada, y un fallo a mitad de
 * la copia deshace la transacción (la importación real está en `MenusImportarControllerTest`).
 */
final class PgMenusPlantillaImportadorTest extends TestCase
{
    /** Conexión de comun que devuelve las filas indicadas para cada tabla `ref_*`. */
    private function comun(array $menus): PDO
    {
        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willReturnCallback(function (string $sql) use ($menus) {
            $st = $this->createMock(PDOStatement::class);
            $st->method('execute')->willReturn(true);
            $st->method('fetchAll')->willReturn(str_contains($sql, 'FROM ref_menus') ? $menus : [['id_grupmenu' => 1, 'grup_menu' => 'g', 'orden' => 1]]);

            return $st;
        });

        return $pdo;
    }

    public function test_sin_menus_en_la_plantilla_no_toca_la_conexion_de_destino(): void
    {
        $destino = $this->createMock(PDO::class);
        $destino->expects($this->never())->method('beginTransaction');
        $destino->expects($this->never())->method('exec');
        $destino->expects($this->never())->method('prepare');

        $error = (new PgMenusPlantillaImportador($destino, $this->comun([])))->importar(5);

        $this->assertSame('La plantilla de menús no tiene menús: no se ha cambiado nada.', $error);
    }

    public function test_si_no_se_puede_leer_la_plantilla_no_toca_nada(): void
    {
        $comun = $this->createMock(PDO::class);
        $comun->method('prepare')->willReturn(false);
        $destino = $this->createMock(PDO::class);
        $destino->expects($this->never())->method('exec');

        $error = (new PgMenusPlantillaImportador($destino, $comun))->importar(5);

        $this->assertStringStartsWith('No se ha podido leer la plantilla de menús: no se ha cambiado nada.', $error);
    }

    public function test_un_fallo_a_mitad_de_la_copia_deshace_todo(): void
    {
        $log = [];
        $destino = $this->createMock(PDO::class);
        $destino->method('inTransaction')->willReturn(false);
        $destino->method('getAttribute')->willReturn(PDO::ERRMODE_SILENT);
        $destino->method('beginTransaction')->willReturnCallback(function () use (&$log) {
            $log[] = 'BEGIN';

            return true;
        });
        $destino->method('exec')->willReturnCallback(function (string $sql) use (&$log) {
            $log[] = $sql;

            return 0;
        });
        $destino->method('query')->willReturn($this->createMock(PDOStatement::class));
        $destino->method('prepare')->willReturnCallback(function (string $sql) {
            $st = $this->createMock(PDOStatement::class);
            if (str_contains($sql, 'aux_menus')) {
                $st->method('execute')->willThrowException(new PDOException('fallo al insertar'));
            } else {
                $st->method('execute')->willReturn(true);
            }

            return $st;
        });
        $destino->expects($this->never())->method('commit');
        $destino->expects($this->once())->method('rollBack')->willReturnCallback(function () use (&$log) {
            $log[] = 'ROLLBACK';

            return true;
        });

        $error = (new PgMenusPlantillaImportador($destino, $this->comun([['id_menu' => 1, 'orden' => '{1}', 'menu' => 'm', 'parametros' => '', 'id_metamenu' => 1, 'menu_perm' => 1, 'id_grupmenu' => 1, 'ok' => 't']])))->importar(5);

        $this->assertSame("Hay un error, no se han importado los menús: siguen los de antes.\nfallo al insertar", $error);
        $this->assertSame('BEGIN', $log[0]);
        $this->assertSame('ROLLBACK', end($log));
    }
}
