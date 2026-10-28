<?php

namespace Tests\integration\personas\infrastructure\repositories;

use src\personas\domain\contracts\SituacionRepositoryInterface;
use src\personas\domain\contracts\TelecoPersonaDlRepositoryInterface;
use src\personas\domain\contracts\TrasladoRepositoryInterface;
use src\personas\domain\contracts\UltimaAsistenciaRepositoryInterface;
use src\personas\domain\criteria\SituacionField;
use src\personas\domain\criteria\TelecoPersonaField;
use src\personas\domain\criteria\TrasladoField;
use src\personas\domain\criteria\UltimaAsistenciaField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use Tests\myTest;

/**
 * `matching()` de situaciones, traslados, telecos y última asistencia
 * (paso 4.3 de docs/dev/refactor_busquedas_criteria.md). Comparan con el método legacy.
 */
class PgPersonasCatalogosMatchingTest extends myTest
{
    // Solo la situación 'A': otras filas existentes tienen caracteres que el VO SituacionName rechaza (falla igual con el método legacy).
    public function test_situaciones_equivalen_a_legacy(): void
    {
        $repo = $GLOBALS['container']->get(SituacionRepositoryInterface::class);
        $this->assertEquals(
            $repo->getSituaciones(['situacion' => 'A', '_ordre' => 'situacion']),
            $repo->matching(Criteria::create()->where(Filter::eq(SituacionField::Situacion, 'A'))->orderBy(SituacionField::Situacion))
        );
    }

    public function test_traslados_equivalen_a_legacy(): void
    {
        $repo = $GLOBALS['container']->get(TrasladoRepositoryInterface::class);
        $this->assertEquals(
            $repo->getTraslados(['_ordre' => 'id_item', '_limit' => 30]),
            $repo->matching(Criteria::create()->orderBy(TrasladoField::IdItem)->limit(30))
        );
    }

    public function test_telecos_equivalen_a_legacy(): void
    {
        $repo = $GLOBALS['container']->get(TelecoPersonaDlRepositoryInterface::class);
        $this->assertEquals(
            $repo->getTelecosPersona(['_ordre' => 'id_item', '_limit' => 30]),
            $repo->matching(Criteria::create()->orderBy(TelecoPersonaField::IdItem)->limit(30))
        );
    }

    public function test_ultima_asistencia_equivale_a_legacy(): void
    {
        $repo = $GLOBALS['container']->get(UltimaAsistenciaRepositoryInterface::class);
        $this->assertEquals(
            $repo->getUltimasAsistencias(['_ordre' => 'id_item', '_limit' => 30]),
            $repo->matching(Criteria::create()->orderBy(UltimaAsistenciaField::IdItem)->limit(30))
        );
    }
}
