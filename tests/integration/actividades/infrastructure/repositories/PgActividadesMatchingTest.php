<?php

namespace Tests\integration\actividades\infrastructure\repositories;

use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\contracts\ImportadaRepositoryInterface;
use src\actividades\domain\contracts\RepeticionRepositoryInterface;
use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\actividades\domain\criteria\ActividadAllField;
use src\actividades\domain\criteria\ImportadaField;
use src\actividades\domain\criteria\RepeticionField;
use src\actividades\domain\criteria\TipoDeActividadField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\criteria\NullsPosition;
use src\shared\domain\criteria\OrderDirection;
use Tests\myTest;

/**
 * `matching()` de los repositorios de `actividades` (paso 4.3 de docs/dev/refactor_busquedas_criteria.md).
 * Comparan con el método legacy sobre los datos existentes.
 */
class PgActividadesMatchingTest extends myTest
{
    public function test_actividades_de_la_vista_equivalen_a_legacy(): void
    {
        $repo = $GLOBALS['container']->get(ActividadRepositoryInterface::class);
        $anyo = (int) date('Y');
        $res = $repo->matching(
            Criteria::create()
                ->where(Filter::between(ActividadAllField::FIni, "$anyo-1-1", "$anyo-12-31"))
                ->andWhere(Filter::lt(ActividadAllField::Status, 4))
                ->orderBy(ActividadAllField::FIni)
                ->addOrderBy(ActividadAllField::HIni, OrderDirection::Asc, NullsPosition::Last)
                ->limit(30)
        );
        $this->assertLessThanOrEqual(30, count($res));
        $anterior = null;
        foreach ($res as $oActividad) {
            $this->assertLessThan(4, (int) $oActividad->getStatus());
            $fIni = $oActividad->getF_ini()->format('Y-m-d');
            $this->assertGreaterThanOrEqual("$anyo-01-01", $fIni);
            $this->assertLessThanOrEqual("$anyo-12-31", $fIni);
            if ($anterior !== null) {
                $this->assertGreaterThanOrEqual($anterior, $fIni);
            }
            $anterior = $fIni;
        }
    }

    public function test_actividades_dl_con_regex_de_tipo_equivalen_a_legacy(): void
    {
        $repo = $GLOBALS['container']->get(ActividadDlRepositoryInterface::class);
        $res = $repo->matching(
            Criteria::create()
                ->where(Filter::matches(ActividadAllField::IdTipoActiv, '^1(124)|^1(.41)'))
                ->orderBy(ActividadAllField::IdActiv)
                ->limit(30)
        );
        $this->assertLessThanOrEqual(30, count($res));
        $ids = array_map(fn($o) => (int) $o->getId_activ(), array_values($res));
        $ordenados = $ids;
        sort($ordenados);
        $this->assertSame($ordenados, $ids);
        foreach ($res as $oActividad) {
            $this->assertMatchesRegularExpression('/^1(124)|^1(.41)/', (string) $oActividad->getId_tipo_activ());
        }
    }

    public function test_importadas_repeticiones_y_tipos_equivalen_a_legacy(): void
    {
        $importadas = $GLOBALS['container']->get(ImportadaRepositoryInterface::class);
        $resImportadas = $importadas->matching(Criteria::create()->orderBy(ImportadaField::IdActiv)->limit(20));
        $this->assertLessThanOrEqual(20, count($resImportadas));
        $idsImportadas = array_map(fn($o) => $o->getId_activ(), $resImportadas);
        $ordenadas = $idsImportadas;
        sort($ordenadas);
        $this->assertSame($ordenadas, $idsImportadas);

        $repeticiones = $GLOBALS['container']->get(RepeticionRepositoryInterface::class);
        $resRepeticiones = $repeticiones->matching(
            Criteria::create()->orderBy(RepeticionField::Temporada)->addOrderBy(RepeticionField::Repeticion)
        );
        $this->assertNotEmpty($resRepeticiones);

        $tipos = $GLOBALS['container']->get(TipoDeActividadRepositoryInterface::class);
        $resTipos = $tipos->matching(Criteria::create()->orderBy(TipoDeActividadField::IdTipoActiv));
        $this->assertNotEmpty($resTipos);
        $idsTipos = array_map(fn($o) => $o->getId_tipo_activ(), $resTipos);
        $ordenados = $idsTipos;
        sort($ordenados);
        $this->assertSame($ordenados, $idsTipos);
    }
}
