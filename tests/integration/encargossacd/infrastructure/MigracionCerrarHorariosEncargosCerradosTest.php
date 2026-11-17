<?php

declare(strict_types=1);

namespace Tests\integration\encargossacd\infrastructure;

use PDO;
use src\shared\infrastructure\GlobalPdo;
use Tests\myTest;

/**
 * Migración que cierra los horarios (`encargo_sacd_horario`) que quedaron abiertos con su encargo
 * (`encargos_sacd`) ya cerrado: `PropuestasAprobarService` cerraba el encargo sin cerrar sus horarios.
 *
 * Ejecuta el SQL del fichero de migración (con el comodín `*.` sustituido por el esquema de la sesión)
 * dentro de una transacción que se deshace, sobre filas propias.
 */
final class MigracionCerrarHorariosEncargosCerradosTest extends myTest
{
    private const MIGRACION = __DIR__ . '/../../../../db/migrations/202611171500_encargo_sacd_horario_cerrar_los_de_encargos_cerrados__sv-e.sql';
    private const ID_ENC = 999990;

    private PDO $pdo;
    private string $esquema;

    public function setUp(): void
    {
        parent::setUp();
        $this->pdo = GlobalPdo::get('oDBE');
        $this->esquema = (string) $this->pdo->query('SELECT current_schema()')->fetchColumn();
        $this->pdo->beginTransaction();
    }

    public function tearDown(): void
    {
        if ($this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }
        parent::tearDown();
    }

    private function encargo(int $idNom, string $fIni, ?string $fFin): int
    {
        $st = $this->pdo->prepare("INSERT INTO \"{$this->esquema}\".encargos_sacd (id_enc, id_nom, modo, f_ini, f_fin) VALUES (:enc, :nom, 3, :ini, :fin) RETURNING id_item");
        $st->execute(['enc' => self::ID_ENC, 'nom' => $idNom, 'ini' => $fIni, 'fin' => $fFin]);

        return (int) $st->fetchColumn();
    }

    private function horario(int $idTarea, int $idNom, string $fIni, ?string $fFin): int
    {
        $st = $this->pdo->prepare("INSERT INTO \"{$this->esquema}\".encargo_sacd_horario (id_enc, id_nom, f_ini, f_fin, dia_ref, dia_inc, id_item_tarea_sacd) VALUES (:enc, :nom, :ini, :fin, 'm', 3, :tarea) RETURNING id_item");
        $st->execute(['enc' => self::ID_ENC, 'nom' => $idNom, 'ini' => $fIni, 'fin' => $fFin, 'tarea' => $idTarea]);

        return (int) $st->fetchColumn();
    }

    private function finDe(int $idHorario): ?string
    {
        $v = $this->pdo->query("SELECT f_fin FROM \"{$this->esquema}\".encargo_sacd_horario WHERE id_item = $idHorario")->fetchColumn();

        return $v === null || $v === false ? null : (string) $v;
    }

    private function aplicarMigracion(): void
    {
        $this->assertFileExists(self::MIGRACION);
        $sql = str_replace('*.', '"' . $this->esquema . '".', (string) file_get_contents(self::MIGRACION));
        $this->pdo->exec($sql);
    }

    public function test_cierra_los_horarios_abiertos_de_encargos_cerrados_con_la_fecha_del_encargo(): void
    {
        $cerrado = $this->encargo(10011, '2020-01-01', '2024-06-30');
        $abierto = $this->encargo(10012, '2020-01-01', null);
        $hAbiertoDeCerrado = $this->horario($cerrado, 10011, '2020-01-01', null);
        $hYaCerrado = $this->horario($cerrado, 10011, '2020-01-01', '2022-03-31');
        $hDeAbierto = $this->horario($abierto, 10012, '2020-01-01', null);

        $this->aplicarMigracion();

        $this->assertSame('2024-06-30', $this->finDe($hAbiertoDeCerrado), 'el abierto de un encargo cerrado se cierra con la fecha del encargo');
        $this->assertSame('2022-03-31', $this->finDe($hYaCerrado), 'el que ya estaba cerrado conserva su fecha');
        $this->assertNull($this->finDe($hDeAbierto), 'el de un encargo abierto no se toca');

        $this->aplicarMigracion();
        $this->assertSame('2024-06-30', $this->finDe($hAbiertoDeCerrado), 'se puede volver a aplicar sin cambiar nada');
    }
}
