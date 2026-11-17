<?php

declare(strict_types=1);

namespace Tests\integration\casas\application;

use src\casas\application\CasaEcGastosGuardar;
use src\casas\domain\contracts\UbiGastoRepositoryInterface;
use src\casas\domain\criteria\UbiGastoField;
use src\casas\domain\entity\UbiGasto;
use src\casas\domain\value_objects\UbiGastoCantidad;
use src\casas\domain\value_objects\UbiGastoTipo;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\ubis\domain\entity\Casa;
use Tests\factories\ubis\CasaFactory;
use Tests\myTest;

/** Guardar los gastos de un año contra la BD: un apunte por mes y tipo, sin duplicar. */
final class CasaEcGastosGuardarIntegracionTest extends myTest
{
    private const ID_UBI = -10019002;
    private const YEAR = 2026;

    private UbiGastoRepositoryInterface $gastos;
    private CasaDlRepositoryInterface $casas;
    private Casa $casa;

    public function setUp(): void
    {
        parent::setUp();
        $this->gastos = $GLOBALS['container']->get(UbiGastoRepositoryInterface::class);
        $this->casas = $GLOBALS['container']->get(CasaDlRepositoryInterface::class);
        $this->casa = (new CasaFactory())->createSimple(self::ID_UBI);
        $this->casas->Guardar($this->casa);
    }

    public function tearDown(): void
    {
        foreach ($this->apuntesDelAnyo() as $g) {
            $this->gastos->Eliminar($g);
        }
        $this->casas->Eliminar($this->casa);
        parent::tearDown();
    }

    /** @return list<UbiGasto> */
    private function apuntesDelAnyo(): array
    {
        return $this->gastos->matching(
            Criteria::create()
                ->where(Filter::eq(UbiGastoField::IdUbi, self::ID_UBI))
                ->andWhere(Filter::between(UbiGastoField::FGasto, self::YEAR . '-01-01', self::YEAR . '-12-31'))
        );
    }

    private function sumaGastos(): float
    {
        return $this->gastos->getSumaGastos(
            self::ID_UBI,
            UbiGastoTipo::GASTO,
            new DateTimeLocal(self::YEAR . '/1/1'),
            new DateTimeLocal(self::YEAR . '/12/31'),
        );
    }

    public function test_guardar_dos_veces_deja_un_apunte_por_mes_y_tipo_con_el_ultimo_valor(): void
    {
        $uc = new CasaEcGastosGuardar($this->gastos);
        $base = ['id_ubi' => (string) self::ID_UBI, 'year' => (string) self::YEAR];

        $this->assertTrue($uc->execute($base + ['g1' => '100', 'g2' => '50'])['ok']);
        $this->assertTrue($uc->execute($base + ['g1' => '120', 'g2' => '50'])['ok']);

        $this->assertCount(36, $this->apuntesDelAnyo());
        $this->assertSame(170.0, $this->sumaGastos());
    }

    public function test_los_apuntes_repetidos_que_ya_habia_se_borran_al_guardar(): void
    {
        foreach ([1, 2, 3] as $i) {
            $g = new UbiGasto();
            $g->setId_item($this->gastos->getNewId());
            $g->setId_ubi(self::ID_UBI);
            $g->setF_gasto(new DateTimeLocal(self::YEAR . '/3/5'));
            $g->setTipoVo(new UbiGastoTipo(UbiGastoTipo::GASTO));
            $g->setCantidadVo(new UbiGastoCantidad(10.0));
            $this->gastos->Guardar($g);
        }
        $this->assertSame(30.0, $this->sumaGastos());

        $r = (new CasaEcGastosGuardar($this->gastos))->execute(['id_ubi' => (string) self::ID_UBI, 'year' => (string) self::YEAR, 'g3' => '10']);

        $this->assertTrue($r['ok']);
        $this->assertCount(36, $this->apuntesDelAnyo());
        $this->assertSame(10.0, $this->sumaGastos());
    }
}
