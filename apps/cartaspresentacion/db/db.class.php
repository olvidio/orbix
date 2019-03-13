<?php
namespace cartaspresentacion\db;
use core\ConfigGlobal;
use devel\model\DBAbstract;

/**
 * Crear las tablas necesaria a nivel de aplicación (global).
 * En este caso public(v/f) porque se debe tener acceso de consulta.
 * Cada esquema deberá crear las suyas, heredadas de estas.
 */
class DB extends DBAbstract {

    public function __construct(){
        $esquema_sfsv = ConfigGlobal::mi_region_dl();
        $role = substr($esquema_sfsv,0,-1); // quito la v o la f.
        $this->vf = substr($esquema_sfsv,-1); // solo la v o la f.
        
        $this->role = '"'. $role .'"';
        $this->role_vf = '"'. $esquema_sfsv .'"';
        
        $this->esquema = 'public';
    }
    
    public function dropAll() {
        $this->eliminar_presentacion();
    }
    
    public function createAll() {
        $this->create_presentacion();
    }
    
    /**
     * En el esquema sv
     *  // OJO Corresponde al esquema sf/sv, no al comun.
     *  // OJO Corresponde al esquema public, no al global.
     */
    
    public function create_presentacion() {
        $this->addPermisoGlobal('svsf');
        $tabla = "du_presentacion";
        $nom_tabla = $this->getNomTabla($tabla);
        $a_sql = [];
        
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
            id_schema integer NOT NULL,
            id_ubi integer NOT NULL,
            pres_nom text,
            pres_telf text,
            pres_mail text,
            zona text
            );";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->user_orbix";

        $this->executeSql($a_sql);
        
        $this->delPermisoGlobal('svsf');
    }
    public function eliminar_presentacion() {
        $this->addPermisoGlobal('svsf');
        $tabla = "du_presentacion";
        $nom_tabla = $this->getNomTabla($tabla);
        
        $this->eliminar($nom_tabla);
        
        $this->delPermisoGlobal('svsf');
    }

}