<?php

use src\menus\domain\contracts\GrupMenuRepositoryInterface;
use src\menus\domain\contracts\GrupMenuRoleRepositoryInterface;
use src\menus\domain\contracts\MenuDbRepositoryInterface;
use src\menus\domain\contracts\MetaMenuRepositoryInterface;
use src\menus\domain\contracts\TemplateMenuRepositoryInterface;
use src\menus\infrastructure\persistence\postgresql\PgGrupMenuRepository;
use src\menus\infrastructure\persistence\postgresql\PgGrupMenuRoleRepository;
use src\menus\infrastructure\persistence\postgresql\PgMenuDbRepository;
use src\menus\infrastructure\persistence\postgresql\PgMetaMenuRepository;
use src\menus\infrastructure\persistence\postgresql\PgTemplateMenuRepository;
use function DI\autowire;

return [
    // Mapeos de Interfaces a Implementaciones
    GrupMenuRepositoryInterface::class => autowire(PgGrupMenuRepository::class),
    GrupMenuRoleRepositoryInterface::class => autowire(PgGrupMenuRoleRepository::class),
    MenuDbRepositoryInterface::class => autowire(PgMenuDbRepository::class),
    MetaMenuRepositoryInterface::class => autowire(PgMetaMenuRepository::class),
    TemplateMenuRepositoryInterface::class => autowire(PgTemplateMenuRepository::class),
];
