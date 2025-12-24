<?php

use src\menus\domain\contracts\GrupMenuRepositoryInterface;
use src\menus\domain\contracts\GrupMenuRoleRepositoryInterface;
use src\menus\domain\contracts\MenuDbRepositoryInterface;
use src\menus\domain\contracts\MetaMenuRepositoryInterface;
use src\menus\domain\contracts\TemplateMenuRepositoryInterface;
use src\menus\infrastructure\repositories\PgGrupMenuRepository;
use src\menus\infrastructure\repositories\PgGrupMenuRoleRepository;
use src\menus\infrastructure\repositories\PgMenuDbRepository;
use src\menus\infrastructure\repositories\PgMetaMenuRepository;
use src\menus\infrastructure\repositories\PgTemplateMenuRepository;
use function DI\autowire;

return [
    // Mapeos de Interfaces a Implementaciones
    GrupMenuRepositoryInterface::class => autowire(PgGrupMenuRepository::class),
    GrupMenuRoleRepositoryInterface::class => autowire(PgGrupMenuRoleRepository::class),
    MenuDbRepositoryInterface::class => autowire(PgMenuDbRepository::class),
    MetaMenuRepositoryInterface::class => autowire(PgMetaMenuRepository::class),
    TemplateMenuRepositoryInterface::class => autowire(PgTemplateMenuRepository::class),
];
