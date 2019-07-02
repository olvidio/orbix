<?php
namespace casas\db;
use core\ConfigGlobal;
use devel\model\DBAbstract;

/**
 * Crear las tablas necesaria a nivel de aplicación (global).
 * Cada esquema deberá crear las suyas, heredadas de estas.
 */
class DB extends DBAbstract {

    public function __construct(){
        $esquema_sfsv = ConfigGlobal::mi_region_dl();
        $role = substr($esquema_sfsv,0,-1); // quito la v o la f.
        
        $this->role = '"'. $role .'"';
        $this->role_vf = '"'. $esquema_sfsv .'"';
        
        $this->esquema = 'global';
    }
    
    public function dropAll() {
        $this->eliminar_da_ingresos();
        $this->eliminar_du_gastos();
        $this->eliminar_du_grupos();
    }
    
    public function createAll() {
        $this->create_da_ingresos();
        $this->create_du_gastos();
        $this->create_du_grupos();
    }
    
    /**
     * 
     * En la BD Comun (global).
     */
    public function create_da_ingresos() {
        $this->addPermisoGlobal('comun');
        

        $tabla = "da_ingresos";
        $nom_tabla = $this->getNomTabla($tabla);
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                    id_schema integer NOT NULL,
                    id_activ integer,
                    ingresos numeric(10,2), 	
                    num_asistentes smallint,
                    ingresos_previstos numeric(10,2), 	
                    num_asistentes_previstos smallint, 	
                    observ text 	
                    );";
        
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->user_orbix";
        
        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun');
    }
    public function eliminar_da_ingresos() {
        $this->addPermisoGlobal('comun');

        $tabla = "da_ingresos";
        $nom_tabla = $this->getNomTabla($tabla);
        $this->eliminar($nom_tabla);
        
        $this->delPermisoGlobal('comun');
    }
    public function create_du_gastos() {
        $this->addPermisoGlobal('comun');

        $tabla = "du_gastos";
        $nom_tabla = $this->getNomTabla($tabla);
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                    id_schema integer NOT NULL,
                    id_item integer NOT NULL,
                    id_ubi integer NOT NULL,
                    f_gasto date NOT NULL,
                    tipo smallint DEFAULT 3, 	
                    cantidad numeric(10,2) DEFAULT	0 		
                    );";
        
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->user_orbix";
        
        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun');
    }
    public function eliminar_du_gastos() {
        $this->addPermisoGlobal('comun');

        $tabla = "du_gastos";
        $nom_tabla = $this->getNomTabla($tabla);
        $this->eliminar($nom_tabla);
        
        $this->delPermisoGlobal('comun');
    }
    
    public function create_du_grupos() {
        $this->addPermisoGlobal('comun');

        $tabla = "du_grupos";
        $nom_tabla = $this->getNomTabla($tabla);
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                    id_schema integer NOT NULL,
                    id_item integer NOT NULL,
                    id_ubi_padre integer NOT NULL,
                    id_ubi_hijo integer NOT NULL,
                    UNIQUE (id_ubi_padre, id_ubi_hijo)
                    );";
        
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->user_orbix";
        
        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun');
    }
    public function eliminar_du_grupos() {
        $this->addPermisoGlobal('comun');

        $tabla = "du_grupos";
        $nom_tabla = $this->getNomTabla($tabla);
        $this->eliminar($nom_tabla);
        
        $this->delPermisoGlobal('comun');
    }
}