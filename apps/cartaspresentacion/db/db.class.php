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
        $this->create_presentacion_resto();
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
            zona text,
            observ text
            );";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->user_orbix";

        $this->executeSql($a_sql);
        
        $this->delPermisoGlobal('svsf');
    }

    public function create_presentacion_resto() {
        // OJO Corresponde al esquema sf/sv, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = 'resto'.$this->vf;
        $this->role = 'orbix'.$this->vf;
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('svsf');
        
        $tabla = "du_presentacion";
        
        $nom_tabla = $this->esquema.'.'."du_presentacion_ex";
        $nom_tabla_parent = 'public';
        if ($this->vf == 'v') {
            $nom_tabla_parent = 'publicv';
        }
        if ($this->vf == 'f') {
            $nom_tabla_parent = 'publicf';
        }
        
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                )
            INHERITS ($nom_tabla_parent.$tabla);";
        
        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY (id_ubi); ";
        
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role; ";
        
        $this->executeSql($a_sql);
        
        $this->delPermisoGlobal('svsf');
        // Devolver los valores al estado original
        $this->esquema = $esquema_org;
        $this->role = $role_org;
    }
    public function eliminar_presentacion() {
        $this->addPermisoGlobal('svsf');
        $tabla = "du_presentacion";
        $nom_tabla = $this->getNomTabla($tabla);
        
        $this->eliminar($nom_tabla);
        
        $this->delPermisoGlobal('svsf');
    }

}