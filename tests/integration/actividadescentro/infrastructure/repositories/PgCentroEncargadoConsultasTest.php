<?php

declare(strict_types=1);

namespace Tests\integration\actividadescentro\infrastructure\persistence\postgresql;

use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\shared\infrastructure\GlobalPdo;
use Tests\myTest;

/**
 * Consultas de PgCentroEncargadoRepository: centros encargados de una
 * actividad (por orden), actividades de un centro con condición de periodo y
 * distancia a las actividades próximas del centro.
 *
 * Usa la actividad real 300124996 (cv agd 2027, sin centros encargados) y dos
 * centros reales; las filas de da_ctr_encargados que crea se borran antes y después.
 */
final class PgCentroEncargadoConsultasTest extends myTest
{
    private const ACTIV = 300124996;
    private const CTR_A = 1010810;
    private const CTR_B = 1010819;

    private CentroEncargadoRepositoryInterface $repo;
    private \PDO $comun;

    public function setUp(): void
    {
        parent::setUp();
        $this->repo = $GLOBALS['container']->get(CentroEncargadoRepositoryInterface::class);
        $this->comun = GlobalPdo::get('oDBC');
        $this->limpiar();
        $st = $this->comun->prepare('INSERT INTO da_ctr_encargados (id_activ, id_ubi, num_orden) VALUES (?, ?, ?)');
        $st->execute([self::ACTIV, self::CTR_B, 2]);
        $st->execute([self::ACTIV, self::CTR_A, 1]);
    }

    public function tearDown(): void
    {
        $this->limpiar();
        parent::tearDown();
    }

    private function limpiar(): void
    {
        $this->comun->exec('DELETE FROM da_ctr_encargados WHERE id_activ = ' . self::ACTIV . ' AND id_ubi IN (' . self::CTR_A . ',' . self::CTR_B . ')');
    }

    public function test_centros_encargados_por_orden(): void
    {
        $centros = $this->repo->getCentrosEncargadosActividad(self::ACTIV);
        $this->assertSame([self::CTR_A, self::CTR_B], array_map(fn ($c) => $c->getId_ubi(), $centros));
        $this->assertSame('La Cadiera', $centros[0]->getNombre_ubi());
        $this->assertSame([], $this->repo->getCentrosEncargadosActividad(99800099));
    }

    public function test_actividades_de_un_centro_con_y_sin_periodo(): void
    {
        $ids = fn (string $cond): array => array_map(fn ($a) => $a->getId_activ(), $this->repo->getActividadesDeCentros(self::CTR_A, $cond));
        $this->assertContains(self::ACTIV, $ids(''));
        $this->assertContains(self::ACTIV, $ids("f_ini BETWEEN '2027-06-01' AND '2027-06-30'"));
        $this->assertNotContains(self::ACTIV, $ids("f_ini BETWEEN '2027-07-01' AND '2027-07-31'"));
    }

    public function test_proximas_actividades_del_centro(): void
    {
        // La actividad empieza el 14/6/2027: 4 días después del 10/6.
        $txt = $this->repo->getProximasActividadesDeCentro(self::CTR_A, '2027-06-10');
        $this->assertStringContainsString(' 4;', $txt);
        $this->assertSame('', $this->repo->getProximasActividadesDeCentro(99800099, '2027-06-10'));
    }
}
