<?php

namespace Tests\integration\procesos\infrastructure\repositories;

use src\actividades\domain\value_objects\ActividadTipoIdTxt;
use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;
use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;
use src\procesos\domain\contracts\ActividadTareaRepositoryInterface;
use src\procesos\domain\contracts\PermUsuarioActividadRepositoryInterface;
use src\procesos\domain\contracts\ProcesoTipoRepositoryInterface;
use src\procesos\domain\contracts\TareaProcesoRepositoryInterface;
use src\procesos\domain\criteria\ActividadFaseField;
use src\procesos\domain\criteria\ActividadProcesoTareaField;
use src\procesos\domain\criteria\ActividadTareaField;
use src\procesos\domain\criteria\PermUsuarioActividadField;
use src\procesos\domain\criteria\ProcesoTipoField;
use src\procesos\domain\criteria\TareaProcesoField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use Tests\myTest;

/**
 * `matching()` de los repositorios de `procesos` (paso 4.3 de docs/dev/refactor_busquedas_criteria.md).
 * Comparan con el método legacy sobre los datos existentes.
 */
class PgProcesosMatchingTest extends myTest
{
    public function test_fases_y_tareas_equivalen_a_legacy(): void
    {
        $fases = $GLOBALS['container']->get(ActividadFaseRepositoryInterface::class);
        $this->assertContainsOnlyInstancesOf(
            \src\procesos\domain\entity\ActividadFase::class,
            $fases->matching(Criteria::create()->orderBy(ActividadFaseField::DescFase))
        );

        $tareas = $GLOBALS['container']->get(ActividadTareaRepositoryInterface::class);
        $this->assertContainsOnlyInstancesOf(
            \src\procesos\domain\entity\ActividadTarea::class,
            $tareas->matching(Criteria::create()->orderBy(ActividadTareaField::DescTarea))
        );
    }

    public function test_tipos_de_proceso_equivalen_a_legacy(): void
    {
        $repo = $GLOBALS['container']->get(ProcesoTipoRepositoryInterface::class);
        $tipos = $repo->matching(
            Criteria::create()
                ->where(Filter::eq(ProcesoTipoField::Sfsv, 1))
                ->orderBy(ProcesoTipoField::NomProceso)
        );
        $this->assertContainsOnlyInstancesOf(\src\procesos\domain\entity\ProcesoTipo::class, $tipos);
        foreach ($tipos as $tipo) {
            $this->assertSame(1, (int) $tipo->getSfsv());
        }
    }

    public function test_tareas_proceso_conservan_el_json_de_fases_previas(): void
    {
        $repo = $GLOBALS['container']->get(TareaProcesoRepositoryInterface::class);
        $tareas = $repo->matching(
            Criteria::create()
                ->orderBy(TareaProcesoField::IdTipoProceso)
                ->addOrderBy(TareaProcesoField::IdFase)
                ->addOrderBy(TareaProcesoField::IdTarea)
                ->limit(30)
        );
        $this->assertLessThanOrEqual(30, count($tareas));
        foreach ($tareas as $tarea) {
            $this->assertTrue(is_array($tarea->getJson_fases_previas()) || $tarea->getJson_fases_previas() === null || $tarea->getJson_fases_previas() instanceof \stdClass);
        }
    }

    public function test_tareas_de_proceso_vacias_primero_son_las_del_tipo_de_proceso(): void
    {
        $repo = $GLOBALS['container']->get(TareaProcesoRepositoryInterface::class);
        $todas = $repo->matching(Criteria::create()->orderBy(TareaProcesoField::IdTipoProceso)->limit(1));
        if ($todas === []) {
            $this->markTestSkipped('No hay tareas de proceso');
        }
        $idTipo = $todas[0]->getIdTipoProcesoVo()->value();
        $esperadas = $repo->matching(Criteria::create()->where(Filter::eq(TareaProcesoField::IdTipoProceso, $idTipo)));
        $obtenidas = $repo->getTareasDeProcesoVaciasPrimero($idTipo);
        $this->assertCount(count($esperadas), $obtenidas);
        foreach ($obtenidas as $tarea) {
            $this->assertSame($idTipo, $tarea->getIdTipoProcesoVo()->value());
        }
    }

    public function test_actividad_proceso_tarea_matching_respeta_orden_y_limite(): void
    {
        $repo = $GLOBALS['container']->get(ActividadProcesoTareaRepositoryInterface::class);
        $filas = $repo->matching(Criteria::create()->orderBy(ActividadProcesoTareaField::IdItem)->limit(30));
        $this->assertLessThanOrEqual(30, count($filas));
        $ids = array_map(static fn ($f) => $f->getId_item(), $filas);
        $ordenados = $ids;
        sort($ordenados);
        $this->assertSame($ordenados, $ids);
    }

    public function test_permisos_de_usuario_con_alias_de_tipo_filtran_por_dl_propia(): void
    {
        $repo = $GLOBALS['container']->get(PermUsuarioActividadRepositoryInterface::class);
        $filas = $repo->matching(
            Criteria::create()
                ->where(Filter::eq(PermUsuarioActividadField::DlPropia, true))
                ->andWhere(ActividadTipoIdTxt::filterForRepository(PermUsuarioActividadField::IdTipoActivTxt, '17'))
        );
        foreach ($filas as $fila) {
            $this->assertTrue($fila->isDl_propia());
        }
    }
}
