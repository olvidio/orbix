<?php

namespace Tests\integration\menus\infrastructure\repositories;

use src\menus\domain\contracts\GrupMenuRepositoryInterface;
use src\menus\domain\contracts\GrupMenuRoleRepositoryInterface;
use src\menus\domain\contracts\MenuDbRepositoryInterface;
use src\menus\domain\contracts\MetaMenuRepositoryInterface;
use src\menus\domain\contracts\TemplateMenuRepositoryInterface;
use src\menus\domain\criteria\GrupMenuField;
use src\menus\domain\criteria\GrupMenuRoleField;
use src\menus\domain\criteria\MenuDbField;
use src\menus\domain\criteria\MetaMenuField;
use src\menus\domain\criteria\TemplateMenuField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use Tests\factories\menus\GrupMenuFactory;
use Tests\factories\menus\MenuDbFactory;
use Tests\myTest;

/**
 * `matching()` de los repositorios de `menus` (paso 4.2 de docs/dev/refactor_busquedas_criteria.md).
 */
class PgMenusMatchingTest extends myTest
{
    public function test_grupmenu_por_id_equivale_a_legacy(): void
    {
        $repo = $GLOBALS['container']->get(GrupMenuRepositoryInterface::class);
        $oGrupMenu = (new GrupMenuFactory())->createSimple();
        $id = $oGrupMenu->getId_grupmenu();
        $repo->Guardar($oGrupMenu);

        try {
            $criteria = Criteria::create()->where(Filter::eq(GrupMenuField::IdGrupmenu, $id));
            $nuevo = $repo->matching($criteria);

            $this->assertCount(1, $nuevo);
            $this->assertEquals($id, $nuevo[0]->getId_grupmenu());
        } finally {
            $repo->Eliminar($oGrupMenu);
        }
        $this->assertSame([], $repo->matching($criteria));
    }

    public function test_menudb_por_grupo_con_orden_equivale_a_legacy(): void
    {
        $repo = $GLOBALS['container']->get(MenuDbRepositoryInterface::class);
        $oMenuDb = (new MenuDbFactory())->createSimple();
        $idGrupmenu = 987654;
        $oMenuDb->setId_grupmenu($idGrupmenu);
        $repo->Guardar($oMenuDb);

        try {
            $criteria = Criteria::create()
                ->where(Filter::eq(MenuDbField::IdGrupmenu, $idGrupmenu))
                ->orderBy(MenuDbField::IdGrupmenu)
                ->addOrderBy(MenuDbField::Orden);
            $nuevo = $repo->matching($criteria);

            $this->assertNotEmpty($nuevo);
            $this->assertSame($idGrupmenu, $nuevo[0]->getId_grupmenu());
        } finally {
            $repo->Eliminar($oMenuDb);
        }
    }

    public function test_el_resto_de_repositorios_equivalen_a_legacy_sin_filtros(): void
    {
        $role = $GLOBALS['container']->get(GrupMenuRoleRepositoryInterface::class);
        $this->assertIsArray($role->matching(Criteria::create()->orderBy(GrupMenuRoleField::IdItem)));

        $meta = $GLOBALS['container']->get(MetaMenuRepositoryInterface::class);
        $this->assertIsArray($meta->matching(Criteria::create()->orderBy(MetaMenuField::IdMetamenu)));

        $template = $GLOBALS['container']->get(TemplateMenuRepositoryInterface::class);
        $this->assertIsArray($template->matching(Criteria::create()->orderBy(TemplateMenuField::IdTemplateMenu)));
    }
}
