<?php

namespace Tests\integration\encargossacd\infrastructure\persistence\postgresql;

use src\encargossacd\domain\contracts\EncargoHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdHorarioRepositoryInterface;
use src\encargossacd\domain\criteria\EncargoHorarioField;
use src\encargossacd\domain\criteria\EncargoSacdHorarioField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\criteria\OrderDirection;
use Tests\factories\encargossacd\EncargoHorarioFactory;
use Tests\factories\encargossacd\EncargoSacdHorarioFactory;
use Tests\myTest;

/**
 * `matching()` de `EncargoHorario` y `EncargoSacdHorario` (paso 4.5 de docs/dev/refactor_busquedas_criteria.md).
 */
class PgEncargoHorarioMatchingTest extends myTest
{
    public function test_encargo_horario_matching_equivale_a_legacy(): void
    {
        $repo = $GLOBALS['container']->get(EncargoHorarioRepositoryInterface::class);
        $oHorario = (new EncargoHorarioFactory())->createSimple();
        $this->assertTrue($repo->Guardar($oHorario));

        try {
            $id_enc = $oHorario->getId_enc();

            $nuevo = $repo->matching(
                Criteria::create()
                    ->where(Filter::eq(EncargoHorarioField::IdEnc, $id_enc))
                    ->orderBy(EncargoHorarioField::FIni, OrderDirection::Desc)
            );
            $this->assertNotEmpty($nuevo);
            $this->assertEquals($id_enc, $nuevo[0]->getId_enc());

            $sinFin = $repo->matching(
                Criteria::create()
                    ->where(Filter::eq(EncargoHorarioField::IdEnc, $id_enc))
                    ->andWhere(Filter::isNull(EncargoHorarioField::FFin))
            );
            foreach ($sinFin as $h) {
                $this->assertNull($h->getF_fin());
            }
        } finally {
            $repo->Eliminar($oHorario);
        }
    }

    public function test_encargo_sacd_horario_matching_equivale_a_legacy(): void
    {
        $repo = $GLOBALS['container']->get(EncargoSacdHorarioRepositoryInterface::class);
        $oHorario = (new EncargoSacdHorarioFactory())->createSimple();
        $this->assertTrue($repo->Guardar($oHorario));

        try {
            $id_enc = $oHorario->getId_enc();
            $id_nom = $oHorario->getId_nom();

            $nuevo = $repo->matching(
                Criteria::create()
                    ->where(Filter::eq(EncargoSacdHorarioField::IdEnc, $id_enc))
                    ->andWhere(Filter::eq(EncargoSacdHorarioField::IdNom, $id_nom))
                    ->orderBy(EncargoSacdHorarioField::IdItem, OrderDirection::Desc)
                    ->limit(1)
            );
            $this->assertCount(1, $nuevo);
            $this->assertEquals($id_nom, $nuevo[0]->getId_nom());

            $sinFin = $repo->matching(
                Criteria::create()
                    ->where(Filter::eq(EncargoSacdHorarioField::IdNom, $id_nom))
                    ->andWhere(Filter::isNull(EncargoSacdHorarioField::FFin))
                    ->orderBy(EncargoSacdHorarioField::FIni, OrderDirection::Desc)
            );
            foreach ($sinFin as $h) {
                $this->assertNull($h->getF_fin());
            }
        } finally {
            $repo->Eliminar($oHorario);
        }
    }
}
