<?php

declare(strict_types=1);

namespace Tests\integration\actividadcargos\infrastructure\persistence\postgresql;

use src\actividadcargos\domain\contracts\CargoOAsistenteInterface;
use src\actividades\domain\entity\ActividadAll;
use src\personas\domain\entity\PersonaSacd;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\infrastructure\GlobalPdo;
use Tests\myTest;

/**
 * Actividades de una persona como asistente (dl/out) o con cargo, y solapes de
 * fechas entre ellas. Filas propias en d_asistentes_dl/_out y d_cargos_activ_dl
 * (sin claves foráneas) con ids de prueba; se borran antes y después.
 */
final class PgCargoOAsistenteTest extends myTest
{
    private const NOM = 990777001;
    private const NOM2 = 990777002;
    private const A = 99800000;

    private CargoOAsistenteInterface $repo;
    private \PDO $db;

    public function setUp(): void
    {
        parent::setUp();
        $this->repo = $GLOBALS['container']->get(CargoOAsistenteInterface::class);
        $this->db = GlobalPdo::get('oDBE');
        $this->limpiar();
    }

    public function tearDown(): void
    {
        $this->limpiar();
        $this->db->exec('DROP TABLE IF EXISTS tmp_activ_solape; DROP TABLE IF EXISTS tmp_sacd_solape');
        parent::tearDown();
    }

    private function limpiar(): void
    {
        $noms = self::NOM . ',' . self::NOM2;
        $this->db->exec("DELETE FROM d_asistentes_dl WHERE id_nom IN ($noms)");
        $this->db->exec("DELETE FROM d_asistentes_out WHERE id_nom IN ($noms)");
        $this->db->exec("DELETE FROM d_cargos_activ_dl WHERE id_nom IN ($noms)");
    }

    private function asiste(string $tabla, int $activ, bool $propio, int $nom = self::NOM): void
    {
        $st = $this->db->prepare("INSERT INTO $tabla (id_activ, id_nom, propio) VALUES (?, ?, ?)");
        $st->execute([self::A + $activ, $nom, $propio ? 't' : 'f']);
    }

    private function cargo(int $activ, int $cargo, int $nom = self::NOM): void
    {
        $st = $this->db->prepare('INSERT INTO d_cargos_activ_dl (id_activ, id_cargo, id_nom) VALUES (?, ?, ?)');
        $st->execute([self::A + $activ, $cargo, $nom]);
    }

    public function test_actividades_como_asistente_o_con_cargo_ordenadas_y_sin_repetir(): void
    {
        $this->asiste('d_asistentes_dl', 3, true);
        $this->asiste('d_asistentes_out', 1, false);
        $this->cargo(2, 35);
        // Asiste y además tiene cargo: una sola fila, con el cargo.
        $this->asiste('d_asistentes_dl', 4, true);
        $this->cargo(4, 36);

        $res = $this->repo->getCargoOAsistente(self::NOM);

        // Antes: id_cargo sin inicializar (error al leerlo) y el cargo de la actividad 2 se perdía.
        $this->assertSame(
            [[self::A + 1, false, 0], [self::A + 2, false, 35], [self::A + 3, true, 0], [self::A + 4, true, 36]],
            array_map(fn ($c) => [$c->getId_activ(), $c->isPropio(), $c->getId_cargo()], $res)
        );
        $this->assertSame(self::NOM, $res[0]->getId_nom());
    }

    public function test_persona_sin_actividades(): void
    {
        $this->assertSame([], $this->repo->getCargoOAsistente(self::NOM));
    }

    private function actividad(int $n, string $ini, string $fin, ?int $ubi = 200): ActividadAll
    {
        $a = new ActividadAll();
        $a->setId_activ(self::A + $n);
        $a->setF_ini(new DateTimeLocal($ini));
        $a->setF_fin(new DateTimeLocal($fin));
        $a->setId_ubi($ubi);
        return $a;
    }

    private function sacd(int $id): PersonaSacd
    {
        $p = new PersonaSacd();
        $p->setId_nom($id);
        return $p;
    }

    public function test_solapes_de_fechas_por_persona(): void
    {
        // Persona 1: 1 y 2 se solapan; 3 no. Persona 2: sin solape.
        $this->asiste('d_asistentes_dl', 1, true);
        $this->cargo(2, 35);
        $this->asiste('d_asistentes_out', 3, false);
        $this->asiste('d_asistentes_dl', 1, true, self::NOM2);
        $this->asiste('d_asistentes_dl', 3, true, self::NOM2);
        $actividades = [
            $this->actividad(1, '2091-03-01', '2091-03-05'),
            $this->actividad(2, '2091-03-04', '2091-03-08'),
            $this->actividad(3, '2091-04-01', '2091-04-05'),
        ];

        $res = $this->repo->getSolapes([$this->sacd(self::NOM), $this->sacd(self::NOM2)], $actividades);

        $this->assertSame([self::NOM => [self::A + 1, self::A + 2]], $res);
    }

    public function test_acabar_y_empezar_el_mismo_dia_cuenta_como_solape(): void
    {
        $this->asiste('d_asistentes_dl', 1, true);
        $this->asiste('d_asistentes_dl', 2, true);
        $res = $this->repo->getSolapes([$this->sacd(self::NOM)], [
            $this->actividad(1, '2091-03-01', '2091-03-05'),
            $this->actividad(2, '2091-03-05', '2091-03-08'),
        ]);
        $this->assertSame([self::NOM => [self::A + 1, self::A + 2]], $res);
    }

    public function test_actividades_fuera_de_la_lista_no_cuentan(): void
    {
        $this->asiste('d_asistentes_dl', 1, true);
        $this->asiste('d_asistentes_dl', 2, true);
        $res = $this->repo->getSolapes([$this->sacd(self::NOM)], [$this->actividad(1, '2091-03-01', '2091-03-05')]);
        $this->assertSame([], $res);
    }

    public function test_dos_llamadas_seguidas_no_arrastran_los_datos_de_la_primera(): void
    {
        $this->asiste('d_asistentes_dl', 1, true);
        $this->asiste('d_asistentes_dl', 2, true);
        $solapan = [$this->actividad(1, '2091-03-01', '2091-03-05'), $this->actividad(2, '2091-03-04', '2091-03-08')];
        $this->assertNotSame([], $this->repo->getSolapes([$this->sacd(self::NOM)], $solapan));

        $noSolapan = [$this->actividad(1, '2091-03-01', '2091-03-05'), $this->actividad(2, '2091-04-04', '2091-04-08')];
        $this->assertSame([], $this->repo->getSolapes([$this->sacd(self::NOM)], $noSolapan));
    }
}
