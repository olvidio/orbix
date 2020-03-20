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
        $this->role_vf = '"'. $esquema_sfsv .'"';
    }
    
    public function dropAll() {
        $this->eliminar_a_actividad_proceso('sv');
        $this->eliminar_a_actividad_proceso('sf');
        $this->eliminar_a_tipos_proceso();
        $this->eliminar_a_tareas_proceso();
        $this->eliminar_a_fases();
        $this->eliminar_a_tareas();
        $this->eliminar_aux_usuarios_perm();
    }
    
    public function createAll() {
        $this->create_a_tareas(); // antes que 'actividad_proceso' por el FOREIGN KEY
        $this->create_a_fases(); // antes que 'actividad_proceso' por el FOREIGN KEY
        $this->create_a_actividad_proceso('sv');
        $this->create_a_actividad_proceso('sf');
        $this->create_a_tipos_procesos();
        $this->create_a_tareas_proceso();
        $this->create_aux_usuarios_perm();
    }
    
    public function llenarAll() {
        /*
        $this->llenar_a_tareas();
        $this->llenar_a_fases();
        $this->llenar_a_tipos_actividad();
        $this->llenar_a_tipos_procesos();
        $this->llenar_a_tareas_proceso();
        $this->llenar_a_actividad_proceso();
        $this->llenar_aux_usuarios_perm();
        */
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
            case "a_actividad_proceso_sv":
            case "a_actividad_proceso_sf":
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
            case "a_tareas_proceso":
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
     * En la BD Comun (esquema).
     * Tiene un foreing key con el id_activ. Entiendo que no hay problemas con sf, ya
     * los procesoso podrian ser distintos, pero no interfieren los ids.
     */
    public function create_a_actividad_proceso($seccion) {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        $tabla_padre = "a_actividad_proceso";
        if ($seccion == 'sv') {
            $tabla = "a_actividad_proceso_sv";
        }
        if ($seccion == 'sf') {
            $tabla = "a_actividad_proceso_sf";
        }
        $datosTabla = $this->infoTable($tabla);
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];
        $id_seq = $datosTabla['id_seq'];
        $nompkey = $tabla.'_pkey';
        
        $datosTablaF = $this->infoTable('a_fases');
        $nom_tabla_fases = $datosTablaF['nom_tabla'];
        $datosTablaT = $this->infoTable('a_tareas');
        $nom_tabla_tareas = $datosTablaT['nom_tabla'];
        
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                    CONSTRAINT $nompkey PRIMARY KEY ($campo_seq),
                    CONSTRAINT ${tabla}_id_tipo_proceso_key
                        UNIQUE (id_tipo_proceso, id_activ, id_fase, id_tarea),
                    CONSTRAINT ${tabla}_id_fase_fk
                        FOREIGN KEY (id_fase) REFERENCES $nom_tabla_fases(id_fase) ON DELETE CASCADE,
                    CONSTRAINT ${tabla}_id_tarea
                        FOREIGN KEY (id_tarea) REFERENCES $nom_tabla_tareas(id_tarea) ON DELETE CASCADE
                ) 
            INHERITS (global.$tabla_padre);";

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
        
        /* Los constraint de 'primary key' y 'foreign key' deben estar en la creación de la tabla,
         *  que permite la clausaula 'IF EXISTS'.  De otro modo da error cuando se está activando un módulo
         *  que ya había sido instalado y se había desactivado, pero no borrado.
         *  
        
        $a_sql[] = "ALTER TABLE $nom_tabla ADD CONSTRAINT ${tabla}_id_tipo_proceso_key
                    UNIQUE (id_tipo_proceso, id_activ, id_fase, id_tarea); ";
        
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY (id_item); ";
        
        $datosTablaF = $this->infoTable('a_fases');
        $nom_tabla_fases = $datosTablaF['nom_tabla'];
        
        $a_sql[] = "ALTER TABLE $nom_tabla ADD CONSTRAINT ${tabla}_id_fase_fk
                    FOREIGN KEY (id_fase) REFERENCES $nom_tabla_fases(id_fase) ON DELETE CASCADE; ";
        
        $datosTablaT = $this->infoTable('a_tareas');
        $nom_tabla_tareas = $datosTablaT['nom_tabla'];
        
        $a_sql[] = "ALTER TABLE $nom_tabla ADD CONSTRAINT ${tabla}_id_tarea
                    FOREIGN KEY (id_tarea) REFERENCES $nom_tabla_tareas(id_tarea) ON DELETE CASCADE; ";
        */
        
        // No va con tablas heredadas
        //$a_sql[] = "ALTER TABLE $nom_tabla ADD CONSTRAINT a_actividad_proceso_id_activ_fk
        //            FOREIGN KEY (id_activ) REFERENCES public.a_actividades_all(id_activ) ON DELETE CASCADE; ";
        
        $a_sql[] = "CREATE INDEX IF NOT EXISTS ${tabla}_n_orden ON $nom_tabla USING btree (n_orden); ";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";
        
        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun');
    }
    public function eliminar_a_actividad_proceso($seccion) {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        if ($seccion == 'sv') {
            $tabla = "a_actividad_proceso_sv";
        }
        if ($seccion == 'sf') {
            $tabla = "a_actividad_proceso_sf";
        }
        $datosTabla = $this->infoTable($tabla);
        
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
        $nompkey = $tabla.'_pkey';
        
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                    CONSTRAINT $nompkey PRIMARY KEY ($campo_seq)
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
        
        /* Los constraint de 'primary key' y 'foreign key' deben estar en la creación de la tabla,
         *  que permite la clausaula 'IF EXISTS'.  De otro modo da error cuando se está activando un módulo
         *  que ya había sido instalado y se había desactivado, pero no borrado.
         *  
        
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY (id_tipo_proceso); ";
        */
        
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
        $nompkey = $tabla.'_pkey';
        
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                    CONSTRAINT $nompkey PRIMARY KEY ($campo_seq)
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
        
        $a_sql[] = "CREATE UNIQUE INDEX IF NOT EXISTS ${tabla}_id_tarea_key ON $nom_tabla USING btree (id_tarea); ";
            
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
        $nompkey = $tabla.'_pkey';
        
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                    CONSTRAINT $nompkey PRIMARY KEY ($campo_seq)
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
        
        
        /* Los constraint de 'primary key' y 'foreign key' deben estar en la creación de la tabla,
         *  que permite la clausaula 'IF EXISTS'.  De otro modo da error cuando se está activando un módulo
         *  que ya había sido instalado y se había desactivado, pero no borrado.
         *
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY (id_fase); ";
        */
            
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

    public function create_a_tareas_proceso() {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        $tabla = "a_tareas_proceso";
        $datosTabla = $this->infoTable($tabla);
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];
        $id_seq = $datosTabla['id_seq'];
        $nompkey = $tabla.'_pkey';
        
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                    CONSTRAINT $nompkey PRIMARY KEY ($campo_seq),
                    CONSTRAINT a_procesos_ukey
                        UNIQUE (id_tipo_proceso, id_fase, id_tarea)
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
        
        /* Los constraint de 'primary key' y 'foreign key' deben estar en la creación de la tabla,
         *  que permite la clausaula 'IF EXISTS'.  De otro modo da error cuando se está activando un módulo
         *  que ya había sido instalado y se había desactivado, pero no borrado.
         *
         
        $a_sql[] = "ALTER TABLE $nom_tabla ADD CONSTRAINT a_procesos_ukey
                        UNIQUE (id_tipo_proceso, id_fase, id_tarea); ";
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY (id_item); ";
        */
        
        $a_sql[] = "CREATE UNIQUE INDEX IF NOT EXISTS ${tabla}_idx ON $nom_tabla USING btree (id_tipo_proceso, id_fase, id_tarea); ";
            
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role; ";
        
        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun');
    }
    public function eliminar_a_tareas_proceso() {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        $datosTabla = $this->infoTable("a_tareas_proceso");
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $id_seq = $datosTabla['id_seq'];
        
        $a_sql = [];
        $a_sql[0] = "DROP SEQUENCE IF EXISTS $id_seq CASCADE;" ;
        $this->executeSql($a_sql);

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('comun');
    }
    
    /**
     * En la BD sf-e/sv-e [exterior] (esquema).
     */
    public function create_aux_usuarios_perm() {
        // OJO Corresponde al esquema sf-e/sv-e, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"'. $this->esquema .'"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('sfsv-e');
        
        $tabla = "aux_usuarios_perm";
        $datosTabla = $this->infoTable($tabla);
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];
        $id_seq = $datosTabla['id_seq'];
        $nompkey = $tabla.'_pkey';
        
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                    CONSTRAINT $nompkey PRIMARY KEY ($campo_seq)
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
        
        
        /* Los constraint de 'primary key' y 'foreign key' deben estar en la creación de la tabla,
         *  que permite la clausaula 'IF EXISTS'.  De otro modo da error cuando se está activando un módulo
         *  que ya había sido instalado y se había desactivado, pero no borrado.
         *
         
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY (id_item); ";
        */
        
        $a_sql[] = "CREATE INDEX IF NOT EXISTS ${tabla}_id_usuario ON $nom_tabla USING btree (id_usuario); ";
        $a_sql[] = "CREATE INDEX IF NOT EXISTS ${tabla}_tipo_activ ON $nom_tabla USING btree (id_tipo_activ_txt); ";
        
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role; ";
        
        $this->executeSql($a_sql);
        
        $this->delPermisoGlobal('sfsv-e');
        // Devolver los valores al estado original
        $this->esquema = $esquema_org;
        $this->role = $role_org;
    }
    public function eliminar_aux_usuarios_perm() {
        // OJO Corresponde al esquema sf-e/sv-e, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"'. $this->esquema .'"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('sfsv-e');
        
        $datosTabla = $this->infoTable("aux_usuarios_perm");
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $id_seq = $datosTabla['id_seq'];
        
        $a_sql = [];
        $a_sql[0] = "DROP SEQUENCE IF EXISTS $id_seq CASCADE;" ;
        $this->executeSql($a_sql);

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('sfsv-e');
        // Devolver los valores al estado original
        $this->esquema = $esquema_org;
        $this->role = $role_org;
    }

    /* ###################### LLENAR TABLAS ################################ */
    /**
     * La tabla ya existe, pero hay que actualizar el tipo de proceso
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
        $fields = "id_tipo_activ, nombre, id_tipo_proceso_sv, id_tipo_proceso_ex_sv, id_tipo_proceso_sf, id_tipo_proceso_ex_sf";
       
        // Comprobar que existe el fichero (la ruta esta bien...
        if (!file_exists($filename)) {
            $msg = sprintf(_("no existe el fichero: %s"),$filename);
            exit ($msg);
        }
        
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
        $fields = "id_tipo_proceso, nom_proceso, sfsv";
        
        // Comprobar que existe el fichero (la ruta esta bien...
        if (!file_exists($filename)) {
            $msg = sprintf(_("no existe el fichero: %s"),$filename);
            exit ($msg);
        }
        
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
        // Con CASCADE para borrar los que dependen de lel por FOREIGN KEY (a_actividad_proceso_sv/sf)
        $a_sql[0] = "TRUNCATE $nom_tabla RESTART IDENTITY CASCADE;" ;
        $this->executeSql($a_sql);
        
        $delimiter = "\t"; 
        $null_as = "\\\\N";
        $fields = "id_fase, id_tarea, desc_tarea";
        
        // Comprobar que existe el fichero (la ruta esta bien...
        if (!file_exists($filename)) {
            $msg = sprintf(_("no existe el fichero: %s"),$filename);
            exit ($msg);
        }
        
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
        // Con CASCADE para borrar los que dependen de lel por FOREIGN KEY (a_actividad_proceso_sv/sf)
        $a_sql[0] = "TRUNCATE $nom_tabla RESTART IDENTITY CASCADE;" ;
        $this->executeSql($a_sql);
        
        $delimiter = "\t"; 
        $null_as = "\\\\N";
        $fields = "id_fase, desc_fase, sf, sv";
        
        // Comprobar que existe el fichero (la ruta esta bien...
        if (!file_exists($filename)) {
            $msg = sprintf(_("no existe el fichero: %s"),$filename);
            exit ($msg);
        }
        
        $oDbl->pgsqlCopyFromFile($nom_tabla, $filename, $delimiter, $null_as, $fields);

        // Fix sequences
        $a_sql[0] = "SELECT SETVAL('$id_seq', (SELECT MAX($campo_seq)/10 FROM $nom_tabla) )";
        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun');
    }
    public function llenar_a_tareas_proceso() {
        $this->addPermisoGlobal('comun');
        $this->setConexion('comun');
        $datosTabla = $this->infoTable("a_tareas_proceso");
        
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
        
        // Comprobar que existe el fichero (la ruta esta bien...
        if (!file_exists($filename)) {
            $msg = sprintf(_("no existe el fichero: %s"),$filename);
            exit ($msg);
        }
        
        $oDbl->pgsqlCopyFromFile($nom_tabla, $filename, $delimiter, $null_as, $fields);
        
        // Fix sequences
        $a_sql[0] = "SELECT SETVAL('$id_seq', (SELECT MAX($campo_seq) FROM $nom_tabla) )";
        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun');
    }
    
    public function llenar_aux_usuarios_perm() {
        // OJO Corresponde al esquema sf-e/sv-e, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"'. $this->esquema .'"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('sfsv-e');
        $this->setConexion('sfsv-e');

        $datosTabla = $this->infoTable("aux_usuarios_perm");
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];
        $id_seq = $datosTabla['id_seq'];
        $filename = $datosTabla['filename'];
        if (ConfigGlobal::mi_sfsv() == 1) {
            $filename .= '_sv';
        } else {
            $filename .= '_sf';
        }
        $oDbl = $this->oDbl;
        
        $a_sql = [];
        $a_sql[0] = "TRUNCATE $nom_tabla RESTART IDENTITY;" ;
        $this->executeSql($a_sql);
        
        $delimiter = "\t"; 
        $null_as = "\\\\N";
        $fields = "id_item, id_usuario, id_tipo_activ_txt, fases_csv, accion, afecta_a, dl_propia, json_fase_accion";
        
        // Comprobar que existe el fichero (la ruta esta bien...
        if (!file_exists($filename)) {
            $msg = sprintf(_("no existe el fichero: %s"),$filename);
            exit ($msg);
        }
        
        $oDbl->pgsqlCopyFromFile($nom_tabla, $filename, $delimiter, $null_as, $fields);
        
        // Fix sequences
        $a_sql[0] = "SELECT SETVAL('$id_seq', (SELECT MAX($campo_seq) FROM $nom_tabla) )";
        $this->executeSql($a_sql);

        $this->delPermisoGlobal('sfsv');
        // Devolver los valores al estado original
        $this->esquema = $esquema_org;
        $this->role = $role_org;
    }

 }