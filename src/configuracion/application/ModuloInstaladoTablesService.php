<?php

declare(strict_types=1);

namespace src\configuracion\application;

use src\configuracion\domain\contracts\AppRepositoryInterface;
use src\configuracion\domain\contracts\ModuloInstaladoRepositoryInterface;
use src\configuracion\domain\contracts\ModuloRepositoryInterface;
use src\devel_db_admin\infrastructure\AppDB;

/**
 * Crea o elimina tablas de esquema al instalar / desinstalar un módulo (apptables).
 */
final class ModuloInstaladoTablesService
{
    public function __construct(
        private ModuloRepositoryInterface $moduloRepository,
        private AppRepositoryInterface $appRepository,
        private ModuloInstaladoRepositoryInterface $moduloInstaladoRepository,
    ) {
    }

    public function createTables(int $idMod): void
    {
        $this->appDb($idMod)->createTables();
    }

    public function dropTables(int $idMod): void
    {
        $this->appDb($idMod)->dropTables();
    }

    private function appDb(int $idMod): AppDB
    {
        return new AppDB(
            $idMod,
            $this->moduloRepository,
            $this->appRepository,
            $this->moduloInstaladoRepository,
        );
    }
}
