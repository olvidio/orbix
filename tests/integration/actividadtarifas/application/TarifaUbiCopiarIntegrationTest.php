<?php

declare(strict_types=1);

namespace Tests\integration\actividadtarifas\application;

use src\actividadtarifas\application\TarifaUbiCopiar;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\ubis\domain\contracts\TarifaUbiRepositoryInterface;
use src\ubis\domain\criteria\TarifaUbiField;
use src\ubis\domain\entity\TarifaUbi;
use Tests\myTest;

/**
 * Copiar tarifas de una casa del año anterior contra la BD de pruebas: borra las del año y copia las del
 * anterior (clave casa + tarifa + año + serie). Datos propios en una casa y unos años que no existen.
 */
final class TarifaUbiCopiarIntegrationTest extends myTest
{
    private const CASA = 999731;
    private const YEAR = 2091;

    private TarifaUbiRepositoryInterface $repo;

    public function setUp(): void
    {
        parent::setUp();
        $this->repo = $GLOBALS['container']->get(TarifaUbiRepositoryInterface::class);
        $this->limpiar();
    }

    public function tearDown(): void
    {
        $this->limpiar();
        parent::tearDown();
    }

    private function limpiar(): void
    {
        foreach ([self::YEAR - 1, self::YEAR] as $year) {
            foreach ($this->deAnyo($year) as $t) {
                $this->repo->Eliminar($t);
            }
        }
    }

    /** @return list<TarifaUbi> */
    private function deAnyo(int $year): array
    {
        return array_values($this->repo->matching(Criteria::create()
            ->where(Filter::eq(TarifaUbiField::IdUbi, self::CASA))
            ->andWhere(Filter::eq(TarifaUbiField::Year, $year))
            ->orderBy(TarifaUbiField::IdTarifa)));
    }

    private function crear(int $year, int $idTarifa, float $cantidad, ?string $observ = null): void
    {
        $t = new TarifaUbi();
        $t->setId_item($this->repo->getNewId());
        $t->setId_ubi(self::CASA);
        $t->setYear($year);
        $t->setId_tarifa($idTarifa);
        $t->setId_serie(1);
        $t->setCantidad($cantidad);
        $t->setObserv($observ);
        $this->assertTrue($this->repo->Guardar($t), $this->repo->getErrorTxt());
    }

    public function test_copia_las_del_anyo_anterior_sustituyendo_las_del_anyo(): void
    {
        $this->crear(self::YEAR - 1, 4, 30.5, 'con sábanas');
        $this->crear(self::YEAR - 1, 5, 12.0);
        $this->crear(self::YEAR, 4, 99.0);
        $this->crear(self::YEAR, 6, 7.0);

        $uc = $GLOBALS['container']->get(TarifaUbiCopiar::class);
        $this->assertSame('', $uc->execute(['id_ubi' => self::CASA, 'year' => self::YEAR]));

        $resumen = static fn (array $lista): array => array_map(
            static fn (TarifaUbi $t): array => [$t->getId_tarifa(), $t->getCantidad(), $t->getObserv()],
            $lista
        );
        $this->assertSame([[4, 30.5, 'con sábanas'], [5, 12.0, null]], $resumen($this->deAnyo(self::YEAR)));
        $this->assertCount(2, $this->deAnyo(self::YEAR - 1), 'las del año anterior siguen');
    }
}
