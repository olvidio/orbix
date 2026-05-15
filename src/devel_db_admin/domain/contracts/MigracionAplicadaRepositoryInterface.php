<?php

declare(strict_types=1);

namespace src\devel_db_admin\domain\contracts;

use src\devel_db_admin\domain\entity\MigracionAplicada;

interface MigracionAplicadaRepositoryInterface
{
    public function ensureTabla(): void;

    /**
     * @return array<int, MigracionAplicada>
     */
    public function aplicadas(): array;

    public function findByKey(string $prefijo, string $descripcion, string $database): ?MigracionAplicada;

    public function existe(string $prefijo, string $descripcion, string $database): bool;

    public function registrar(MigracionAplicada $migracion): bool;

    public function Eliminar(MigracionAplicada $migracion): bool;
}
