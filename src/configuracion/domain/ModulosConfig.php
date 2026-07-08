<?php

namespace src\configuracion\domain;

use src\configuracion\domain\contracts\AppRepositoryInterface;
use src\configuracion\domain\contracts\ModuloInstaladoRepositoryInterface;
use src\configuracion\domain\contracts\ModuloRepositoryInterface;
use src\configuracion\domain\entity\App;
use src\configuracion\domain\entity\Modulo;
use src\configuracion\domain\entity\ModuloInstalado;

/**
 * Description of modulosconfig
 *
 * @author Daniel Serrabou <dani@moneders.net>
 */
class ModulosConfig
{
    /** @var array<int, array{nom: string, mods_req: list<int>, apps_req: list<int>}> */
    private array $a_mods_todos = [];

    /** @var array<int, string> */
    private array $a_apps_todas = [];

    /** @var list<Modulo> */
    private array $cMods = [];

    /** @var list<App> */
    private array $cApps = [];

    /** @var array<int, string> */
    private array $a_mods_installed = [];

    /** @var list<ModuloInstalado> */
    private array $cModsInstalados = [];

    public function __construct(
        private ModuloRepositoryInterface $moduloRepository,
        private AppRepositoryInterface $appRepository,
        private ModuloInstaladoRepositoryInterface $moduloInstaladoRepository,
    ) {
        $this->cMods = $this->moduloRepository->getModulos();
        $this->cApps = $this->appRepository->getApps();
    }

    /**
     * @return array<int, array{nom: string, mods_req: list<int>, apps_req: list<int>}>
     */
    public function getModsAll(): array
    {
        foreach ($this->cMods as $oMod) {
            $id_mod = $oMod->getId_mod();
            $nom_mod = $oMod->getNomVo()->value();
            $mods_req = array_values(array_map(intval(...), $oMod->getMods_req() ?? []));
            $apps_req = array_values(array_map(intval(...), $oMod->getApps_req() ?? []));
            $this->a_mods_todos[$id_mod] = [
                'nom' => $nom_mod,
                'mods_req' => $mods_req,
                'apps_req' => $apps_req,
            ];
        }
        return $this->a_mods_todos;
    }

    /**
     * @return array<int, string>
     */
    public function getAppsAll(): array
    {
        foreach ($this->cApps as $oApp) {
            $id_app = $oApp->getIdAppVo()->value();
            $nom_app = $oApp->getNomVo()->value();
            $this->a_apps_todas[$id_app] = $nom_app;
        }
        return $this->a_apps_todas;
    }

    /**
     * Todos los módulos instalados en el esquema (activos e inactivos).
     *
     * @return array<int, string>
     */
    public function getModsInstalados(): array
    {
        $this->cModsInstalados = $this->moduloInstaladoRepository->getModuloInstalados();
        $a_mods = $this->getModsAll();
        $this->a_mods_installed = [];
        foreach ($this->cModsInstalados as $oMod) {
            $id_mod = $oMod->getId_mod();
            $nom_mod = $a_mods[$id_mod]['nom'] ?? (string)$id_mod;
            $this->a_mods_installed[$id_mod] = $nom_mod;
        }
        return $this->a_mods_installed;
    }

    /**
     * Solo módulos instalados marcados como activos.
     *
     * @return array<int, string>
     */
    public function getModsInstaladosActivos(): array
    {
        $a_mods = $this->getModsAll();
        $activos = [];
        foreach ($this->moduloInstaladoRepository->getModuloInstalados() as $oMod) {
            if ($oMod->isActive() !== true) {
                continue;
            }
            $id_mod = $oMod->getId_mod();
            $activos[$id_mod] = $a_mods[$id_mod]['nom'] ?? (string)$id_mod;
        }

        return $activos;
    }

    /**
     * Módulos activos que declaran al indicado como requisito obligatorio.
     *
     * @return array<int, string> id_mod => nombre
     */
    public function getModulosActivosQueRequieren(int $id_mod): array
    {
        $a_mods = $this->getModsAll();
        $activos = $this->getModsInstaladosActivos();
        $dependientes = [];
        foreach ($activos as $id_activo => $nom) {
            if ($id_activo === $id_mod) {
                continue;
            }
            $mods_req = $a_mods[$id_activo]['mods_req'] ?? [];
            if (in_array($id_mod, $mods_req, true)) {
                $dependientes[$id_activo] = $nom;
            }
        }

        return $dependientes;
    }

    /**
     * @param array<int, string>|null $a_mods_activos
     * @return list<int>
     */
    public function getAppsMods(int $id_mod, ?array $a_mods_activos = null): array
    {
        if ($id_mod === 0) {
            return [];
        }
        $a_mods = $this->getModsAll();
        $mods_req = $a_mods[$id_mod]['mods_req'] ?? [];
        $a_mods_activos ??= $this->getModsInstaladosActivos();
        $all = [];
        foreach ($mods_req as $mod) {
            if (!array_key_exists($mod, $a_mods_activos)) {
                continue;
            }
            $all[] = $this->getApps($mod);
        }
        if ($all === []) {
            return $this->getApps($id_mod);
        }
        $apps = array_merge([], ...$all);
        $apps = array_merge($apps, $this->getApps($id_mod));

        return array_values(array_unique($apps));
    }

    /**
     * @return list<int>
     */
    public function getApps(int $id_mod): array
    {
        if ($id_mod === 0) {
            return [];
        }
        $a_mods = $this->getModsAll();
        return $a_mods[$id_mod]['apps_req'] ?? [];
    }

    /**
     * @return list<int>
     */
    public function getAppsInstaladas(): array
    {
        $app_installed = [];

        $a_mods_activos = $this->getModsInstaladosActivos();
        foreach ($a_mods_activos as $id_mod => $nom_mod) {
            $ap1 = $this->getAppsMods($id_mod, $a_mods_activos);
            $ap2 = $this->getApps($id_mod);
            array_push($app_installed, ...$ap1, ...$ap2);
        }

        return array_values(array_unique($app_installed));
    }
}
