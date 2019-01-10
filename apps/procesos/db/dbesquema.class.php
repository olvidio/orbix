<?php
namespace procesos\db;
use core\ConfigGlobal;
use devel\model\DBAbstract;

/**
 * crear las tablas necesarias para el esquema.
 * Heredadas de global
 */
class DBEsquema extends DBAbstract {

    private $dir_base = ConfigGlobal::DIR."/apps/procesos/db";
    
    public function __construct($esquema_sfsv=NULL) {
        if (empty($esquema_sfsv)) {
            $esquema_sfsv = ConfigGlobal::mi_region_dl();
        }
        $this->esquema = substr($esquema_sfsv,0,-1); // quito la v o la f.
        $this->role = '"'. $this->esquema .'"';
    }
    
    public function dropAll() {
        $this->eliminar_a_actividad_proceso();
        $this->eliminar_a_tipos_proceso();
        $this->eliminar_a_procesos();
        $this->eliminar_a_fases();
        $this->eliminar_a_tareas();
        $this->eliminar_aux_usuarios_perm();
    }
    
    public function createAll() {
        $this->create_a_actividad_proceso();
        $this->create_a_tipos_procesos();
        $this->create_a_tareas();
        $this->create_a_fases();
        $this->create_a_procesos();
        $this->create_aux_usuarios_perm();
    }
    
    public function llenarAll() {
        $this->llenar_a_tipos_actividad();
        $this->llenar_a_actividad_proceso();
        $this->llenar_a_tipos_procesos();
        $this->llenar_a_tareas();
        $this->llenar_a_fases();
        $this->llenar_a_procesos();
        $this->llenar_aux_usuarios_perm();
    }
    
    private function infoTable($tabla) {
        $datosTabla = [];
        $datosTabla['tabla'] = $tabla;
        switch ($tabla) {
            case "a_tipos_actividad":
                $nom_tabla = $this->getNomTabla($tabla);
                $campo_seq = '';
                $id_seq = '';
                break;
            case "a_actividad_proceso":
                $nom_tabla = $this->getNomTabla($tabla);
                $campo_seq = 'id_item';
                $id_seq = $nom_tabla."_".$campo_seq."_seq";
                break;
            case "a_tipos_proceso":
                $nom_tabla = $this->getNomTabla($tabla);
                $campo_seq = 'id_tipo_proceso';
                $id_seq = $nom_tabla."_".$campo_seq."_seq";
                break;
            case "a_tareas":
                $nom_tabla = $this->getNomTabla($tabla);
                $campo_seq = 'id_tarea';
                $id_seq = $nom_tabla."_".$campo_seq."_seq";
                break;
            case "a_fases":
                $nom_tabla = $this->getNomTabla($tabla);
                $campo_seq = 'id_fase';
                $id_seq = $nom_tabla."_".$campo_seq."_seq";
                break;
            case "a_procesos":
                $nom_tabla = $this->getNomTabla($tabla);
                $campo_seq = 'id_item';
                $id_seq = $nom_tabla."_".$campo_seq."_seq";
                break;
            case "aux_usuarios_perm":
                $nom_tabla = $this->getNomTabla($tabla);
                $campo_seq = 'id_item';
                $id_seq = $nom_tabla."_".$campo_seq."_seq";
                break;
        }
        $datosTabla['nom_tabla'] = $nom_tabla;
        $datosTabla['campo_seq'] = $campo_seq;
        $datosTabla['id_seq'] = $id_seq;
        $datosTabla['filename'] = $this->dir_base."/$tabla.csv";
        return $datosTabla;
    }
    
    
    /**
     * En la BD Comun.
     * Tiene un foreing key con el id_activ. Entiendo que no hay problemas con sf, ya
     * los procesoso podrian ser distintos, pero no interfieren los ids.
     */
    public function create_a_actividad_proceso() {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        $tabla = "a_actividad_proceso";
        $datosTabla = $this->infoTable($tabla);
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];
        $id_seq = $datosTabla['id_seq'];
        
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                ) 
            INHERITS (global.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
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
        
        $a_sql[] = "ALTER TABLE $nom_tabla ALTER $campo_seq SET DEFAULT nextval('$id_seq'::regclass); ";
        
        $a_sql[] = "ALTER TABLE $nom_tabla ADD CONSTRAINT a_actividad_proceso_id_tipo_proceso_key
                    UNIQUE (id_tipo_proceso, id_activ, id_fase, id_tarea); ";
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY (id_item); ";
        // No va con tablas heredadas
        //$a_sql[] = "ALTER TABLE $nom_tabla ADD CONSTRAINT a_actividad_proceso_id_activ_fk
        //            FOREIGN KEY (id_activ) REFERENCES public.a_actividades_all(id_activ) ON DELETE CASCADE; ";
        
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
        
        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun');
    }
    public function eliminar_a_actividad_proceso() {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        $datosTabla = $this->infoTable("a_actividad_proceso");
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $id_seq = $datosTabla['id_seq'];
        
        $a_sql = [];
        $a_sql[0] = "DROP SEQUENCE IF EXISTS $id_seq CASCADE;" ;
        $this->executeSql($a_sql);

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('comun');
    }
    
    public function create_a_tipos_procesos() {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        $tabla = "a_tipos_proceso";
        $datosTabla = $this->infoTable($tabla);
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];
        $id_seq = $datosTabla['id_seq'];
        
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                ) 
            INHERITS (global.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";

        //secuencia
        $a_sql[] = "CREATE SEQUENCE IF NOT EXISTS $id_seq;";
        $a_sql[] = "ALTER SEQUENCE $id_seq
                    INCREMENT BY 1
                    MINVALUE 1
                    MAXVALUE 9223372036854775807
                    START WITH 1
                    NO CYCLE;";
        $a_sql[] = "ALTER SEQUENCE $id_seq OWNER TO $this->role;";
        
        $a_sql[] = "ALTER TABLE $nom_tabla ALTER $campo_seq SET DEFAULT nextval('$id_seq'::regclass); ";
        
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY (id_tipo_proceso); ";
        
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role; ";
        
        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun');
    }
    public function eliminar_a_tipos_proceso() {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        $datosTabla = $this->infoTable("a_tipos_proceso");
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $id_seq = $datosTabla['id_seq'];
        
        $a_sql = [];
        $a_sql[0] = "DROP SEQUENCE IF EXISTS $id_seq CASCADE;" ;
        $this->executeSql($a_sql);

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('comun');
    }
    
    public function create_a_tareas() {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        $tabla = "a_tareas";
        $datosTabla = $this->infoTable($tabla);
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];
        $id_seq = $datosTabla['id_seq'];
        
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                ) 
            INHERITS (global.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        //secuencia
        $a_sql[] = "CREATE SEQUENCE IF NOT EXISTS $id_seq;";
        $a_sql[] = "ALTER SEQUENCE $id_seq
                    INCREMENT BY 1
                    MINVALUE 1
                    MAXVALUE 9223372036854775807
                    START WITH 1
                    NO CYCLE;";
        $a_sql[] = "ALTER SEQUENCE $id_seq OWNER TO $this->role;";
        
        $a_sql[] = "ALTER TABLE $nom_tabla ALTER $campo_seq SET DEFAULT nextval('$id_seq'::regclass); ";
        
        $a_sql[] = "CREATE UNIQUE INDEX ${tabla}_id_tarea_key ON $nom_tabla USING btree (id_tarea); ";
            
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role; ";
        
        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun');
    }
    public function eliminar_a_tareas() {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        $datosTabla = $this->infoTable("a_tareas");
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $id_seq = $datosTabla['id_seq'];
        
        $a_sql = [];
        $a_sql[0] = "DROP SEQUENCE IF EXISTS $id_seq CASCADE;" ;
        $this->executeSql($a_sql);

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('comun');
    }

    public function create_a_fases() {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        $tabla = "a_fases";
        $datosTabla = $this->infoTable($tabla);
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];
        $id_seq = $datosTabla['id_seq'];
        
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                ) 
            INHERITS (global.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        //secuencia
        $a_sql[] = "CREATE SEQUENCE IF NOT EXISTS $id_seq;";
        $a_sql[] = "ALTER SEQUENCE $id_seq
                    INCREMENT BY 1
                    MINVALUE 1
                    MAXVALUE 9223372036854775807
                    START WITH 1
                    NO CYCLE;";
        $a_sql[] = "ALTER SEQUENCE $id_seq OWNER TO $this->role;";
        
        $a_sql[] = "ALTER TABLE $nom_tabla ALTER $campo_seq SET DEFAULT (10 * nextval('$id_seq'::regclass)); ";
        $a_sql[] = "ALTER TABLE $nom_tabla ALTER sf SET DEFAULT false; ";
        $a_sql[] = "ALTER TABLE $nom_tabla ALTER sv SET DEFAULT true; ";
        
        
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY (id_fase); ";
            
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role; ";
        
        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun');
    }
    public function eliminar_a_fases() {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        $datosTabla = $this->infoTable("a_fases");
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $id_seq = $datosTabla['id_seq'];
        
        $a_sql = [];
        $a_sql[0] = "DROP SEQUENCE IF EXISTS $id_seq CASCADE;" ;
        $this->executeSql($a_sql);

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('comun');
    }

    public function create_a_procesos() {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        $tabla = "a_procesos";
        $datosTabla = $this->infoTable($tabla);
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];
        $id_seq = $datosTabla['id_seq'];
        
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                ) 
            INHERITS (global.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        //secuencia
        $a_sql[] = "CREATE SEQUENCE IF NOT EXISTS $id_seq;";
        $a_sql[] = "ALTER SEQUENCE $id_seq
                    INCREMENT BY 1
                    MINVALUE 1
                    MAXVALUE 9223372036854775807
                    START WITH 1
                    NO CYCLE;";
        $a_sql[] = "ALTER SEQUENCE $id_seq OWNER TO $this->role;";
        
        $a_sql[] = "ALTER TABLE $nom_tabla ALTER $campo_seq SET DEFAULT nextval('$id_seq'::regclass); ";
        
        $a_sql[] = "ALTER TABLE $nom_tabla ADD CONSTRAINT a_procesos_ukey
                        UNIQUE (id_tipo_proceso, id_fase, id_tarea); ";
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY (id_item); ";
        
        $a_sql[] = "CREATE UNIQUE INDEX ${tabla}_idx ON $nom_tabla USING btree (id_tipo_proceso, id_fase, id_tarea); ";
            
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role; ";
        
        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun');
    }
    public function eliminar_a_procesos() {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        $datosTabla = $this->infoTable("a_procesos");
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $id_seq = $datosTabla['id_seq'];
        
        $a_sql = [];
        $a_sql[0] = "DROP SEQUENCE IF EXISTS $id_seq CASCADE;" ;
        $this->executeSql($a_sql);

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('comun');
    }

    public function create_aux_usuarios_perm() {
        // OJO Corresponde al esquema sf/sv, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"'. $this->esquema .'"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('svsf');
        
        $tabla = "aux_usuarios_perm";
        $datosTabla = $this->infoTable($tabla);
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];
        $id_seq = $datosTabla['id_seq'];
        
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                ) 
            INHERITS (global.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        //secuencia
        $a_sql[] = "CREATE SEQUENCE IF NOT EXISTS $id_seq;";
        $a_sql[] = "ALTER SEQUENCE $id_seq
                    INCREMENT BY 1
                    MINVALUE 1
                    MAXVALUE 9223372036854775807
                    START WITH 1
                    NO CYCLE;";
        $a_sql[] = "ALTER SEQUENCE $id_seq OWNER TO $this->role;";
        
        $a_sql[] = "ALTER TABLE $nom_tabla ALTER $campo_seq SET DEFAULT nextval('$id_seq'::regclass); ";
        
        
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY (id_item); ";
        
        $a_sql[] = "CREATE INDEX ${tabla}_id_fase_fin ON $nom_tabla USING btree (id_fase_fin); ";
        $a_sql[] = "CREATE INDEX ${tabla}_id_fase_ini ON $nom_tabla USING btree (id_fase_ini); ";
        $a_sql[] = "CREATE INDEX ${tabla}_id_usuario ON $nom_tabla USING btree (id_usuario); ";
        $a_sql[] = "CREATE INDEX ${tabla}_tipo_activ ON $nom_tabla USING btree (id_tipo_activ_txt); ";
        
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role; ";
        
        $this->executeSql($a_sql);
        
        $this->delPermisoGlobal('svsf');
        // Devolver los valores al estodo original
        $this->esquema = $esquema_org;
        $this->role = $role_org;
    }
    public function eliminar_aux_usuarios_perm() {
        // OJO Corresponde al esquema sf/sv, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"'. $this->esquema .'"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('svsf');
        
        $datosTabla = $this->infoTable("aux_usuarios_perm");
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $id_seq = $datosTabla['id_seq'];
        
        $a_sql = [];
        $a_sql[0] = "DROP SEQUENCE IF EXISTS $id_seq CASCADE;" ;
        $this->executeSql($a_sql);

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('svsf');
        // Devolver los valores al estodo original
        $this->esquema = $esquema_org;
        $this->role = $role_org;
    }

    /* ###################### LLENAR TABLAS ################################ */
    /**
     * La tabla ya existe, pero hay que e¡actualizar el tipo de proceso
     *  parar cada tipo de actividad. 
     *  Si hay algun añadido en los tipos de actividad se borrará.
     */
    public function llenar_a_tipos_actividad() {
        $this->addPermisoGlobal('comun');
        $this->setConexion('comun');
        $datosTabla = $this->infoTable("a_tipos_actividad");
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $filename = $datosTabla['filename'];
        $oDbl = $this->oDbl;
        
        $a_sql = [];
        $a_sql[0] = "TRUNCATE $nom_tabla RESTART IDENTITY;" ;
        $this->executeSql($a_sql);
        
        $delimiter = "\t"; 
        $null_as = "\\\\N";
        $fields = "id_tipo_activ, nombre, id_tipo_proceso, id_tipo_proceso_ex";
       
        $oDbl->pgsqlCopyFromFile($nom_tabla, $filename, $delimiter, $null_as, $fields);
        
    }
    public function llenar_a_actividad_proceso(){
        // empty;
    }
    public function llenar_a_tipos_procesos() {
        $this->addPermisoGlobal('comun');
        $this->setConexion('comun');
        $datosTabla = $this->infoTable("a_tipos_proceso");
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];
        $id_seq = $datosTabla['id_seq'];
        $filename = $datosTabla['filename'];
        $oDbl = $this->oDbl;
        
        $a_sql = [];
        $a_sql[0] = "TRUNCATE $nom_tabla RESTART IDENTITY;" ;
        $this->executeSql($a_sql);
        
        $delimiter = "\t"; 
        $null_as = "\\\\N";
        $fields = "id_tipo_proceso, nom_proceso";
       
        $oDbl->pgsqlCopyFromFile($nom_tabla, $filename, $delimiter, $null_as, $fields);
        // Fix sequences
        $a_sql[0] = "SELECT SETVAL('$id_seq', (SELECT MAX($campo_seq) FROM $nom_tabla) )";
        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun');
    }
    public function llenar_a_tareas() {
        $this->addPermisoGlobal('comun');
        $this->setConexion('comun');
        $datosTabla = $this->infoTable("a_tareas");
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];
        $id_seq = $datosTabla['id_seq'];
        $filename = $datosTabla['filename'];
        $oDbl = $this->oDbl;
        
        // Borrar los posibles datos de la tabla
        $a_sql = [];
        $a_sql[0] = "TRUNCATE $nom_tabla RESTART IDENTITY;" ;
        $this->executeSql($a_sql);
        
        $delimiter = "\t"; 
        $null_as = "\\\\N";
        $fields = "id_fase, id_tarea, desc_tarea";

        $oDbl->pgsqlCopyFromFile($nom_tabla, $filename, $delimiter, $null_as, $fields);
        
        // Fix sequences
        $a_sql[0] = "SELECT SETVAL('$id_seq', (SELECT MAX($campo_seq) FROM $nom_tabla) )";
        $this->executeSql($a_sql);
        
        $this->delPermisoGlobal('comun');
    }
    public function llenar_a_fases() {
        $this->addPermisoGlobal('comun');
        $this->setConexion('comun');
        $datosTabla = $this->infoTable("a_fases");
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];
        $id_seq = $datosTabla['id_seq'];
        $filename = $datosTabla['filename'];
        $oDbl = $this->oDbl;
        
        $a_sql = [];
        $a_sql[0] = "TRUNCATE $nom_tabla RESTART IDENTITY;" ;
        $this->executeSql($a_sql);
        
        $delimiter = "\t"; 
        $null_as = "\\\\N";
        $fields = "id_fase, desc_fase, sf, sv";

        $oDbl->pgsqlCopyFromFile($nom_tabla, $filename, $delimiter, $null_as, $fields);

        // Fix sequences
        $a_sql[0] = "SELECT SETVAL('$id_seq', (SELECT MAX($campo_seq)/10 FROM $nom_tabla) )";
        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun');
    }
    public function llenar_a_procesos() {
        $this->addPermisoGlobal('comun');
        $this->setConexion('comun');
        $datosTabla = $this->infoTable("a_procesos");
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];
        $id_seq = $datosTabla['id_seq'];
        $filename = $datosTabla['filename'];
        $oDbl = $this->oDbl;
        
        $a_sql = [];
        $a_sql[0] = "TRUNCATE $nom_tabla RESTART IDENTITY;" ;
        $this->executeSql($a_sql);
        
        $delimiter = "\t"; 
        $null_as = "\\\\N";
        $fields = "id_item, id_tipo_proceso, n_orden, id_fase, id_tarea, status, of_responsable, id_fase_previa, id_tarea_previa, mensaje_requisito";

        $oDbl->pgsqlCopyFromFile($nom_tabla, $filename, $delimiter, $null_as, $fields);
        
        // Fix sequences
        $a_sql[0] = "SELECT SETVAL('$id_seq', (SELECT MAX($campo_seq) FROM $nom_tabla) )";
        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun');
    }
    
    public function llenar_aux_usuarios_perm() {
        // OJO Corresponde al esquema sf/sv, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"'. $this->esquema .'"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('svsf');
        $this->setConexion('svsf');

        $datosTabla = $this->infoTable("aux_usuarios_perm");
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];
        $id_seq = $datosTabla['id_seq'];
        $filename = $datosTabla['filename'];
        $oDbl = $this->oDbl;
        
        $a_sql = [];
        $a_sql[0] = "TRUNCATE $nom_tabla RESTART IDENTITY;" ;
        $this->executeSql($a_sql);
        
        $delimiter = "\t"; 
        $null_as = "\\\\N";
        $fields = "id_item, id_usuario, id_tipo_activ_txt, id_fase_ini, id_fase_fin, accion, afecta_a, dl_propia, id_fases";
        
        $oDbl->pgsqlCopyFromFile($nom_tabla, $filename, $delimiter, $null_as, $fields);
        
        // Fix sequences
        $a_sql[0] = "SELECT SETVAL('$id_seq', (SELECT MAX($campo_seq) FROM $nom_tabla) )";
        $this->executeSql($a_sql);

        $this->delPermisoGlobal('svsf');
        // Devolver los valores al estodo original
        $this->esquema = $esquema_org;
        $this->role = $role_org;
    }

 }