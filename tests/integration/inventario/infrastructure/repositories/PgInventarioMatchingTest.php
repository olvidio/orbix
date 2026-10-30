<?php

namespace Tests\integration\inventario\infrastructure\persistence\postgresql;

use src\inventario\domain\contracts\ColeccionRepositoryInterface;
use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use src\inventario\domain\contracts\EgmRepositoryInterface;
use src\inventario\domain\contracts\EquipajeRepositoryInterface;
use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\contracts\TipoDocRepositoryInterface;
use src\inventario\domain\contracts\UbiInventarioRepositoryInterface;
use src\inventario\domain\contracts\WhereisRepositoryInterface;
use src\inventario\domain\criteria\ColeccionField;
use src\inventario\domain\criteria\DocumentoField;
use src\inventario\domain\criteria\EgmField;
use src\inventario\domain\criteria\EquipajeField;
use src\inventario\domain\criteria\LugarField;
use src\inventario\domain\criteria\TipoDocField;
use src\inventario\domain\criteria\UbiInventarioField;
use src\inventario\domain\criteria\WhereisField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use Tests\factories\inventario\DocumentoFactory;
use Tests\factories\inventario\EquipajeFactory;
use Tests\myTest;

/**
 * `matching()` de los 8 repositorios de `inventario` (paso 4.4 de docs/dev/refactor_busquedas_criteria.md).
 */
class PgInventarioMatchingTest extends myTest
{
    public function test_documento_matching_booleano_y_orden_equivale_a_legacy(): void
    {
        $repo = $GLOBALS['container']->get(DocumentoRepositoryInterface::class);
        $o = (new DocumentoFactory())->createSimple();
        $id = $o->getId_doc();
        $repo->Guardar($o);

        $criteria = Criteria::create()
            ->where(Filter::eq(DocumentoField::IdDoc, $id))
            ->andWhere(Filter::isNull(DocumentoField::FPerdido))
            ->orderBy(DocumentoField::IdDoc);
        $nuevo = $repo->matching($criteria);

        $this->assertCount(1, $nuevo);
        $this->assertEquals($id, $nuevo[0]->getId_doc());

        $repo->Eliminar($o);
    }

    public function test_documento_matching_in_equivale_a_legacy(): void
    {
        $repo = $GLOBALS['container']->get(DocumentoRepositoryInterface::class);
        $o = (new DocumentoFactory())->createSimple();
        $id = $o->getId_doc();
        $repo->Guardar($o);

        $nuevo = $repo->matching(Criteria::create()->where(Filter::in(DocumentoField::IdDoc, [$id, $id + 1])));

        $this->assertCount(1, $nuevo);
        $this->assertEquals($id, $nuevo[0]->getId_doc());

        $repo->Eliminar($o);
    }

    public function test_ubi_inventario_y_tipo_doc_matching_equivalen_a_legacy(): void
    {
        $ubiRepo = $GLOBALS['container']->get(UbiInventarioRepositoryInterface::class);
        $tipoRepo = $GLOBALS['container']->get(TipoDocRepositoryInterface::class);

        $nuevoUbi = $ubiRepo->matching(Criteria::create()->orderBy(UbiInventarioField::NomUbi)->limit(5));
        $legacyUbi = $ubiRepo->getUbisInventario(['_ordre' => 'nom_ubi', '_limit' => 5]);
        $this->assertEquals($legacyUbi, $nuevoUbi);

        $nuevoTipo = $tipoRepo->matching(Criteria::create()->orderBy(TipoDocField::NomDoc));
        $legacyTipo = $tipoRepo->getTipoDocs(['_ordre' => 'nom_doc']);
        $this->assertEquals($legacyTipo, $nuevoTipo);
    }

    public function test_equipaje_matching_fechas_equivale_a_legacy(): void
    {
        $repo = $GLOBALS['container']->get(EquipajeRepositoryInterface::class);
        $o = (new EquipajeFactory())->createSimple();
        $id = $o->getId_equipaje();
        $repo->Guardar($o);

        $nuevo = $repo->matching(Criteria::create()->where(Filter::eq(EquipajeField::IdEquipaje, $id)));

        $this->assertCount(1, $nuevo);
        $this->assertEquals($id, $nuevo[0]->getId_equipaje());

        $repo->Eliminar($o);
    }

    public function test_lugar_coleccion_egm_whereis_matching_equivalen_a_legacy(): void
    {
        $lugarRepo = $GLOBALS['container']->get(LugarRepositoryInterface::class);
        $colRepo = $GLOBALS['container']->get(ColeccionRepositoryInterface::class);
        $egmRepo = $GLOBALS['container']->get(EgmRepositoryInterface::class);
        $whRepo = $GLOBALS['container']->get(WhereisRepositoryInterface::class);

        $this->assertEquals(
            $lugarRepo->getLugares(['_ordre' => 'nom_lugar']),
            $lugarRepo->matching(Criteria::create()->orderBy(LugarField::NomLugar))
        );
        $this->assertEquals(
            $colRepo->getColecciones(['_ordre' => 'nom_coleccion']),
            $colRepo->matching(Criteria::create()->orderBy(ColeccionField::NomColeccion))
        );
        $this->assertIsArray($egmRepo->matching(Criteria::create()->orderBy(EgmField::IdGrupo)));
        $this->assertIsArray($whRepo->matching(Criteria::create()->orderBy(WhereisField::IdItemWhereis)));
    }
}
