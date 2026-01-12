<?php

namespace src\configuracion\domain;


use src\configuracion\domain\contracts\AppRepositoryInterface;
use src\configuracion\domain\contracts\ModuloInstaladoRepositoryInterface;
use src\configuracion\domain\contracts\ModuloRepositoryInterface;

/**
 * Description of modulosconfig
 *
 * @author Daniel Serrabou <dani@moneders.net>
 */
class ModulosConfig
{

    private $a_mods_todos = [];
    private $a_apps_todas = [];
    private $cMods;
    private $cApps;

    private $a_mods_installed = [];
    private array|false $cModsInstalados;

    public function __construct()
    {
        $MooduloRepository = $GLOBALS['container']->get(ModuloRepositoryInterface::class);
        $this->cMods = $MooduloRepository->getModulos();

        $AppRepository = $GLOBALS['container']->get(AppRepositoryInterface::class);
        $this->cApps = $AppRepository->getApps();
    }

    public function getModsAll()
    {
        foreach ($this->cMods as $oMod) {
            $id_mod = $oMod->getId_mod();
            $nom_mod = $oMod->getNomVo()->value();
            $mods_req = $oMod->getMods_req();
            $apps_req = $oMod->getApps_req();
            $this->a_mods_todos[$id_mod] = array('nom' => $nom_mod, 'mods_req' => $mods_req, 'apps_req' => $apps_req);
        }
        return $this->a_mods_todos;
    }

    public function getAppsAll()
    {
        foreach ($this->cApps as $oApp) {
            $id_app = $oApp->getIdAppVo()->value();
            $nom_app = $oApp->getNombreAppVo()->value();
            $this->a_apps_todas[$id_app] = $nom_app;
        }
        return $this->a_apps_todas;
    }

    // APLICACIONES INSTALADAS EN LA DL
    public function getModsInstalados()
    {
        $ModuloInstaladoRepository = $GLOBALS['container']->get(ModuloInstaladoRepositoryInterface::class);
        $this->cModsInstalados = $ModuloInstaladoRepository->getModulosInstalados();
        foreach ($this->cModsInstalados as $oMod) {
            $id_mod = $oMod->getId_mod();
            $nom_mod = $oMod->getNomVo()->value();
            $this->a_mods_installed[$id_mod] = $nom_mod;
        }
        return $this->a_mods_installed;
    }

    public function getAppsMods($id_mod)
    {
        $apps = [];
        if (empty($id_mod)) {
            return $apps;
        }
        $a_mods = $this->getModsAll();
        $mods_req = $a_mods[$id_mod]['mods_req']?? [];
        $all = [];
        foreach ($mods_req as $mod) {
            $all[] = $this->getApps($mod);
        }
        $apps = array_merge(...$all);

        $apps_propias = $this->getApps($id_mod);
        $apps = array_merge($apps, $apps_propias);

        $apps = array_unique($apps);

        return $apps;
    }

    public function getApps($id_mod)
    {
        $apps = [];
        if (empty($id_mod)) {
            return $apps;
        }
        $a_mods = $this->getModsAll();
        return $a_mods[$id_mod]['apps_req'];
    }

    public function getAppsInstaladas()
    {
        //$a_apps = $this->getAppsAll();
        $app_installed = [];

        $a_mods_installed = $this->getModsInstalados();
        foreach ($a_mods_installed as $id_mod => $nom_mod) {
            $ap1 = $this->getAppsMods($id_mod);
            $ap2 = $this->getApps($id_mod);
            array_push($app_installed, ...$ap1, ...$ap2);
        }
        $app_installed = array_unique($app_installed);
        return $app_installed;
    }

}