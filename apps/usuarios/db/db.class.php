<?php
namespace usuarios\db;
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
        $this->eliminar_aux_usuarios_ctr_perm();
    }
    
    public function createAll() {
        $this->create_aux_usuarios_ctr_perm();
    }
    
    /**
     * En la BD sf/sv (global).
     */
    public function create_aux_usuarios_ctr_perm() {
        // OJO Corresponde al esquema sf/sv, no al comun.
        $this->addPermisoGlobal('sfsv');
        
        $tabla = "aux_usuarios_ctr_perm";
        $nom_tabla = $this->getNomTabla($tabla);
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                id_schema integer NOT NULL,
                id_item integer NOT NULL,
                id_usuario integer,
                id_ctr integer,
                perm_ctr integer
            );";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->user_orbix";
        
        $this->executeSql($a_sql);
        
        $this->delPermisoGlobal('sfsv');
    }
    public function eliminar_aux_usuarios_ctr_perm() {
        // OJO Corresponde al esquema sf/sv, no al comun.
        $this->addPermisoGlobal('sfsv');
        
        $tabla = "aux_usuarios_ctr_perm";
        $nom_tabla = $this->getNomTabla($tabla);
        $this->eliminar($nom_tabla);
        
        $this->delPermisoGlobal('sfsv');
    }
    
}