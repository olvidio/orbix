<?php

namespace Tests\integration\ubiscamas\infrastructure\repositories;

use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\ubiscamas\domain\contracts\CamaRepositoryInterface;
use src\ubiscamas\domain\contracts\HabitacionRepositoryInterface;
use src\ubiscamas\domain\criteria\CamaField;
use src\ubiscamas\domain\criteria\HabitacionField;
use Tests\factories\ubiscamas\CamaFactory;
use Tests\factories\ubiscamas\HabitacionFactory;
use Tests\myTest;

/**
 * `matching()` de PgHabitacionRepository y PgCamaRepository (paso 4.1 de docs/dev/refactor_busquedas_criteria.md).
 */
class PgUbiscamasMatchingTest extends myTest
{
    private HabitacionRepositoryInterface $habitacionRepo;
    private CamaRepositoryInterface $camaRepo;

    public function setUp(): void
    {
        parent::setUp();
        $this->habitacionRepo = $GLOBALS['container']->get(HabitacionRepositoryInterface::class);
        $this->camaRepo = $GLOBALS['container']->get(CamaRepositoryInterface::class);
    }

    public function test_habitacion_matching_por_id_equivale_a_legacy(): void
    {
        $oHabitacion = (new HabitacionFactory())->createSimple();
        $idHabitacion = $oHabitacion->getId_habitacion();
        $this->habitacionRepo->Guardar($oHabitacion);

        $criteria = Criteria::create()->where(Filter::eq(HabitacionField::IdHabitacion, $idHabitacion));
        $nuevo = $this->habitacionRepo->matching($criteria);

        $this->assertCount(1, $nuevo);
        $this->assertSame($idHabitacion, $nuevo[0]->getId_habitacion());

        $this->habitacionRepo->Eliminar($nuevo[0]);
        $this->assertSame([], $this->habitacionRepo->matching($criteria));
    }

    public function test_habitacion_matching_por_ubi_con_orden_y_limite(): void
    {
        $oHabitacion = (new HabitacionFactory())->createSimple();
        $this->habitacionRepo->Guardar($oHabitacion);

        $res = $this->habitacionRepo->matching(
            Criteria::create()
                ->where(Filter::eq(HabitacionField::IdUbi, $oHabitacion->getIdUbiVo()))
                ->orderBy(HabitacionField::Orden)
                ->limit(1)
        );

        $this->assertCount(1, $res);

        $this->habitacionRepo->Eliminar($oHabitacion);
    }

    public function test_cama_matching_in_por_habitaciones(): void
    {
        $oHabitacion = (new HabitacionFactory())->createSimple();
        $idHabitacion = $oHabitacion->getId_habitacion();
        $this->habitacionRepo->Guardar($oHabitacion);

        $oCama = (new CamaFactory())->createSimple(null, $idHabitacion);
        $this->camaRepo->Guardar($oCama);

        $criteria = Criteria::create()->where(Filter::in(CamaField::IdHabitacion, [$idHabitacion, $idHabitacion]));
        $nuevo = $this->camaRepo->matching($criteria);

        $this->assertCount(1, $nuevo);
        $this->assertSame($oCama->getIdCamaVo()->value(), $nuevo[0]->getIdCamaVo()->value());

        foreach ($nuevo as $cama) {
            $this->camaRepo->Eliminar($cama);
        }
        $this->habitacionRepo->Eliminar($oHabitacion);
    }
}
