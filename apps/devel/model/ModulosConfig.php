<?php

namespace devel\model;

use devel\model\entity\GestorApp;
use devel\model\entity\GestorModulo;
use permisos\model\entity\GestorModuloInstalado;
use function permisos\controller\getApps;
use function permisos\controller\getModsInstalados;

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
        $GesModulos = new GestorModulo();
        $this->cMods = $GesModulos->getModulos();

        $gesApps = new GestorApp();
        $this->cApps = $gesApps->getApps();
    }


    public function getModsAll()
    {
        foreach ($this->cMods as $oMod) {
            $id_mod = $oMod->getId_mod();
            $nom_mod = $oMod->getNom();
            $mods_req = $oMod->getMods_req();
            $apps_req = $oMod->getApps_req();
            $this->a_mods_todos[$id_mod] = array('nom' => $nom_mod, 'mods_req' => $mods_req, 'apps_req' => $apps_req);
        }
        return $this->a_mods_todos;
    }

    public function getAppsAll()
    {
        foreach ($this->cApps as $oApp) {
            $id_app = $oApp->getId_app();
            $nom_app = $oApp->getNom();
            $this->a_apps_todas[$id_app] = $nom_app;
        }
        return $this->a_apps_todas;
    }


    // APLICACIONES INSTALADAS EN LA DL
    public function getModsInstalados()
    {
        $GesModulosInstalados = new GestorModuloInstalado();
        $this->cModsInstalados = $GesModulosInstalados->getModulosInstalados();
        foreach ($this->cModsInstalados as $oMod) {
            $id_mod = $oMod->getId_mod();
            $nom_mod = $oMod->getNom();
            $this->a_mods_installed[$id_mod] = $nom_mod;
        }
        return $this->a_mods_installed;
    }

    public function getAppsMods($id_mod)
    {
        $apps = array();
        if (empty($id_mod)) {
            return $apps;
        }
        $a_mods = $this->getModsAll();
        $ajson = $a_mods[$id_mod]['mods_req'];
        $matches = [];
        if (preg_match('/^{(.*)}$/', $ajson, $matches)) {
            $mod_in = str_getcsv($matches[1]);
            foreach ($mod_in as $mod) {
                $appsi = $this->getApps($mod);
                $apps = array_merge($apps, $appsi);
            }
        }
        $apps_popias = $this->getApps($id_mod);
        $apps = array_merge($apps, $apps_popias);

        return $apps;
    }

    public function getApps($id_mod)
    {
        $apps = array();
        if (empty($id_mod)) {
            return $apps;
        }
        $a_mods = $this->getModsAll();
        $ajson = $a_mods[$id_mod]['apps_req'];
        $matches = [];
        if (preg_match('/^{(.*)}$/', $ajson, $matches)) {
            $app_in = str_getcsv($matches[1]);
            foreach ($app_in as $app) {
                array_push($apps, $app);
            }
        }
        return $apps;
    }

    public function getAppsInstaladas()
    {
        //$a_apps = $this->getAppsAll();
        $app_installed = [];

        $a_mods_installed = getModsInstalados();
        foreach ($a_mods_installed as $id_mod => $nom_mod) {
            $ap1 = $this->getAppsMods($id_mod);
            $ap2 = $this->getApps($id_mod);
            $app_installed = array_merge($app_installed, $ap1, $ap2);
            $app_installed = array_unique($app_installed);
        }
        return $app_installed;
    }


}