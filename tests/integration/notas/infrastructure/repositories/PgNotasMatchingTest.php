<?php

namespace Tests\integration\notas\infrastructure\persistence\postgresql;

use src\notas\domain\contracts\ActaDlRepositoryInterface;
use src\notas\domain\contracts\ActaTribunalDlRepositoryInterface;
use src\notas\domain\contracts\NotaRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaDlRepositoryInterface;
use src\notas\domain\criteria\ActaField;
use src\notas\domain\criteria\ActaTribunalField;
use src\notas\domain\criteria\NotaField;
use src\notas\domain\criteria\PersonaNotaField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\criteria\OrderDirection;
use Tests\factories\notas\ActaFactory;
use Tests\factories\notas\ActaTribunalFactory;
use Tests\factories\notas\PersonaNotaFactory;
use Tests\myTest;

/**
 * `matching()` de los repositorios de `notas` (paso 4.4 de docs/dev/refactor_busquedas_criteria.md).
 */
class PgNotasMatchingTest extends myTest
{
    public function test_acta_matching_y_sin_pdf_equivalen_a_legacy(): void
    {
        $repo = $GLOBALS['container']->get(ActaDlRepositoryInterface::class);
        $o = (new ActaFactory())->createSimple();
        $acta = $o->getActa();
        $repo->Guardar($o);

        $criteria = Criteria::create()
            ->where(Filter::eq(ActaField::Acta, $acta))
            ->orderBy(ActaField::FActa, OrderDirection::Desc);
        $nuevo = $repo->matching($criteria);
        $legacy = $repo->getActas(['acta' => $acta, '_ordre' => 'f_acta DESC']);
        $this->assertCount(1, $nuevo);
        $this->assertEquals($legacy, $nuevo);

        $nuevoSinPdf = $repo->matchingSinPdf($criteria);
        $legacySinPdf = $repo->getActas(['acta' => $acta, '_ordre' => 'f_acta DESC', '_sin_pdf' => true]);
        $this->assertCount(1, $nuevoSinPdf);
        $this->assertEquals($legacySinPdf, $nuevoSinPdf);

        $repo->Eliminar($o);
    }

    public function test_acta_matching_between_fechas_equivale_a_legacy(): void
    {
        $repo = $GLOBALS['container']->get(ActaDlRepositoryInterface::class);
        $o = (new ActaFactory())->createSimple();
        $repo->Guardar($o);

        $nuevo = $repo->matching(
            Criteria::create()->where(Filter::between(ActaField::FActa, '2025-10-01', '2025-10-31'))
        );
        $legacy = $repo->getActas(['f_acta' => "'2025-10-01','2025-10-31'"], ['f_acta' => 'BETWEEN']);
        $this->assertNotEmpty($nuevo);
        $this->assertEquals($legacy, $nuevo);

        $repo->Eliminar($o);
    }

    public function test_acta_tribunal_matching_equivale_a_legacy(): void
    {
        $repo = $GLOBALS['container']->get(ActaTribunalDlRepositoryInterface::class);
        $o = (new ActaTribunalFactory())->createSimple();
        $repo->Guardar($o);

        $nuevo = $repo->matching(
            Criteria::create()->where(Filter::eq(ActaTribunalField::IdItem, $o->getId_item()))->orderBy(ActaTribunalField::Orden)
        );
        $legacy = $repo->getActasTribunales(['id_item' => $o->getId_item(), '_ordre' => 'orden']);
        $this->assertCount(1, $nuevo);
        $this->assertEquals($legacy, $nuevo);

        $repo->Eliminar($o);
    }

    public function test_persona_nota_matching_equivale_a_legacy(): void
    {
        $repo = $GLOBALS['container']->get(PersonaNotaDlRepositoryInterface::class);
        $o = (new PersonaNotaFactory())->createSimple();
        $idNom = $o->getId_nom();
        $repo->Guardar($o);

        $nuevo = $repo->matching(
            Criteria::create()
                ->where(Filter::eq(PersonaNotaField::IdNom, $idNom))
                ->andWhere(Filter::lt(PersonaNotaField::IdNivel, 99999))
                ->orderBy(PersonaNotaField::IdNivel)
        );
        $legacy = $repo->getPersonaNotas(
            ['id_nom' => $idNom, 'id_nivel' => 99999, '_ordre' => 'id_nivel'],
            ['id_nivel' => '<']
        );
        $this->assertNotEmpty($nuevo);
        $this->assertEquals($legacy, $nuevo);

        $repo->Eliminar($o);
    }

    public function test_nota_matching_equivale_a_legacy(): void
    {
        $repo = $GLOBALS['container']->get(NotaRepositoryInterface::class);

        $nuevo = $repo->matching(Criteria::create()->orderBy(NotaField::IdSituacion));
        $this->assertNotEmpty($nuevo);
        $this->assertEquals($repo->getNotas(['_ordre' => 'id_situacion']), $nuevo);
    }
}
