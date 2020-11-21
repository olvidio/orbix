<?php
namespace cambios\db;
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
        $this->eliminar_av_cambios_anotados();
        $this->eliminar_av_cambios_usuario_propiedades_pref();
        $this->eliminar_av_cambios_usuario_objeto_pref();
        $this->eliminar_av_cambios_usuario();
        $this->eliminar_av_cambios();
    }
    
    public function createAll() {
        $this->create_av_cambios();
        $this->create_av_cambios_usuario();
        $this->create_av_cambios_usuario_objeto_pref();
        $this->create_av_cambios_usuario_propiedades_pref();
        $this->create_av_cambios_anotados();
    }
    
    /**
     * En la BD Comun (public).
     * OJO Corresponde al esquema public, no al global.
     */
    public function create_av_cambios() {
        $esquema_org = $this->esquema;
        $this->esquema = 'public';
        
        $this->addPermisoGlobal('comun');
        
        $tabla = "av_cambios";
        $nom_tabla = $this->getNomTabla($tabla);
        $campo_seq = 'id_item_cambio';
        $id_seq = $nom_tabla."_".$campo_seq."_seq";
        
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                id_schema integer NOT NULL,
                id_item_cambio integer NOT NULL,
                id_tipo_cambio integer NOT NULL,
                id_activ bigint NOT NULL,
                id_tipo_activ integer NOT NULL,
                json_fases_sv json,
                json_fases_sf json,
                id_status integer,
                dl_org text,
                objeto text,
                propiedad text,
                valor_old text,
                valor_new text,
                quien_cambia integer,
                sfsv_quien_cambia integer,
                timestamp_cambio timestamp without time zone
                ); ";
        
       //secuencia (para los esquemas que no tengan tabla propia: Los que no tienen instalado el módulo)
        $a_sql[] = "CREATE SEQUENCE IF NOT EXISTS $id_seq;";
        $a_sql[] = "ALTER SEQUENCE $id_seq
                    INCREMENT BY 1
                    MINVALUE 1
                    MAXVALUE 9223372036854775807
                    START WITH 1
                    NO CYCLE;";
        $a_sql[] = "ALTER SEQUENCE $id_seq OWNER TO $this->user_orbix;";
        
        $a_sql[] = "ALTER TABLE $nom_tabla ALTER $campo_seq SET DEFAULT nextval('$id_seq'::regclass); ";
        
        $a_sql[] = "CREATE INDEX ${tabla}_${campo_seq}_idx ON $nom_tabla USING btree ($campo_seq); ";
        $a_sql[] = "CREATE INDEX ${tabla}_dl_org_idx ON $nom_tabla USING btree (dl_org); ";
        
        $a_sql[] = "CREATE UNIQUE INDEX ${tabla}_udx ON $nom_tabla USING btree (id_schema,id_item_cambio); ";
        
        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT 3000"; 
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->user_orbix";
        // Aseguarme que todos pueden leer:
        $a_sql[] = "GRANT SELECT,DELETE ON $nom_tabla TO PUBLIC; ";
        $a_sql[] = "GRANT SELECT,UPDATE ON $id_seq TO PUBLIC; ";
        
        $this->executeSql($a_sql);
        
        $this->delPermisoGlobal('comun');
        // Devolver los valores al estado original
        $this->esquema = $esquema_org;
    }
    public function eliminar_av_cambios() {
        $esquema_org = $this->esquema;
        $this->esquema = 'public';
        
        $this->addPermisoGlobal('comun');
        
        $tabla = "av_cambios";
        $nom_tabla = $this->getNomTabla($tabla);
        $this->eliminar($nom_tabla);
        
        $this->delPermisoGlobal('comun');
        // Devolver los valores al estado original
        $this->esquema = $esquema_org;
    }
    
    /**
     * En la BD comun (global).
     */
    public function create_av_cambios_anotados() {
        $this->addPermisoGlobal('comun');
        
        $tabla = "av_cambios_anotados";
        $nom_tabla = $this->getNomTabla($tabla);
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                id_schema integer NOT NULL,
                id_item integer NOT NULL,
                id_schema_cambio integer NOT NULL,
                id_item_cambio integer NOT NULL,
                anotado boolean,
                server integer NOT NULL
                ); ";
        
        $a_sql[] = "ALTER TABLE $nom_tabla ALTER anotado SET DEFAULT false;";
        $a_sql[] = "ALTER TABLE $nom_tabla ALTER server SET DEFAULT 1;";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->user_orbix";
        
        $this->executeSql($a_sql);
        
        $this->delPermisoGlobal('comun');
    }
    public function eliminar_av_cambios_anotados() {
        $this->addPermisoGlobal('comun');
        
        $tabla = "av_cambios_anotados";
        $nom_tabla = $this->getNomTabla($tabla);
        $this->eliminar($nom_tabla);
        
        $this->delPermisoGlobal('comun');
    }
    
    /**
     * En la BD comun (global). 
     * Correspondería a sfsv, pero para poder borrar con 'LEFT JOIN' 
     * cuando se eliminan los av_cambios, la pongo en comun.
     */
    public function create_av_cambios_usuario() {
        $this->addPermisoGlobal('comun');
        
        $tabla = "av_cambios_usuario";
        $nom_tabla = $this->getNomTabla($tabla);
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                id_schema integer NOT NULL,
                id_item integer NOT NULL,
                id_schema_cambio integer NOT NULL,
                id_item_cambio integer NOT NULL,
                id_usuario integer NOT NULL,
                sfsv smallint NOT NULL,
                aviso_tipo integer NOT NULL,
                avisado boolean
                ); ";
        
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->user_orbix";
        
        $this->executeSql($a_sql);
        
        $this->delPermisoGlobal('comun');
    }
    public function eliminar_av_cambios_usuario() {
        $this->addPermisoGlobal('comun');
        
        $tabla = "av_cambios_usuario";
        $nom_tabla = $this->getNomTabla($tabla);
        $this->eliminar($nom_tabla);
        
        $this->delPermisoGlobal('comun');
    }

    /**
     * En la BD sv/sf (global).
     */
    public function create_av_cambios_usuario_objeto_pref() {
        $this->addPermisoGlobal('sfsv-e');
        
        $tabla = "av_cambios_usuario_objeto_pref";
        $nom_tabla = $this->getNomTabla($tabla);
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                id_schema integer NOT NULL,
                id_item_usuario_objeto integer NOT NULL,
                id_usuario integer NOT NULL,
                dl_org text NOT NULL,
                id_tipo_activ_txt character varying(6) NOT NULL,
                id_fase_ref integer NOT NULL,
                aviso_off boolean NOT NULL DEFAULT FALSE,
                aviso_on boolean NOT NULL DEFAULT TRUE,
                aviso_outdate boolean NOT NULL DEFAULT FALSE,
                objeto text NOT NULL,
                aviso_tipo integer NOT NULL,
                id_pau text
                ); ";
        
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->user_orbix";
        
        $this->executeSql($a_sql);
        
        $this->delPermisoGlobal('sfsv-e');
    }
    public function eliminar_av_cambios_usuario_objeto_pref() {
        $this->addPermisoGlobal('sfsv-e');
        
        $tabla = "av_cambios_usuario_objeto_pref";
        $nom_tabla = $this->getNomTabla($tabla);
        $this->eliminar($nom_tabla);
        
        $this->delPermisoGlobal('sfsv-e');
    }
    
    public function create_av_cambios_usuario_propiedades_pref() {
        $this->addPermisoGlobal('sfsv-e');
        
        $tabla = "av_cambios_usuario_propiedades_pref";
        $nom_tabla = $this->getNomTabla($tabla);
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                id_schema integer NOT NULL,
                id_item integer NOT NULL,
                id_item_usuario_objeto integer NOT NULL,
                propiedad text NOT NULL,
                operador text,
                valor text,
                valor_old boolean, 	
                valor_new boolean
                ); ";
        
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->user_orbix";
        
        $this->executeSql($a_sql);
        
        $this->delPermisoGlobal('sfsv-e');
    }
    public function eliminar_av_cambios_usuario_propiedades_pref() {
        $this->addPermisoGlobal('sfsv-e');
        
        $tabla = "av_cambios_usuario_propiedades_pref";
        $nom_tabla = $this->getNomTabla($tabla);
        $this->eliminar($nom_tabla);
        
        $this->delPermisoGlobal('sfsv-e');
    }
}
    