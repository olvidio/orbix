<?php

namespace Tests\integration\misas\infrastructure\persistence\postgresql;

use src\misas\domain\contracts\EncargoCtrRepositoryInterface;
use src\misas\domain\contracts\EncargoDiaRepositoryInterface;
use src\misas\domain\contracts\InicialesSacdRepositoryInterface;
use src\misas\domain\criteria\EncargoCtrField;
use src\misas\domain\criteria\EncargoDiaField;
use src\misas\domain\criteria\InicialesSacdField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\value_objects\DateTimeLocal;
use Tests\factories\misas\EncargoDiaFactory;
use Tests\myTest;

/**
 * `matching()` de los repositorios de `misas` (paso 4.5 de docs/dev/refactor_busquedas_criteria.md).
 */
class PgMisasMatchingTest extends myTest
{
    public function test_encargo_dia_between_equivale_a_legacy(): void
    {
        $repo = $GLOBALS['container']->get(EncargoDiaRepositoryInterface::class);
        $oEncargoDia = (new EncargoDiaFactory())->createSimple();
        $oEncargoDia->setTstart(new DateTimeLocal('2031-03-10 12:00:00'));
        $oEncargoDia->setTend(new DateTimeLocal('2031-03-10 13:00:00'));
        $this->assertTrue($repo->Guardar($oEncargoDia));
        try {
            $idEnc = $oEncargoDia->getId_enc();
            $dia = $oEncargoDia->getTstart()->format('Y-m-d');
            $desde = $dia . ' 00:00:00';
            $hasta = $dia . ' 23:59:59';

            $nuevo = $repo->matching(
                Criteria::create()
                    ->where(Filter::eq(EncargoDiaField::IdEnc, $idEnc))
                    ->andWhere(Filter::between(EncargoDiaField::Tstart, $desde, $hasta))
                    ->orderBy(EncargoDiaField::Tstart)
            );
            $legacy = $repo->getEncargoDias(
                ['id_enc' => $idEnc, 'tstart' => "'$desde', '$hasta'", '_ordre' => 'tstart'],
                ['tstart' => 'BETWEEN']
            );

            $this->assertNotEmpty($nuevo);
            $this->assertEquals($legacy, $nuevo);
        } finally {
            $repo->Eliminar($oEncargoDia);
        }
    }

    public function test_iniciales_sacd_matching_equivale_a_legacy(): void
    {
        $repo = $GLOBALS['container']->get(InicialesSacdRepositoryInterface::class);
        $this->assertEquals(
            $repo->getInicialesSacd(['_ordre' => 'id_nom', '_limit' => 5]),
            $repo->matching(Criteria::create()->orderBy(InicialesSacdField::IdNom)->limit(5))
        );
    }

    public function test_encargo_ctr_matching_equivale_a_legacy(): void
    {
        $repo = $GLOBALS['container']->get(EncargoCtrRepositoryInterface::class);
        $this->assertEquals(
            $repo->getEncargosCentros(['_ordre' => 'id_enc', '_limit' => 5]),
            $repo->matching(Criteria::create()->orderBy(EncargoCtrField::IdEnc)->limit(5))
        );
    }
}
