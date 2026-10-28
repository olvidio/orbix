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
 * Comparan con el método legacy sobre los datos existentes (incluida la hidratación de fechas).
 */
class PgProfesoresMatchingTest extends myTest
{
    public function test_profesor_stgr_con_cese_nulo_equivale_a_legacy(): void
    {
        $repo = $GLOBALS['container']->get(ProfesorStgrRepositoryInterface::class);
        $this->assertEquals(
            $repo->getProfesoresStgr(['f_cese' => '', '_ordre' => 'f_nombramiento DESC', '_limit' => 30], ['f_cese' => 'IS NULL']),
            $repo->matching(
                Criteria::create()
                    ->where(Filter::isNull(ProfesorStgrField::FCese))
                    ->orderBy(ProfesorStgrField::FNombramiento, OrderDirection::Desc)
                    ->limit(30)
            )
        );
    }

    public function test_directores_ampliaciones_y_congresos_equivalen_a_legacy(): void
    {
        $directores = $GLOBALS['container']->get(ProfesorDirectorRepositoryInterface::class);
        $this->assertEquals(
            $directores->getProfesoresDirectores(['_ordre' => 'id_item', '_limit' => 30]),
            $directores->matching(Criteria::create()->orderBy(ProfesorDirectorField::IdItem)->limit(30))
        );

        $ampliaciones = $GLOBALS['container']->get(ProfesorAmpliacionRepositoryInterface::class);
        $this->assertEquals(
            $ampliaciones->getProfesorAmpliaciones(['_ordre' => 'id_item', '_limit' => 30]),
            $ampliaciones->matching(Criteria::create()->orderBy(ProfesorAmpliacionField::IdItem)->limit(30))
        );

        $congresos = $GLOBALS['container']->get(ProfesorCongresoRepositoryInterface::class);
        $this->assertEquals(
            $congresos->getProfesorCongresos(['_ordre' => 'f_ini DESC', '_limit' => 30]),
            $congresos->matching(Criteria::create()->orderBy(ProfesorCongresoField::FIni, OrderDirection::Desc)->limit(30))
        );
    }

    public function test_juramentos_publicaciones_y_titulos_equivalen_a_legacy(): void
    {
        $juramentos = $GLOBALS['container']->get(ProfesorJuramentoRepositoryInterface::class);
        $this->assertEquals(
            $juramentos->getProfesorJuramentos(['_ordre' => 'id_item', '_limit' => 30]),
            $juramentos->matching(Criteria::create()->orderBy(ProfesorJuramentoField::IdItem)->limit(30))
        );

        $publicaciones = $GLOBALS['container']->get(ProfesorPublicacionRepositoryInterface::class);
        $this->assertEquals(
            $publicaciones->getProfesorPublicaciones(['_ordre' => 'id_item', '_limit' => 30]),
            $publicaciones->matching(Criteria::create()->orderBy(ProfesorPublicacionField::IdItem)->limit(30))
        );

        $titulos = $GLOBALS['container']->get(ProfesorTituloEstRepositoryInterface::class);
        $this->assertEquals(
            $titulos->getProfesorTitulosEst(['_ordre' => 'id_item', '_limit' => 30]),
            $titulos->matching(Criteria::create()->orderBy(ProfesorTituloEstField::IdItem)->limit(30))
        );
    }

    public function test_docencia_latin_y_tipos_equivalen_a_legacy(): void
    {
        $docencia = $GLOBALS['container']->get(ProfesorDocenciaStgrRepositoryInterface::class);
        $this->assertEquals(
            $docencia->getProfesorDocenciasStgr(['_ordre' => 'curso_inicio,id_asignatura', '_limit' => 30]),
            $docencia->matching(
                Criteria::create()
                    ->orderBy(ProfesorDocenciaStgrField::CursoInicio)
                    ->addOrderBy(ProfesorDocenciaStgrField::IdAsignatura)
                    ->limit(30)
            )
        );

        $latin = $GLOBALS['container']->get(ProfesorLatinRepositoryInterface::class);
        $this->assertEquals(
            $latin->getProfesoresLatin(['_ordre' => 'id_nom', '_limit' => 30]),
            $latin->matching(Criteria::create()->orderBy(ProfesorLatinField::IdNom)->limit(30))
        );

        $tipos = $GLOBALS['container']->get(ProfesorTipoRepositoryInterface::class);
        $this->assertEquals(
            $tipos->getProfesorTipos(['_ordre' => 'tipo_profesor']),
            $tipos->matching(Criteria::create()->orderBy(ProfesorTipoField::TipoProfesor))
        );
    }
}
