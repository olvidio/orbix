<?php

namespace Tests\integration\asignaturas\infrastructure\repositories;

use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\contracts\AsignaturaTipoRepositoryInterface;
use src\asignaturas\domain\contracts\DepartamentoRepositoryInterface;
use src\asignaturas\domain\contracts\SectorRepositoryInterface;
use src\asignaturas\domain\criteria\AsignaturaField;
use src\asignaturas\domain\criteria\AsignaturaTipoField;
use src\asignaturas\domain\criteria\DepartamentoField;
use src\asignaturas\domain\criteria\SectorField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use Tests\myTest;

/**
 * `matching()` de los repositorios de asignaturas (paso 4.1 de docs/dev/refactor_busquedas_criteria.md).
 * Son catálogos de solo lectura: cada test compara `matching()` con el método legacy sobre los mismos datos.
 */
class PgAsignaturasMatchingTest extends myTest
{
    public function test_asignatura_matching_menores_de_3000_ordenadas_equivale_a_legacy(): void
    {
        $repo = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class);

        $nuevo = $repo->matching(
            Criteria::create()
                ->where(Filter::lt(AsignaturaField::IdAsignatura, 3000))
                ->orderBy(AsignaturaField::IdNivel)
        );
        $legacy = $repo->getAsignaturas(['id_asignatura' => 3000, '_ordre' => 'id_nivel'], ['id_asignatura' => '<']);

        $this->assertNotEmpty($nuevo);
        $this->assertEquals($legacy, $nuevo);
    }

    public function test_asignatura_matching_plan_estudios_array_equivale_a_legacy(): void
    {
        $repo = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class);

        $nuevo = $repo->matching(
            Criteria::create()
                ->where(Filter::arrayContains(AsignaturaField::PlanEstudios, 2))
                ->orderBy(AsignaturaField::IdNivel)
        );
        $legacy = $repo->getAsignaturas(['plan_estudios' => 2, '_ordre' => 'id_nivel'], ['plan_estudios' => 'IN_ARRAY']);

        $this->assertEquals($legacy, $nuevo);
    }

    public function test_asignatura_tipo_matching_ordenado_equivale_a_legacy(): void
    {
        $repo = $GLOBALS['container']->get(AsignaturaTipoRepositoryInterface::class);

        $nuevo = $repo->matching(Criteria::create()->orderBy(AsignaturaTipoField::TipoAsignatura));
        $legacy = $repo->getAsignaturaTipos(['_ordre' => 'tipo_asignatura']);

        $this->assertEquals($legacy, $nuevo);
    }

    public function test_departamento_matching_usa_la_columna_departamento(): void
    {
        $repo = $GLOBALS['container']->get(DepartamentoRepositoryInterface::class);

        $nuevo = $repo->matching(Criteria::create()->orderBy(DepartamentoField::NombreDepartamento));
        $legacy = $repo->getDepartamentos(['_ordre' => 'departamento']);

        $this->assertEquals($legacy, $nuevo);

        if ($nuevo !== []) {
            $nombre = $nuevo[0]->getDepartamento();
            $porNombre = $repo->matching(
                Criteria::create()->where(Filter::matchesUnaccented(DepartamentoField::NombreDepartamento, $nombre))
            );
            $this->assertEquals($repo->getDepartamentos(['departamento' => $nombre], ['departamento' => 'sin_acentos']), $porNombre);
            $this->assertNotEmpty($porNombre);
        }
    }

    public function test_sector_matching_usa_la_columna_sector(): void
    {
        $repo = $GLOBALS['container']->get(SectorRepositoryInterface::class);

        $nuevo = $repo->matching(Criteria::create()->orderBy(SectorField::NombreSector));
        $legacy = $repo->getSectores(['_ordre' => 'sector']);

        $this->assertEquals($legacy, $nuevo);
    }
}
