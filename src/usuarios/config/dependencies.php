<?php

use src\usuarios\domain\contracts\GrupoRepositoryInterface;
use src\usuarios\domain\contracts\LocalRepositoryInterface;
use src\usuarios\domain\contracts\PermMenuRepositoryInterface;
use src\usuarios\domain\contracts\PreferenciaRepositoryInterface;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioGrupoRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\usuarios\infrastructure\persistence\postgresql\PgGrupoRepository;
use src\usuarios\infrastructure\persistence\postgresql\PgLocalRepository;
use src\usuarios\infrastructure\persistence\postgresql\PgPermMenuRepository;
use src\usuarios\infrastructure\persistence\postgresql\PgPreferenciaRepository;
use src\usuarios\infrastructure\persistence\postgresql\PgRoleRepository;
use src\usuarios\infrastructure\persistence\postgresql\PgUsuarioGrupoRepository;
use src\usuarios\infrastructure\persistence\postgresql\PgUsuarioRepository;
use function DI\autowire;

return [
    // Mapeo simple: Interfaz => Clase
    // 'autowire()' le dice a PHP-DI: "Intenta inyectar el PDO automáticamente en el constructor de Pg...Repository"
    GrupoRepositoryInterface::class => autowire(PgGrupoRepository::class),
    LocalRepositoryInterface::class => autowire(PgLocalRepository::class),
    PermMenuRepositoryInterface::class => autowire(PgPermMenuRepository::class),
    PreferenciaRepositoryInterface::class => autowire(PgPreferenciaRepository::class),
    RoleRepositoryInterface::class => autowire(PgRoleRepository::class),
    UsuarioGrupoRepositoryInterface::class => autowire(PgUsuarioGrupoRepository::class),
    UsuarioRepositoryInterface::class => autowire(PgUsuarioRepository::class),
];