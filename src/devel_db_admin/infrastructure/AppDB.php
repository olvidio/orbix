<?php

declare(strict_types=1);

namespace src\devel_db_admin\infrastructure;

use src\configuracion\domain\contracts\ModuloRepositoryInterface;
use src\configuracion\domain\contracts\AppRepositoryInterface;
use src\configuracion\domain\contracts\ModuloInstaladoRepositoryInterface;
use src\configuracion\domain\ModulosConfig;

class AppDB
{
    private int $id_mod;
    private ModulosConfig $oModuloConfig;

    public function __construct(
        int $id_mod,
        ModuloRepositoryInterface $moduloRepository,
        AppRepositoryInterface $appRepository,
        ModuloInstaladoRepositoryInterface $moduloInstaladoRepository,
    ) {
        $this->id_mod = $id_mod;
        $oModulo = $moduloRepository->findById($id_mod);
        if ($oModulo === null) {
            throw new \InvalidArgumentException(sprintf('Módulo %d no encontrado.', $id_mod));
        }
        $this->oModuloConfig = new ModulosConfig($moduloRepository, $appRepository, $moduloInstaladoRepository);
    }

    /*
     * Genera las tablas del esquema correspondiente
     */
    public function createTables(): void
    {
        $configSession = $_SESSION['config'] ?? null;
        $sessionApps = is_array($configSession) ? ($configSession['a_apps'] ?? null) : null;
        if (!is_array($sessionApps)) {
            return;
        }
        /** @var array<string, int|string> $a_todasApps */
        $a_todasApps = $sessionApps;
        $a_apps = $this->oModuloConfig->getApps($this->id_mod);
        foreach ($a_apps as $id_app) {
            $nom_app = array_search($id_app, $a_todasApps, true);
            if ($nom_app === false) {
                continue;
            }
            $this->createTablesApp((string) $nom_app);
        }
    }

    private function createTablesApp(string $nom_app): void
    {
        $legacyClass = "$nom_app\\db\\DBEsquema";
        $srcClass = 'src\\' . "$nom_app\\db\\DBEsquema";
        ModuleDbClassInvoker::invokeMethod($legacyClass, $srcClass, 'createAll');
        ModuleDbClassInvoker::invokeMethod($legacyClass, $srcClass, 'llenarAll');
    }

    /*
     * Eliminar las tablas del esquema correspondiente
     */
    public function dropTables(): void
    {
        $configSession = $_SESSION['config'] ?? null;
        $sessionApps = is_array($configSession) ? ($configSession['a_apps'] ?? null) : null;
        if (!is_array($sessionApps)) {
            return;
        }
        /** @var array<string, int|string> $a_todasApps */
        $a_todasApps = $sessionApps;
        $a_apps = $this->oModuloConfig->getApps($this->id_mod);
        foreach ($a_apps as $id_app) {
            $nom_app = array_search($id_app, $a_todasApps, true);
            if ($nom_app === false) {
                continue;
            }
            $this->dropTablesApp((string) $nom_app);
        }
    }

    private function dropTablesApp(string $nom_app): void
    {
        $legacyClass = "$nom_app\\db\\DBEsquema";
        $srcClass = 'src\\' . "$nom_app\\db\\DBEsquema";
        ModuleDbClassInvoker::invokeMethod($legacyClass, $srcClass, 'dropAll');
    }
}
