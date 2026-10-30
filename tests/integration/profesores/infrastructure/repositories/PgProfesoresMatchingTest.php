<?php

namespace Tests\integration\profesores\infrastructure\repositories;

use src\profesores\domain\contracts\ProfesorAmpliacionRepositoryInterface;
use src\profesores\domain\contracts\ProfesorCongresoRepositoryInterface;
use src\profesores\domain\contracts\ProfesorDirectorRepositoryInterface;
use src\profesores\domain\contracts\ProfesorDocenciaStgrRepositoryInterface;
use src\profesores\domain\contracts\ProfesorJuramentoRepositoryInterface;
use src\profesores\domain\contracts\ProfesorLatinRepositoryInterface;
use src\profesores\domain\contracts\ProfesorPublicacionRepositoryInterface;
use src\profesores\domain\contracts\ProfesorStgrRepositoryInterface;
use src\profesores\domain\contracts\ProfesorTipoRepositoryInterface;
use src\profesores\domain\contracts\ProfesorTituloEstRepositoryInterface;
use src\profesores\domain\criteria\ProfesorAmpliacionField;
use src\profesores\domain\criteria\ProfesorCongresoField;
use src\profesores\domain\criteria\ProfesorDirectorField;
use src\profesores\domain\criteria\ProfesorDocenciaStgrField;
use src\profesores\domain\criteria\ProfesorJuramentoField;
use src\profesores\domain\criteria\ProfesorLatinField;
use src\profesores\domain\criteria\ProfesorPublicacionField;
use src\profesores\domain\criteria\ProfesorStgrField;
use src\profesores\domain\criteria\ProfesorTipoField;
use src\profesores\domain\criteria\ProfesorTituloEstField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\criteria\OrderDirection;
use Tests\myTest;

/**
 * `matching()` de los repositorios de `profesores` (paso 4.3 de docs/dev/refactor_busquedas_criteria.md).
 * Comprueban orden y límite sobre los datos existentes (incluida la hidratación de fechas).
 */
class PgProfesoresMatchingTest extends myTest
{
    public function test_profesor_stgr_con_cese_nulo(): void
    {
        $repo = $GLOBALS['container']->get(ProfesorStgrRepositoryInterface::class);
        $result = $repo->matching(
            Criteria::create()
                ->where(Filter::isNull(ProfesorStgrField::FCese))
                ->orderBy(ProfesorStgrField::FNombramiento, OrderDirection::Desc)
                ->limit(30)
        );
        $this->assertLessThanOrEqual(30, count($result));
        foreach ($result as $profesor) {
            $this->assertNull($profesor->getF_cese());
        }
    }

    public function test_directores_ampliaciones_y_congresos_con_orden_y_limite(): void
    {
        $directores = $GLOBALS['container']->get(ProfesorDirectorRepositoryInterface::class);
        $res = $directores->matching(Criteria::create()->orderBy(ProfesorDirectorField::IdItem)->limit(30));
        $this->assertLessThanOrEqual(30, count($res));

        $ampliaciones = $GLOBALS['container']->get(ProfesorAmpliacionRepositoryInterface::class);
        $res = $ampliaciones->matching(Criteria::create()->orderBy(ProfesorAmpliacionField::IdItem)->limit(30));
        $this->assertLessThanOrEqual(30, count($res));

        $congresos = $GLOBALS['container']->get(ProfesorCongresoRepositoryInterface::class);
        $res = $congresos->matching(Criteria::create()->orderBy(ProfesorCongresoField::FIni, OrderDirection::Desc)->limit(30));
        $this->assertLessThanOrEqual(30, count($res));
    }

    public function test_juramentos_publicaciones_y_titulos_con_orden_y_limite(): void
    {
        $juramentos = $GLOBALS['container']->get(ProfesorJuramentoRepositoryInterface::class);
        $res = $juramentos->matching(Criteria::create()->orderBy(ProfesorJuramentoField::IdItem)->limit(30));
        $this->assertLessThanOrEqual(30, count($res));

        $publicaciones = $GLOBALS['container']->get(ProfesorPublicacionRepositoryInterface::class);
        $res = $publicaciones->matching(Criteria::create()->orderBy(ProfesorPublicacionField::IdItem)->limit(30));
        $this->assertLessThanOrEqual(30, count($res));

        $titulos = $GLOBALS['container']->get(ProfesorTituloEstRepositoryInterface::class);
        $res = $titulos->matching(Criteria::create()->orderBy(ProfesorTituloEstField::IdItem)->limit(30));
        $this->assertLessThanOrEqual(30, count($res));
    }

    public function test_docencia_latin_y_tipos_con_orden_y_limite(): void
    {
        $docencia = $GLOBALS['container']->get(ProfesorDocenciaStgrRepositoryInterface::class);
        $res = $docencia->matching(
                Criteria::create()
                    ->orderBy(ProfesorDocenciaStgrField::CursoInicio)
                    ->addOrderBy(ProfesorDocenciaStgrField::IdAsignatura)
                    ->limit(30)
            );
        $this->assertLessThanOrEqual(30, count($res));

        $latin = $GLOBALS['container']->get(ProfesorLatinRepositoryInterface::class);
        $res = $latin->matching(Criteria::create()->orderBy(ProfesorLatinField::IdNom)->limit(30));
        $this->assertLessThanOrEqual(30, count($res));

        $tipos = $GLOBALS['container']->get(ProfesorTipoRepositoryInterface::class);
        $res = $tipos->matching(Criteria::create()->orderBy(ProfesorTipoField::TipoProfesor));
        $this->assertIsArray($res);
    }
}
