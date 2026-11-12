<?php

declare(strict_types=1);

namespace Tests\integration\actividadcargos\infrastructure\persistence\postgresql;

use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\shared\infrastructure\GlobalPdo;
use Tests\myTest;

/**
 * Sacd de una actividad (copia en comun `cd_cargos_activ_dl`): ids y personas,
 * orden por cargo, solo cargos de tipo sacd, y que una persona sin acceso no
 * escribe nada en la salida (la usan endpoints JSON).
 *
 * Filas propias con id_item 99800001..99800099 (actividad 99800001, y una en la
 * actividad real 300124996); se borran antes y después solo por esos id_item.
 */
final class PgActividadCargoDlSacdsTest extends myTest
{
    private const ACTIV = 99800001;
    private const SACD_REAL = 10012925;
    private const NADIE = 990777001;

    private ActividadCargoRepositoryInterface $repo;
    private \PDO $comun;
    private mixed $sesion = null;

    public function setUp(): void
    {
        parent::setUp();
        $this->repo = $GLOBALS['container']->get(ActividadCargoRepositoryInterface::class);
        $this->comun = GlobalPdo::get('oDBC');
        $this->sesion = $_SESSION['session_auth'] ?? null;
        $this->limpiar();
        $st = $this->comun->prepare('INSERT INTO cd_cargos_activ_dl (id_item, id_activ, id_cargo, id_nom) VALUES (?, ?, ?, ?)');
        $st->execute([99800001, self::ACTIV, 36, self::NADIE]);
        $st->execute([99800002, self::ACTIV, 35, self::SACD_REAL]);
        $st->execute([99800003, self::ACTIV, 5, 990777002]);
    }

    public function tearDown(): void
    {
        $this->limpiar();
        $_SESSION['session_auth'] = $this->sesion;
        parent::tearDown();
    }

    private function limpiar(): void
    {
        $this->comun->exec('DELETE FROM cd_cargos_activ_dl WHERE id_item BETWEEN 99800001 AND 99800099');
    }

    public function test_ids_de_los_sacd_por_orden_de_cargo_y_solo_cargos_de_sacd(): void
    {
        $this->assertSame([self::SACD_REAL, self::NADIE], $this->repo->getActividadIdSacds(self::ACTIV));
        $this->assertSame([], $this->repo->getActividadIdSacds(99800099));
    }

    public function test_personas_de_los_sacd_salta_las_que_no_encuentra(): void
    {
        $personas = $this->repo->getActividadSacds(self::ACTIV);
        $this->assertCount(1, $personas);
        $this->assertSame(self::SACD_REAL, $personas[0]->getId_nom());
    }

    public function test_sin_acceso_a_la_persona_no_escribe_en_la_salida(): void
    {
        // Actividad real (300124996, cv agd 2027) con una persona a la que no se tiene acceso.
        // En sf no se busca en las tablas de sv: antes se hacía echo del aviso y rompía el JSON.
        $this->comun->prepare('INSERT INTO cd_cargos_activ_dl (id_item, id_activ, id_cargo, id_nom) VALUES (?, ?, ?, ?)')
            ->execute([99800010, 300124996, 37, self::NADIE]);
        $_SESSION['session_auth']['sfsv'] = 2;
        $this->expectOutputString('');
        $ids = array_map(fn ($p) => $p->getId_nom(), $this->repo->getActividadSacds(300124996));
        $this->assertNotContains(self::NADIE, $ids);
    }
}
