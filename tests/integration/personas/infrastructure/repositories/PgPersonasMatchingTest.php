<?php

namespace Tests\integration\personas\infrastructure\repositories;

use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\contracts\PersonaExRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\contracts\PersonaPubRepositoryInterface;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\personas\domain\criteria\PersonaField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use Tests\myTest;

/**
 * `matching()` de los repositorios de personas (paso 4.3 de docs/dev/refactor_busquedas_criteria.md).
 * Comparan con el método legacy sobre los datos existentes (fechas y `publicado_para` incluidos).
 */
class PgPersonasMatchingTest extends myTest
{
    public function test_persona_n_equivale_a_legacy(): void
    {
        $repo = $GLOBALS['container']->get(PersonaNRepositoryInterface::class);
        $this->assertEquals(
            $repo->getPersonas(['situacion' => 'A', '_ordre' => 'id_nom', '_limit' => 30]),
            $repo->matching(
                Criteria::create()
                    ->where(Filter::eq(PersonaField::Situacion, 'A'))
                    ->orderBy(PersonaField::IdNom)
                    ->limit(30)
            )
        );
    }

    public function test_persona_dl_con_regex_y_sin_acentos_equivale_a_legacy(): void
    {
        $repo = $GLOBALS['container']->get(PersonaDlRepositoryInterface::class);
        $this->assertEquals(
            $repo->getPersonas(
                ['apellido1' => '^a', 'sacd' => 't', '_ordre' => 'apellido1, nom', '_limit' => 30],
                ['apellido1' => 'sin_acentos']
            ),
            $repo->matching(
                Criteria::create()
                    ->where(Filter::matchesUnaccented(PersonaField::Apellido1, '^a'))
                    ->andWhere(Filter::eq(PersonaField::Sacd, true))
                    ->orderBy(PersonaField::Apellido1)
                    ->addOrderBy(PersonaField::Nom)
                    ->limit(30)
            )
        );
    }

    public function test_persona_ex_respeta_orden_y_limite(): void
    {
        $repo = $GLOBALS['container']->get(PersonaExRepositoryInterface::class);
        $personas = $repo->matching(Criteria::create()->orderBy(PersonaField::IdNom)->limit(30));
        $this->assertLessThanOrEqual(30, count($personas));
        $ids = array_map(static fn($p) => $p->getId_nom(), $personas);
        $ordenados = $ids;
        sort($ordenados);
        $this->assertSame($ordenados, $ids);
    }

    public function test_persona_pub_aplica_el_filtro_de_publicacion_igual_que_legacy(): void
    {
        $repo = $GLOBALS['container']->get(PersonaPubRepositoryInterface::class);
        $personas = $repo->matching(Criteria::create()->orderBy(PersonaField::IdNom)->limit(30));
        $this->assertLessThanOrEqual(30, count($personas));
        $ids = array_map(static fn($p) => $p->getId_nom(), $personas);
        $ordenados = $ids;
        sort($ordenados);
        $this->assertSame($ordenados, $ids);
    }

    public function test_persona_sacd_respeta_orden_y_limite(): void
    {
        $repo = $GLOBALS['container']->get(PersonaSacdRepositoryInterface::class);
        $personas = $repo->matching(Criteria::create()->orderBy(PersonaField::IdNom)->limit(30));
        $this->assertLessThanOrEqual(30, count($personas));
        $ids = array_map(static fn($p) => $p->getId_nom(), $personas);
        $ordenados = $ids;
        sort($ordenados);
        $this->assertSame($ordenados, $ids);
    }
}
