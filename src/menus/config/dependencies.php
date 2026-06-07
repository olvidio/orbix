<?php

use src\menus\application\GrupMenuColeccionUseCase;
use src\menus\application\GrupMenuListaUseCase;
use src\menus\application\ListaMetaMenus;
use src\menus\application\ListaTemplatesMenus;
use src\menus\application\MenuCopiar;
use src\menus\application\MenuEliminar;
use src\menus\application\MenuGuardar;
use src\menus\application\MenuMover;
use src\menus\application\MenusBurgerLayoutDataUseCase;
use src\menus\application\MenusGetPageData;
use src\menus\application\MenusLegacyLayoutItemsUseCase;
use src\menus\application\MenusVisiblesPorGrupoMenuUseCase;
use src\menus\domain\contracts\GrupMenuRepositoryInterface;
use src\menus\domain\contracts\GrupMenuRoleRepositoryInterface;
use src\menus\domain\contracts\MenuDbRepositoryInterface;
use src\menus\domain\contracts\MetaMenuRepositoryInterface;
use src\menus\domain\contracts\TemplateMenuRepositoryInterface;
use src\menus\domain\InfoGrupMenus;
use src\menus\domain\InfoMetaMenus;
use src\menus\infrastructure\persistence\postgresql\PgGrupMenuRepository;
use src\menus\infrastructure\persistence\postgresql\PgGrupMenuRoleRepository;
use src\menus\infrastructure\persistence\postgresql\PgMenuDbRepository;
use src\menus\infrastructure\persistence\postgresql\PgMetaMenuRepository;
use src\menus\infrastructure\persistence\postgresql\PgTemplateMenuRepository;
use function DI\autowire;

return [
    GrupMenuRepositoryInterface::class => autowire(PgGrupMenuRepository::class),
    GrupMenuRoleRepositoryInterface::class => autowire(PgGrupMenuRoleRepository::class),
    MenuDbRepositoryInterface::class => autowire(PgMenuDbRepository::class),
    MetaMenuRepositoryInterface::class => autowire(PgMetaMenuRepository::class),
    TemplateMenuRepositoryInterface::class => autowire(PgTemplateMenuRepository::class),

    InfoGrupMenus::class => autowire(InfoGrupMenus::class),
    InfoMetaMenus::class => autowire(InfoMetaMenus::class),

    GrupMenuColeccionUseCase::class => autowire(GrupMenuColeccionUseCase::class),
    GrupMenuListaUseCase::class => autowire(GrupMenuListaUseCase::class),
    ListaMetaMenus::class => autowire(ListaMetaMenus::class),
    ListaTemplatesMenus::class => autowire(ListaTemplatesMenus::class),
    MenuCopiar::class => autowire(MenuCopiar::class),
    MenuEliminar::class => autowire(MenuEliminar::class),
    MenuGuardar::class => autowire(MenuGuardar::class),
    MenuMover::class => autowire(MenuMover::class),
    MenusBurgerLayoutDataUseCase::class => autowire(MenusBurgerLayoutDataUseCase::class),
    MenusGetPageData::class => autowire(MenusGetPageData::class),
    MenusLegacyLayoutItemsUseCase::class => autowire(MenusLegacyLayoutItemsUseCase::class),
    MenusVisiblesPorGrupoMenuUseCase::class => autowire(MenusVisiblesPorGrupoMenuUseCase::class),
];
