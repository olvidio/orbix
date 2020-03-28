<?php
namespace cambios\db;
use core\ConfigGlobal;
use devel\model\DBAbstract;

/**
 * crear las tablas necesarias para el esquema.
 * Heredadas de global
 */
class DBEsquema extends DBAbstract {

    private $dir_base = ConfigGlobal::DIR."/apps/cambios/db";
    
    public function __construct($esquema_sfsv=NULL) {
        if (empty($esquema_sfsv)) {
            $esquema_sfsv = ConfigGlobal::mi_region_dl();
        }
        $this->esquema = substr($esquema_sfsv,0,-1); // quito la v o la f.
        $this->role = '"'. $this->esquema .'"';
        $this->role_vf = '"'. $esquema_sfsv .'"';
    }
    
    public function dropAll() {
        $this->eliminar_av_cambios_usuario_propiedades_pref();
        $this->eliminar_av_cambios_usuario_objeto_pref();
        $this->eliminar_av_cambios_usuario();
        $this->eliminar_av_cambios_anotados();
        $this->eliminar_av_cambios_dl();
    }
    
    public function createAll() {
        $this->create_av_cambios_dl();
        $this->create_av_cambios_anotados();
        $this->create_av_cambios_usuario();
        $this->create_av_cambios_usuario_objeto_pref();
        $this->create_av_cambios_usuario_propiedades_pref();
    }
    
    public function llenarAll() {
        //$this->llenar_a_tipos_actividad();
    }
    
    private function infoTable($tabla) {
        $datosTabla = [];
        $datosTabla['tabla'] = $tabla;
        switch ($tabla) {
            case "av_cambios_dl":
                $nom_tabla = $this->getNomTabla($tabla);
                $campo_seq = 'id_item_cambio';
                $id_seq = $nom_tabla."_".$campo_seq."_seq";
                break;
            case "av_cambios_anotados":
                $nom_tabla = $this->getNomTabla($tabla);
                $campo_seq = 'id_item';
                $id_seq = $nom_tabla."_".$campo_seq."_seq";
                break;
            case "av_cambios_usuario":
                $nom_tabla = $this->getNomTabla($tabla);
                $campo_seq = 'id_item';
                $id_seq = $nom_tabla."_".$campo_seq."_seq";
                break;
            case "av_cambios_usuario_objeto_pref":
                $nom_tabla = $this->getNomTabla($tabla);
                $campo_seq = 'id_item_usuario_objeto';
                $id_seq = $nom_tabla."_".$campo_seq."_seq";
                break;
            case "av_cambios_usuario_propiedades_pref":
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
     */
    public function create_av_cambios_dl() {
        // OJO, está en public
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        $tabla = "av_cambios_dl";
        $tabla_padre = "av_cambios";
        $datosTabla = $this->infoTable($tabla);
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];
        $id_seq = $datosTabla['id_seq'];
        
        // Al ser de la DB comun, puede ser que al intentar crear como sf, las
        // tablas ya se hayan creado como sv (o al revés).
        if ($this->tableExists($tabla)) {
            return  TRUE;
        }
        
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                ) 
            INHERITS (public.$tabla_padre);";

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
        
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY ($campo_seq); ";
        
        $a_sql[] = "CREATE INDEX IF NOT EXISTS ${tabla}_${campo_seq}_idx ON $nom_tabla USING btree ($campo_seq); ";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";
        
        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun');
    }
    public function eliminar_av_cambios_dl() {
        // OJO, está en public
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        $datosTabla = $this->infoTable("av_cambios_dl");
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $id_seq = $datosTabla['id_seq'];
        
        $a_sql = [];
        $a_sql[0] = "DROP SEQUENCE IF EXISTS $id_seq CASCADE;" ;
        $this->executeSql($a_sql);

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('comun');
    }

    /**
     * En la BD Comun (esquema).
     */
    public function create_av_cambios_anotados() {
        
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        $tabla = "av_cambios_anotados";
        $datosTabla = $this->infoTable($tabla);
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];
        $id_seq = $datosTabla['id_seq'];
        
        // Al ser de la DB comun, puede ser que al intentar crear como sf, las
        // tablas ya se hayan creado como sv (o al revés).
        if ($this->tableExists($tabla)) {
            return  TRUE;
        }
        
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                ) 
            INHERITS (global.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        $a_sql[] = "ALTER TABLE $nom_tabla ALTER anotado_sv SET DEFAULT false;";
        $a_sql[] = "ALTER TABLE $nom_tabla ALTER anotado_sf SET DEFAULT false;";
        $a_sql[] = "ALTER TABLE $nom_tabla ALTER server SET DEFAULT 1;";
        
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
        
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY ($campo_seq); ";
        
        $a_sql[] = "CREATE UNIQUE INDEX IF NOT EXISTS ${tabla}_udx ON $nom_tabla USING btree (server,id_schema_cambio,id_item_cambio); ";
        /* No sirve con tablas heredadas
        $tabla1 = 'public.av_cambios'; //la de public
        $a_sql[] = "ALTER TABLE $nom_tabla ADD CONSTRAINT av_cambios_anotados_id_item_cambio_fk
                    FOREIGN KEY (id_schema_cambio,id_item_cambio) REFERENCES $tabla1(id_schema,id_item_cambio) ON DELETE CASCADE; ";
        */
        
        $a_sql[] = "CREATE INDEX IF NOT EXISTS ${tabla}_${campo_seq}_idx ON $nom_tabla USING btree ($campo_seq); ";
        $a_sql[] = "CREATE INDEX IF NOT EXISTS ${tabla}_id_schema_cambio_idx ON $nom_tabla USING btree (id_schema_cambio); ";
        $a_sql[] = "CREATE INDEX IF NOT EXISTS ${tabla}_id_item_cambio_idx ON $nom_tabla USING btree (id_item_cambio); ";
        $a_sql[] = "CREATE INDEX IF NOT EXISTS ${tabla}_anotado_sv_idx ON $nom_tabla USING btree (anotado_sv); ";
        $a_sql[] = "CREATE INDEX IF NOT EXISTS ${tabla}_anotado_sf_idx ON $nom_tabla USING btree (anotado_sf); ";
        $a_sql[] = "CREATE INDEX IF NOT EXISTS ${tabla}_server_idx ON $nom_tabla USING btree (server); ";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";
        
        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun');
    }
    
    public function eliminar_av_cambios_anotados() {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        $datosTabla = $this->infoTable("av_cambios_anotados");
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $id_seq = $datosTabla['id_seq'];
        
        $a_sql = [];
        $a_sql[0] = "DROP SEQUENCE IF EXISTS $id_seq CASCADE;" ;
        $this->executeSql($a_sql);

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('comun');
    }
    
    /**
     * En la BD comun (esquema).
     * Correspondería a sfsv, pero para poder borrar con 'LEFT JOIN'
     * cuando se eliminan los av_cambios, la pongo en comun.
     */
    public function create_av_cambios_usuario() {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        $tabla = "av_cambios_usuario";
        $datosTabla = $this->infoTable($tabla);
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];
        $id_seq = $datosTabla['id_seq'];
        
        // Al ser de la DB comun, puede ser que al intentar crear como sf, las
        // tablas ya se hayan creado como sv (o al revés).
        if ($this->tableExists($tabla)) {
            return  TRUE;
        }
        
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                ) 
            INHERITS (global.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        $a_sql[] = "ALTER TABLE $nom_tabla ALTER avisado SET DEFAULT false;";
        
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
        $a_sql[] = "ALTER TABLE $nom_tabla ALTER sfsv SET DEFAULT 1;";
        
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY ($campo_seq); ";
        
        $a_sql[] = "CREATE UNIQUE INDEX IF NOT EXISTS ${tabla}_udx ON $nom_tabla USING btree (id_schema_cambio,id_item_cambio,id_usuario,sfsv,aviso_tipo); ";
        // FOREIGN KEYS
        /* No sirve con tablas heredadas
        $tabla1 = 'public.av_cambios'; // la de public.
        $a_sql[] = "ALTER TABLE $nom_tabla ADD CONSTRAINT av_cambios_usuario_id_item_cambio_fk
                    FOREIGN KEY (id_schema_cambio,id_item_cambio) REFERENCES $tabla1(id_schema,id_item_cambio) ON DELETE CASCADE; ";
        */
        
        $a_sql[] = "CREATE INDEX IF NOT EXISTS ${tabla}_${campo_seq}_idx ON $nom_tabla USING btree ($campo_seq); ";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";
        
        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun');
    }
    public function eliminar_av_cambios_usuario() {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        $datosTabla = $this->infoTable("av_cambios_usuario");
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $id_seq = $datosTabla['id_seq'];
        
        $a_sql = [];
        $a_sql[0] = "DROP SEQUENCE IF EXISTS $id_seq CASCADE;" ;
        $this->executeSql($a_sql);

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('comun');
    }
    
    /**
     * En la BD sv/sf (esquema).
     */
    public function create_av_cambios_usuario_objeto_pref() {
        // OJO Corresponde al esquema sf/sv, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"'. $this->esquema .'"';
        $mi_dele = ConfigGlobal::mi_delef();
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('sfsv-e');

        $tabla = "av_cambios_usuario_objeto_pref";
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
        $a_sql[] = "ALTER TABLE $nom_tabla ALTER dl_org SET DEFAULT '$mi_dele'";
        
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
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY ($campo_seq); ";
         */
        
        // FOREIGN KEYS
        // con los usuarios no va porque estan en otra base de datos (sv). 
        
        $a_sql[] = "CREATE INDEX IF NOT EXISTS ${tabla}_${campo_seq}_idx ON $nom_tabla USING btree ($campo_seq); ";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";
        
        $this->executeSql($a_sql);

        $this->delPermisoGlobal('sfsv-e');
    }
    public function eliminar_av_cambios_usuario_objeto_pref() {
        // OJO Corresponde al esquema sf/sv, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"'. $this->esquema .'"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('sfsv-e');

        $datosTabla = $this->infoTable("av_cambios_usuario_objeto_pref");
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $id_seq = $datosTabla['id_seq'];
        
        $a_sql = [];
        $a_sql[0] = "DROP SEQUENCE IF EXISTS $id_seq CASCADE;" ;
        $this->executeSql($a_sql);

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('sfsv-e');
    }
    public function create_av_cambios_usuario_propiedades_pref() {
        // OJO Corresponde al esquema sf/sv, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"'. $this->esquema .'"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('sfsv-e');

        $tabla = "av_cambios_usuario_propiedades_pref";
        $datosTabla = $this->infoTable($tabla);
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];
        $id_seq = $datosTabla['id_seq'];
        $nompkey = $tabla.'_pkey';
        $tabla1 = $this->getNomTabla('av_cambios_usuario_objeto_pref');
        
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                    CONSTRAINT $nompkey PRIMARY KEY ($campo_seq),
                    CONSTRAINT av_cambios_usuario_propiedades_pref_id_item_usuario_objeto_fk
                         FOREIGN KEY (id_item_usuario_objeto) REFERENCES $tabla1(id_item_usuario_objeto) ON DELETE CASCADE
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
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY ($campo_seq); ";
        
        // FOREIGN KEYS
        $a_sql[] = "ALTER TABLE $nom_tabla ADD CONSTRAINT av_cambios_usuario_propiedades_pref_id_item_usuario_objeto_fk
                    FOREIGN KEY (id_item_usuario_objeto) REFERENCES $tabla1(id_item_usuario_objeto) ON DELETE CASCADE; ";
         */ 
        
        $a_sql[] = "CREATE INDEX IF NOT EXISTS ${tabla}_${campo_seq}_idx ON $nom_tabla USING btree ($campo_seq); ";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";
        
        $this->executeSql($a_sql);

        $this->delPermisoGlobal('sfsv-e');
    }
    public function eliminar_av_cambios_usuario_propiedades_pref() {
        // OJO Corresponde al esquema sf/sv, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"'. $this->esquema .'"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('sfsv-e');

        $datosTabla = $this->infoTable("av_cambios_usuario_propiedades_pref");
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $id_seq = $datosTabla['id_seq'];
        
        $a_sql = [];
        $a_sql[0] = "DROP SEQUENCE IF EXISTS $id_seq CASCADE;" ;
        $this->executeSql($a_sql);

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('sfsv-e');
    }
    
}