<?php

use src\usuarios\application\AppMobileLogin;
use src\usuarios\application\GruposLista;
use src\usuarios\application\LoginProcesar;
use src\usuarios\application\PreferenciaTablaData;
use src\usuarios\application\rolesLista;
use src\usuarios\application\usuarioEliminar;
use src\usuarios\application\usuariosLista;
use src\usuarios\application\usuariosRegionContactos;
use src\usuarios\domain\GrupoJefeZona;
use src\usuarios\domain\InfoLocales;
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
    GrupoRepositoryInterface::class => autowire(PgGrupoRepository::class),
    LocalRepositoryInterface::class => autowire(PgLocalRepository::class),
    PermMenuRepositoryInterface::class => autowire(PgPermMenuRepository::class),
    PreferenciaRepositoryInterface::class => autowire(PgPreferenciaRepository::class),
    RoleRepositoryInterface::class => autowire(PgRoleRepository::class),
    UsuarioGrupoRepositoryInterface::class => autowire(PgUsuarioGrupoRepository::class),
    UsuarioRepositoryInterface::class => autowire(PgUsuarioRepository::class),

    AppMobileLogin::class => autowire(AppMobileLogin::class),
    GruposLista::class => autowire(GruposLista::class),
    GrupoJefeZona::class => autowire(GrupoJefeZona::class),
    InfoLocales::class => autowire(InfoLocales::class),
    LoginProcesar::class => autowire(LoginProcesar::class),
    PreferenciaTablaData::class => autowire(PreferenciaTablaData::class),
    rolesLista::class => autowire(rolesLista::class),
    usuarioEliminar::class => autowire(usuarioEliminar::class),
    usuariosLista::class => autowire(usuariosLista::class),
    usuariosRegionContactos::class => autowire(usuariosRegionContactos::class),
];
