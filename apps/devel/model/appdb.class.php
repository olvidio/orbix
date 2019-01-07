<?php
namespace devel\model;

class appDB {
 
    private $id_mod;
    private $nom_mod;
    private $oModuloConfig;
    
    public function __construct($id_mod) {
        $this->id_mod = $id_mod;
        $oModulo = new \devel\model\entity\Modulo($id_mod);
        $this->nom_mod = $oModulo->getNom();
        
        $this->oModuloConfig = new modulosConfig();
    }
    
    /*
     * Genera las tablas del esquema correspondiente
     */
    public function createTables() {
        $a_todasApps = $_SESSION['config']['a_apps'];
        // buscar para cada app requerida.
        $a_apps = $this->oModuloConfig->getApps($this->id_mod);
        foreach ($a_apps as $id_app) {
            $nom_app = array_search($id_app, $a_todasApps); 
            $this->createTablesApp($nom_app);
        }
        
    }
    
    private  function createTablesApp($nom_app) {
        $clase_esquema = "$nom_app\\db\\DBEsquema";
        if (class_exists($clase_esquema)) {
            $ClaseEsquema = new $clase_esquema();
            $ClaseEsquema->createAll();
            $ClaseEsquema->llenarAll();
        }
    }
    
    
    
}