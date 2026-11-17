<?php

declare(strict_types=1);

namespace Tests\unit\actividadtarifas\application;

use src\actividadtarifas\application\TarifaUbiCopiar;
use src\shared\domain\criteria\Criteria;
use src\ubis\domain\contracts\TarifaUbiRepositoryInterface;
use src\ubis\domain\entity\TarifaUbi;
use Tests\myTest;
use Tests\support\DescribeCriteria;

/**
 * Copiar las tarifas de una casa del año anterior al año elegido: «copiar las del año 2026 a este (2027).
 * Esto borrará las tarifas actuales de 2027». Duda 33 del plan de tests.
 */
final class TarifaUbiCopiarTest extends myTest
{
    private const CASA = 1003;

    /** @var array<int, list<TarifaUbi>> tarifas de la casa por año */
    private array $porAnyo = [];
    /** @var list<int> */
    private array $eliminadas = [];
    /** @var list<TarifaUbi> */
    private array $guardadas = [];
    private bool $falloAlEliminar = false;
    private int $siguienteId = 500;

    private function tarifa(int $idItem, int $year, int $idTarifa, int $idSerie, float $cantidad, ?string $observ = null): TarifaUbi
    {
        $t = new TarifaUbi();
        $t->setId_item($idItem);
        $t->setId_ubi(self::CASA);
        $t->setYear($year);
        $t->setId_tarifa($idTarifa);
        $t->setId_serie($idSerie);
        $t->setCantidad($cantidad);
        $t->setObserv($observ);

        return $t;
    }

    private function uc(): TarifaUbiCopiar
    {
        $repo = $this->createMock(TarifaUbiRepositoryInterface::class);
        $repo->method('matching')->willReturnCallback(function (Criteria $c): array {
            $filtros = DescribeCriteria::filtros($c);
            $this->assertContains('id_ubi eq ' . self::CASA, $filtros);
            foreach ($this->porAnyo as $year => $lista) {
                if (in_array("year eq $year", $filtros, true)) {
                    return $lista;
                }
            }

            return [];
        });
        $repo->method('Eliminar')->willReturnCallback(function (TarifaUbi $t): bool {
            if ($this->falloAlEliminar) {
                return false;
            }
            $this->eliminadas[] = $t->getId_item();

            return true;
        });
        $repo->method('Guardar')->willReturnCallback(function (TarifaUbi $t): bool {
            $this->guardadas[] = $t;

            return true;
        });
        $repo->method('getNewId')->willReturnCallback(fn (): int => $this->siguienteId++);
        $repo->method('getErrorTxt')->willReturn('error de bd');

        return new TarifaUbiCopiar($repo);
    }

    public function test_exige_casa_y_anyo(): void
    {
        $this->assertNotSame('', $this->uc()->execute([]));
        $this->assertNotSame('', $this->uc()->execute(['id_ubi' => (string) self::CASA]));
        $this->assertSame([], $this->guardadas);
    }

    public function test_copia_las_del_anyo_anterior_y_borra_las_que_habia_en_el_anyo(): void
    {
        $this->porAnyo[2026] = [
            $this->tarifa(11, 2026, 4, 1, 30.5, 'con sábanas'),
            $this->tarifa(12, 2026, 5, 2, 12.0),
        ];
        $this->porAnyo[2027] = [$this->tarifa(21, 2027, 4, 1, 99.0)];

        $this->assertSame('', $this->uc()->execute(['id_ubi' => (string) self::CASA, 'year' => '2027']));

        $this->assertSame([21], $this->eliminadas);
        $copia = array_map(static fn (TarifaUbi $t): array => [
            $t->getId_item(), $t->getId_ubi(), $t->getYear(), $t->getId_tarifa(), $t->getId_serie(), $t->getCantidad(), $t->getObserv(),
        ], $this->guardadas);
        $this->assertSame([
            [500, self::CASA, 2027, 4, 1, 30.5, 'con sábanas'],
            [501, self::CASA, 2027, 5, 2, 12.0, null],
        ], $copia);
        $this->assertSame(2026, $this->porAnyo[2026][0]->getYear(), 'las del año anterior no se tocan');
    }

    public function test_sin_tarifas_el_anyo_anterior_avisa_y_no_borra_las_actuales(): void
    {
        $this->porAnyo[2027] = [$this->tarifa(21, 2027, 4, 1, 99.0)];

        $msg = $this->uc()->execute(['id_ubi' => (string) self::CASA, 'year' => '2027']);

        $this->assertStringContainsString('2026', $msg);
        $this->assertSame([], $this->eliminadas);
        $this->assertSame([], $this->guardadas);
    }

    public function test_si_no_se_puede_borrar_una_tarifa_actual_no_se_copia_nada(): void
    {
        $this->porAnyo[2026] = [$this->tarifa(11, 2026, 4, 1, 30.5)];
        $this->porAnyo[2027] = [$this->tarifa(21, 2027, 4, 1, 99.0)];
        $this->falloAlEliminar = true;

        $msg = $this->uc()->execute(['id_ubi' => (string) self::CASA, 'year' => '2027']);

        $this->assertStringContainsString('error de bd', $msg);
        $this->assertSame([], $this->guardadas);
    }
}
