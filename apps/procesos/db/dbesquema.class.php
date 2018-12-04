<?php
namespace procesos\db;
/**
 * crear las tablas necesarias para el esquiem.
 * Heredadas de global
 */
class DBEsquema {

    private $esquema;
    private $role;
    private $oDbl;
    
    public function __construct($esquema) {
        $this->esquema = $esquema;
        $this->role = '"'. $esquema .'"';
        $this->oDbl = $GLOBALS['oDBC'];
    }
    
    public function dropAll() {
        $this->eliminar_a_actividad_proceso();
        $this->eliminar_a_tipos_proceso();
        $this->eliminar_a_procesos();
        $this->eliminar_a_fases();
        $this->eliminar_a_tareas();
    }
    
    public function createAll() {
        $this->create_a_actividad_proceso();
        $this->create_a_tipos_procesos();
        $this->create_a_tareas();
        $this->create_a_fases();
        $this->create_a_procesos();
    }
    
    private function getNomTabla($tabla) {
        $nom_tabla = '"'.$this->esquema.'".'.$tabla;
        return $nom_tabla;
    }
    private function executeSql($a_sql) {
        $oDbl = $this->oDbl;
        
        $oDbl->beginTransaction();
        foreach ($a_sql as $sql) {
            if ($oDbl->exec($sql) === false) {
                $sClauError = 'Procesos.DBEsquema.query';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                $oDbl->rollback();
                return FALSE;
            }
        }
        $oDbl->commit();
    }
    private function eliminar($tabla) {
        $nom_tabla = $this->getNomTabla($tabla);
        
        $a_sql = [];
        $a_sql[0] = "DROP TABLE IF EXISTS $nom_tabla CASCADE;" ;
        
        return $this->executeSql($a_sql);
    }
    
    /**
     * En la BD Comun.
     * Tiene un foreing key con el id_activ. Entiendo que no hay problemas con sf, ya
     * los procesoso podrian ser distintos, pero no interfieren los ids.
     */
    public function create_a_actividad_proceso() {
        $tabla = "a_actividad_proceso";
        $nom_tabla = $this->getNomTabla($tabla);
        $id_seq = $nom_tabla."_id_item_seq";
    
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                ) 
            INHERITS (global.a_actividad_proceso);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER completado SET DEFAULT false;";
        
        //secuencia
        $a_sql[] = "CREATE SEQUENCE IF NOT EXISTS $id_seq;";
        $a_sql[] = "ALTER SEQUENCE $id_seq
                    INCREMENT BY 1
                    MINVALUE 1
                    MAXVALUE 9223372036854775807
                    START WITH 1
                    NO CYCLE;";
        $a_sql[] = "ALTER SEQUENCE $id_seq OWNER TO $this->role;";
        
        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_item SET DEFAULT nextval('$id_seq'::regclass); ";
        
        $a_sql[] = "ALTER TABLE $nom_tabla ADD CONSTRAINT a_actividad_proceso_id_tipo_proceso_key
                    UNIQUE (id_tipo_proceso, id_activ, id_fase, id_tarea); ";
        $a_sql[] = "ALTER TABLE $nom_tabla ADD CONSTRAINT a_actividad_proceso_pkey
                    PRIMARY KEY (id_item); ";
        $a_sql[] = "ALTER TABLE $nom_tabla ADD CONSTRAINT a_actividad_proceso_id_activ_fk
                    FOREIGN KEY (id_activ) REFERENCES public.a_actividades_all(id_activ) ON DELETE CASCADE; ";
        
        $a_sql[] = "CREATE INDEX a_actividad_proceso_n_orden ON $nom_tabla USING btree (n_orden); ";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";
        
        /*
        $a_sql[] = "GRANT DELETE ON $nom_tabla TO $this->role WITH GRANT OPTION; ";
        $a_sql[] = "GRANT INSERT ON $nom_tabla TO $this->role WITH GRANT OPTION; ";
        $a_sql[] = "GRANT REFERENCES ON $nom_tabla TO $this->role WITH GRANT OPTION; ";
        $a_sql[] = "GRANT SELECT ON $nom_tabla TO $this->role WITH GRANT OPTION; ";
        $a_sql[] = "GRANT TRIGGER ON $nom_tabla TO $this->role WITH GRANT OPTION; ";
        $a_sql[] = "GRANT TRUNCATE ON $nom_tabla TO $this->role WITH GRANT OPTION; ";
        $a_sql[] = "GRANT UPDATE ON $nom_tabla TO $this->role WITH GRANT OPTION; ";
        */
        
        return $this->executeSql($a_sql);
    }
    public function eliminar_a_actividad_proceso() {
        $tabla = "a_actividad_proceso";
        $nom_tabla = $this->getNomTabla($tabla);
        $id_seq = $nom_tabla."_id_item_seq";
        
        $a_sql = [];
        $a_sql[0] = "DROP SEQUENCE IF EXISTS $id_seq CASCADE;" ;
        $this->executeSql($a_sql);

        return $this->eliminar($tabla);
    }
    
    public function create_a_tipos_procesos() {
        $tabla = "a_tipos_proceso";
        $nom_tabla = $this->getNomTabla($tabla);
        $id_seq = $nom_tabla."_id_tipo_proceso_seq";
        
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                ) 
            INHERITS (global.$tabla);";

        //secuencia
        $a_sql[] = "CREATE SEQUENCE IF NOT EXISTS $id_seq;";
        $a_sql[] = "ALTER SEQUENCE $id_seq
                    INCREMENT BY 1
                    MINVALUE 1
                    MAXVALUE 9223372036854775807
                    START WITH 1
                    NO CYCLE;";
        $a_sql[] = "ALTER SEQUENCE $id_seq OWNER TO $this->role;";
        
        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_tipo_proceso SET DEFAULT nextval('$id_seq'::regclass); ";
        
        $a_sql[] = "ALTER TABLE $nom_tabla ADD CONSTRAINT a_tipos_procesos_pkey
                        PRIMARY KEY (id_tipo_proceso); ";
        
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role; ";
        
        return $this->executeSql($a_sql);
    }
    public function eliminar_a_tipos_proceso() {
        $tabla = "a_tipos_proceso";
        $nom_tabla = $this->getNomTabla($tabla);
        $id_seq = $nom_tabla."_id_tipo_proceso_seq";
        
        $a_sql = [];
        $a_sql[0] = "DROP SEQUENCE IF EXISTS $id_seq CASCADE;" ;
        $this->executeSql($a_sql);

        return $this->eliminar($tabla);
    }
    
    public function create_a_tareas() {
        $tabla = "a_tareas";
        $nom_tabla = $this->getNomTabla($tabla);
        $id_seq = $nom_tabla."_id_tarea_seq";
        
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                ) 
            INHERITS (global.$tabla);";

        //secuencia
        $a_sql[] = "CREATE SEQUENCE IF NOT EXISTS $id_seq;";
        $a_sql[] = "ALTER SEQUENCE $id_seq
                    INCREMENT BY 1
                    MINVALUE 1
                    MAXVALUE 9223372036854775807
                    START WITH 1
                    NO CYCLE;";
        $a_sql[] = "ALTER SEQUENCE $id_seq OWNER TO $this->role;";
        
        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_tarea SET DEFAULT nextval('$id_seq'::regclass); ";
        
        $a_sql[] = "CREATE UNIQUE INDEX ${tabla}_id_tarea_key ON $nom_tabla USING btree (id_tarea); ";
            
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role; ";
        
        return $this->executeSql($a_sql);
    }
    public function eliminar_a_tareas() {
        $tabla = "a_tareas";
        $nom_tabla = $this->getNomTabla($tabla);
        $id_seq = $nom_tabla."_id_tarea_seq";
        
        $a_sql = [];
        $a_sql[0] = "DROP SEQUENCE IF EXISTS $id_seq CASCADE;" ;
        $this->executeSql($a_sql);

        return $this->eliminar($tabla);
    }

    public function create_a_fases() {
        $tabla = "a_fases";
        $nom_tabla = $this->getNomTabla($tabla);
        $id_seq = $nom_tabla."_id_fase_seq";
        
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                ) 
            INHERITS (global.$tabla);";

        //secuencia
        $a_sql[] = "CREATE SEQUENCE IF NOT EXISTS $id_seq;";
        $a_sql[] = "ALTER SEQUENCE $id_seq
                    INCREMENT BY 1
                    MINVALUE 1
                    MAXVALUE 9223372036854775807
                    START WITH 1
                    NO CYCLE;";
        $a_sql[] = "ALTER SEQUENCE $id_seq OWNER TO $this->role;";
        
        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_fase SET DEFAULT (10 * nextval('$id_seq'::regclass)); ";
        $a_sql[] = "ALTER TABLE $nom_tabla ALTER sf SET DEFAULT false; ";
        $a_sql[] = "ALTER TABLE $nom_tabla ALTER sv SET DEFAULT true; ";
        
        
        $a_sql[] = "ALTER TABLE $nom_tabla ADD CONSTRAINT a_fases_pkey
                        PRIMARY KEY (id_fase); ";
            
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role; ";
        
        return $this->executeSql($a_sql);
    }
    public function eliminar_a_fases() {
        $tabla = "a_fases";
        $nom_tabla = $this->getNomTabla($tabla);
        $id_seq = $nom_tabla."_id_fase_seq";
        
        $a_sql = [];
        $a_sql[0] = "DROP SEQUENCE IF EXISTS $id_seq CASCADE;" ;
        $this->executeSql($a_sql);

        return $this->eliminar($tabla);
    }

    public function create_a_procesos() {
        $tabla = "a_procesos";
        $nom_tabla = $this->getNomTabla($tabla);
        $id_seq = $nom_tabla."_id_item_seq";
        
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                ) 
            INHERITS (global.$tabla);";

        //secuencia
        $a_sql[] = "CREATE SEQUENCE IF NOT EXISTS $id_seq;";
        $a_sql[] = "ALTER SEQUENCE $id_seq
                    INCREMENT BY 1
                    MINVALUE 1
                    MAXVALUE 9223372036854775807
                    START WITH 1
                    NO CYCLE;";
        $a_sql[] = "ALTER SEQUENCE $id_seq OWNER TO $this->role;";
        
        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_item SET DEFAULT nextval('$id_seq'::regclass); ";
        
        $a_sql[] = "ALTER TABLE $nom_tabla ADD CONSTRAINT a_procesos_ukey
                        UNIQUE (id_tipo_proceso, id_fase, id_tarea); ";
        $a_sql[] = "ALTER TABLE $nom_tabla ADD CONSTRAINT a_procesos_pkey
                        PRIMARY KEY (id_item); ";
        
        $a_sql[] = "CREATE UNIQUE INDEX ${tabla}_idx ON $nom_tabla USING btree (id_tipo_proceso, id_fase, id_tarea); ";
            
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role; ";
        
        return $this->executeSql($a_sql);
    }
    public function eliminar_a_procesos() {
        $tabla = "a_procesos";
        $nom_tabla = $this->getNomTabla($tabla);
        $id_seq = $nom_tabla."_id_item_seq";
        
        $a_sql = [];
        $a_sql[0] = "DROP SEQUENCE IF EXISTS $id_seq CASCADE;" ;
        $this->executeSql($a_sql);

        return $this->eliminar($tabla);
    }

}