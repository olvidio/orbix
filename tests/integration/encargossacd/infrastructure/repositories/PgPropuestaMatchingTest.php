<?php

namespace Tests\integration\encargossacd\infrastructure\persistence\postgresql;

use src\encargossacd\domain\contracts\PropuestaEncargoSacdHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\PropuestaEncargoSacdRepositoryInterface;
use src\encargossacd\domain\criteria\PropuestaEncargoSacdField;
use src\encargossacd\domain\criteria\PropuestaEncargoSacdHorarioField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\criteria\OrderDirection;
use Tests\myTest;

/**
 * `matching()` de las propuestas de encargos (paso 4.5 de docs/dev/refactor_busquedas_criteria.md).
 * Las tablas de propuestas solo existen cuando se ha creado el staging: si faltan, se omite.
 */
class PgPropuestaMatchingTest extends myTest
{
    public function test_propuesta_encargo_sacd_matching_equivale_a_legacy(): void
    {
        $repo = $GLOBALS['container']->get(PropuestaEncargoSacdRepositoryInterface::class);
        if (!$repo->existenLasTablas()) {
            $this->markTestSkipped('No existe propuesta_encargos_sacd en la base de pruebas.');
        }

        $this->assertEquals(
            $repo->getPropuestasEncargoSacd(['id_nom_new' => 1, 'f_fin' => 'x', '_ordre' => 'modo'], ['f_fin' => 'IS NULL']),
            $repo->matching(
                Criteria::create()
                    ->where(Filter::eq(PropuestaEncargoSacdField::IdNomNew, 1))
                    ->andWhere(Filter::isNull(PropuestaEncargoSacdField::FFin))
                    ->orderBy(PropuestaEncargoSacdField::Modo)
            )
        );
        $this->assertEquals(
            $repo->getPropuestasEncargoSacd(['modo' => '[23]', '_ordre' => 'id_item'], ['modo' => '~']),
            $repo->matching(
                Criteria::create()
                    ->where(Filter::matches(PropuestaEncargoSacdField::Modo, '[23]'))
                    ->orderBy(PropuestaEncargoSacdField::IdItem)
            )
        );
    }

    public function test_propuesta_horario_matching_equivale_a_legacy(): void
    {
        $repoEnc = $GLOBALS['container']->get(PropuestaEncargoSacdRepositoryInterface::class);
        if (!$repoEnc->existenLasTablas()) {
            $this->markTestSkipped('No existe propuesta_encargos_sacd en la base de pruebas.');
        }
        $repo = $GLOBALS['container']->get(PropuestaEncargoSacdHorarioRepositoryInterface::class);

        $this->assertEquals(
            $repo->getEncargoSacdHorarios(['id_nom' => 1, 'f_fin' => 'x', '_ordre' => 'f_ini DESC'], ['f_fin' => 'IS NULL']),
            $repo->matching(
                Criteria::create()
                    ->where(Filter::eq(PropuestaEncargoSacdHorarioField::IdNom, 1))
                    ->andWhere(Filter::isNull(PropuestaEncargoSacdHorarioField::FFin))
                    ->orderBy(PropuestaEncargoSacdHorarioField::FIni, OrderDirection::Desc)
            )
        );
        $this->assertEquals(
            $repo->getEncargoSacdHorarios(['id_nom' => 'x', '_ordre' => 'id_item'], ['id_nom' => 'IS NOT NULL']),
            $repo->matching(
                Criteria::create()
                    ->where(Filter::isNotNull(PropuestaEncargoSacdHorarioField::IdNom))
                    ->orderBy(PropuestaEncargoSacdHorarioField::IdItem)
            )
        );
    }
}
