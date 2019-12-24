<?php
namespace procesos\db;
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
        $this->eliminar_a_actividad_proceso();
        $this->eliminar_a_tipos_proceso();
        $this->eliminar_a_tareas_proceso();
        $this->eliminar_a_fases();
        $this->eliminar_a_tareas();
        $this->eliminar_aux_usuarios_perm();
    }
    
    public function createAll() {
        $this->create_a_actividad_proceso();
        $this->create_a_tipos_procesos();
        $this->create_a_tareas();
        $this->create_a_fases();
        $this->create_a_tareas_proceso();
        $this->create_aux_usuarios_perm();
    }
    
    /**
     * En la BD Comun (global).
     * Tiene un foreing key con el id_activ. Entiendo que no hay problemas con sf, ya 
     * los procesos podrian ser distintos, pero no interfieren los ids.
     */
    public function create_a_actividad_proceso() {
        $this->addPermisoGlobal('comun');

        $tabla = "a_actividad_proceso";
        $nom_tabla = $this->getNomTabla($tabla);
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                id_schema integer NOT NULL,
                id_item integer NOT NULL,
                id_tipo_proceso integer NOT NULL,
                id_activ bigint NOT NULL,
                id_fase integer,
                id_tarea integer,
                n_orden smallint,
                completado boolean,
                observ text
                ); ";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER completado SET DEFAULT false;";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->user_orbix";
        
        $this->executeSql($a_sql);
        
        $this->delPermisoGlobal('comun');
    }
    public function eliminar_a_actividad_proceso() {
        $this->addPermisoGlobal('comun');

        $tabla = "a_actividad_proceso";
        $nom_tabla = $this->getNomTabla($tabla);
        $this->eliminar($nom_tabla);
        
        $this->delPermisoGlobal('comun');
    }
    

    public function create_a_tipos_procesos() {
        $this->addPermisoGlobal('comun');

        $tabla = "a_tipos_proceso";
        $nom_tabla = $this->getNomTabla($tabla);
        $a_sql = [];

        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
            id_schema integer NOT NULL,
            id_tipo_proceso integer NOT NULL,
            nom_proceso text,
            sfsv integer
            );";
        
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->user_orbix";
        
        $this->executeSql($a_sql);
        
        $this->delPermisoGlobal('comun');
    }
    public function eliminar_a_tipos_proceso() {
        $this->addPermisoGlobal('comun');

        $tabla = "a_tipos_proceso";
        $nom_tabla = $this->getNomTabla($tabla);
        $this->eliminar($nom_tabla);
        
        $this->delPermisoGlobal('comun');
    }
    
    public function create_a_tareas() {
        $this->addPermisoGlobal('comun');

        $tabla = "a_tareas";
        $nom_tabla = $this->getNomTabla($tabla);
        $a_sql = [];

        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                    id_schema integer NOT NULL,
                    id_fase integer NOT NULL,
                    id_tarea integer NOT NULL,
                    desc_tarea character varying(70)
                    );";
        
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->user_orbix";
        
        $this->executeSql($a_sql);
        
        $this->delPermisoGlobal('comun');
    }
    public function eliminar_a_tareas() {
        $this->addPermisoGlobal('comun');

        $tabla = "a_tareas";
        $nom_tabla = $this->getNomTabla($tabla);
        $this->eliminar($nom_tabla);
        
        $this->delPermisoGlobal('comun');
    }
    
    public function create_a_fases() {
        $this->addPermisoGlobal('comun');

        $tabla = "a_fases";
        $nom_tabla = $this->getNomTabla($tabla);
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                    id_schema integer NOT NULL,
                    id_fase integer NOT NULL,
                    desc_fase text,
                    sf boolean NOT NULL,
                    sv boolean NOT NULL
                    );";
        
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->user_orbix";
        
        $this->executeSql($a_sql);
        
        $this->delPermisoGlobal('comun');
    }
    public function eliminar_a_fases() {
        $this->addPermisoGlobal('comun');

        $tabla = "a_fases";
        $nom_tabla = $this->getNomTabla($tabla);
        $this->eliminar($nom_tabla);
        
        $this->delPermisoGlobal('comun');
    }
    
    public function create_a_tareas_proceso() {
        $this->addPermisoGlobal('comun');

        $tabla = "a_tareas_proceso";
        $nom_tabla = $this->getNomTabla($tabla);
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                    id_schema integer NOT NULL,
                    id_item integer NOT NULL,
                    id_tipo_proceso integer NOT NULL,
                    n_orden smallint,
                    id_fase integer NOT NULL,
                    id_tarea integer NOT NULL,
                    status smallint NOT NULL,
                    of_responsable character varying(7),
                    id_fase_previa integer,
                    id_tarea_previa integer,
                    mensaje_requisito text
                    );";
        
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->user_orbix";
        
        $this->executeSql($a_sql);
        
        $this->delPermisoGlobal('comun');
    }
    public function eliminar_a_tareas_proceso() {
        $this->addPermisoGlobal('comun');

        $tabla = "a_tareas_proceso";
        $nom_tabla = $this->getNomTabla($tabla);
        $this->eliminar($nom_tabla);
        
        $this->delPermisoGlobal('comun');
    }
    
    /**
     * En la BD sf-e/sv-e [exterior] (global).
     */
    public function create_aux_usuarios_perm() {
        // OJO Corresponde al esquema sf/sv, no al comun.
        $this->addPermisoGlobal('sfsv-e');
        
        $tabla = "aux_usuarios_perm";
        $nom_tabla = $this->getNomTabla($tabla);
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                id_schema integer NOT NULL,
                id_item integer NOT NULL,
                id_usuario integer,
                id_tipo_activ_txt character varying(6),
                id_fase_ini integer,
                id_fase_fin integer,
                accion integer,
                afecta_a integer,
                dl_propia boolean DEFAULT true NOT NULL,
                id_fases text
            );";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->user_orbix";
        
        $this->executeSql($a_sql);
        
        $this->delPermisoGlobal('sfsv-e');
    }
    public function eliminar_aux_usuarios_perm() {
        // OJO Corresponde al esquema sf-e/sv-e, no al comun.
        $this->addPermisoGlobal('sfsv-e');
        
        $tabla = "aux_usuarios_perm";
        $nom_tabla = $this->getNomTabla($tabla);
        $this->eliminar($nom_tabla);
        
        $this->delPermisoGlobal('sfsv-e');
    }
    
}