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
        $this->assertEquals(
            $fases->getActividadFases(['_ordre' => 'desc_fase']),
            $fases->matching(Criteria::create()->orderBy(ActividadFaseField::DescFase))
        );

        $tareas = $GLOBALS['container']->get(ActividadTareaRepositoryInterface::class);
        $this->assertEquals(
            $tareas->getActividadTareas(['_ordre' => 'desc_tarea']),
            $tareas->matching(Criteria::create()->orderBy(ActividadTareaField::DescTarea))
        );
    }

    public function test_tipos_de_proceso_equivalen_a_legacy(): void
    {
        $repo = $GLOBALS['container']->get(ProcesoTipoRepositoryInterface::class);
        $this->assertEquals(
            $repo->getProcesoTipos(['sfsv' => 1, '_ordre' => 'nom_proceso']),
            $repo->matching(
                Criteria::create()
                    ->where(Filter::eq(ProcesoTipoField::Sfsv, 1))
                    ->orderBy(ProcesoTipoField::NomProceso)
            )
        );
    }

    public function test_tareas_proceso_conservan_el_json_de_fases_previas(): void
    {
        $repo = $GLOBALS['container']->get(TareaProcesoRepositoryInterface::class);
        $this->assertEquals(
            $repo->getTareasProceso(['_ordre' => 'id_tipo_proceso, id_fase, id_tarea', '_limit' => 30]),
            $repo->matching(
                Criteria::create()
                    ->orderBy(TareaProcesoField::IdTipoProceso)
                    ->addOrderBy(TareaProcesoField::IdFase)
                    ->addOrderBy(TareaProcesoField::IdTarea)
                    ->limit(30)
            )
        );
    }

    public function test_actividad_proceso_tarea_equivale_a_legacy(): void
    {
        $repo = $GLOBALS['container']->get(ActividadProcesoTareaRepositoryInterface::class);
        $this->assertEquals(
            $repo->getActividadProcesoTareas(['_ordre' => 'id_item', '_limit' => 30]),
            $repo->matching(Criteria::create()->orderBy(ActividadProcesoTareaField::IdItem)->limit(30))
        );
    }

    public function test_permisos_de_usuario_con_alias_de_tipo_equivalen_a_legacy(): void
    {
        $repo = $GLOBALS['container']->get(PermUsuarioActividadRepositoryInterface::class);
        [$aWhere, $aOperador] = ActividadTipoIdTxt::applyToRepositoryWhere(['dl_propia' => 't'], [], '17');
        $this->assertEquals(
            $repo->getPermUsuarioActividades($aWhere, $aOperador),
            $repo->matching(
                Criteria::create()
                    ->where(Filter::eq(PermUsuarioActividadField::DlPropia, true))
                    ->andWhere(ActividadTipoIdTxt::filterForRepository(PermUsuarioActividadField::IdTipoActivTxt, '17'))
            )
        );
    }
}
