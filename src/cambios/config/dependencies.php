<?php

use src\cambios\domain\contracts\CambioDlRepositoryInterface;
use src\cambios\domain\contracts\CambioRepositoryInterface;
use src\cambios\infrastructure\repositories\PgCambioDlRepository;
use src\cambios\domain\contracts\CambioAnotadoRepositoryInterface;
use src\cambios\infrastructure\repositories\PgCambioAnotadoRepository;
use src\cambios\domain\contracts\CambioUsuarioRepositoryInterface;
use src\cambios\infrastructure\repositories\PgCambioRepository;
use src\cambios\infrastructure\repositories\PgCambioUsuarioRepository;
use src\cambios\domain\contracts\CambioUsuarioObjetoPrefRepositoryInterface;
use src\cambios\infrastructure\repositories\PgCambioUsuarioObjetoPrefRepository;
use src\cambios\domain\contracts\CambioUsuarioPropiedadPrefRepositoryInterface;
use src\cambios\infrastructure\repositories\PgCambioUsuarioPropiedadPrefRepository;
use function DI\autowire;

return [
// Mapeos de Interfaces a Implementaciones
    CambioAnotadoRepositoryInterface::class => autowire(PgCambioAnotadoRepository::class),
    CambioDlRepositoryInterface::class => autowire(PgCambioDlRepository::class),
    CambioRepositoryInterface::class => autowire(PgCambioRepository::class),
    CambioUsuarioObjetoPrefRepositoryInterface::class => autowire(PgCambioUsuarioObjetoPrefRepository::class),
    CambioUsuarioPropiedadPrefRepositoryInterface::class => autowire(PgCambioUsuarioPropiedadPrefRepository::class),
    CambioUsuarioRepositoryInterface::class => autowire(PgCambioUsuarioRepository::class),
];
