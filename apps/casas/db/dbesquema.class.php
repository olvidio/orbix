<?php
namespace casas\db;
use core\ConfigGlobal;
use devel\model\DBAbstract;

/**
 * crear las tablas necesarias para el esquema.
 * Heredadas de global
 */
class DBEsquema extends DBAbstract {

    private $dir_base = ConfigGlobal::DIR."/apps/casas/db";
    
    public function __construct($esquema_sfsv=NULL) {
        if (empty($esquema_sfsv)) {
            $esquema_sfsv = ConfigGlobal::mi_region_dl();
        }
        $this->esquema = substr($esquema_sfsv,0,-1); // quito la v o la f.
        $this->role = '"'. $this->esquema .'"';
        $this->role_vf = '"'. $esquema_sfsv .'"';
    }
    
    public function dropAll() {
        $this->eliminar_da_ingresos_dl();
        $this->eliminar_du_gastos_dl();
        $this->eliminar_du_grupos_dl();
    }
    
    public function createAll() {
        $this->create_da_ingresos_dl();
        $this->create_du_gastos_dl();
        $this->create_du_grupos_dl();
    }
    
    public function llenarAll() {
    }
    
    private function infoTable($tabla) {
        $datosTabla = [];
        $datosTabla['tabla'] = $tabla;
        switch ($tabla) {
            case "da_ingresos_dl":
                $nom_tabla = $this->getNomTabla($tabla);
                $campo_seq = '';
                $id_seq = '';
                break;
            case "du_gastos_dl":
                $nom_tabla = $this->getNomTabla($tabla);
                $campo_seq = 'id_item';
                $id_seq = $nom_tabla."_".$campo_seq."_seq";
                break;
            case "du_grupos_dl":
                $nom_tabla = $this->getNomTabla($tabla);
                $campo_seq = 'id_item';
                $id_seq = $nom_tabla."_".$campo_seq."_seq";
                break;
            case "a_actividades_dl":
                $nom_tabla = $this->getNomTabla($tabla);
                $campo_seq = '';
                $id_seq = '';
                break;
            case "u_cdc_dl":
                $nom_tabla = $this->getNomTabla($tabla);
                $campo_seq = '';
                $id_seq = '';
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
     * Tiene un foreing key con el id_activ. 
     */
    public function create_da_ingresos_dl() {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        $tabla_padre = "da_ingresos";
        $tabla = "da_ingresos_dl";
        $datosTabla = $this->infoTable($tabla);
        
        $nom_tabla = $datosTabla['nom_tabla'];
        
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                ) 
            INHERITS (global.$tabla_padre);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY (id_activ); ";
        $a_sql[] = "CREATE INDEX ${tabla}_id_activ ON $nom_tabla USING btree (id_activ); ";
        
        $datosTablaA = $this->infoTable('a_actividades_dl');
        $nom_tabla_activ = $datosTablaA['nom_tabla'];
        $a_sql[] = "ALTER TABLE $nom_tabla ADD CONSTRAINT ${tabla}_id_activ_fk
                    FOREIGN KEY (id_activ) REFERENCES $nom_tabla_activ(id_activ) ON DELETE CASCADE; ";
        
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";
        
        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun');
    }
    public function eliminar_da_ingresos_dl() {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        $tabla = "da_ingresos_dl";
        $datosTabla = $this->infoTable($tabla);
        
        $nom_tabla = $datosTabla['nom_tabla'];

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('comun');
    }
    
    public function create_du_gastos_dl() {
        $this->addPermisoGlobal('comun');
        
        $tabla_padre = "du_gastos";
        $tabla = "du_gastos_dl";
        $datosTabla = $this->infoTable($tabla);
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];
        $id_seq = $datosTabla['id_seq'];
        
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                    )
                INHERITS (global.$tabla_padre);";
        
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
        
        $datosTablaA = $this->infoTable('u_cdc_dl');
        $nom_tabla_activ = $datosTablaA['nom_tabla'];
        $a_sql[] = "ALTER TABLE $nom_tabla ADD CONSTRAINT ${tabla}_id_ubi_fk
                    FOREIGN KEY (id_ubi) REFERENCES $nom_tabla_activ(id_ubi) ON DELETE CASCADE; ";
        
        // No va con tablas heredadas
        $a_sql[] = "CREATE INDEX ${tabla}_id_ubi ON $nom_tabla USING btree (id_ubi); ";
        $a_sql[] = "CREATE INDEX ${tabla}_f_gasto ON $nom_tabla USING btree (f_gasto); ";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";
        
        $this->executeSql($a_sql);
        
        $this->delPermisoGlobal('comun');
    }
    public function eliminar_du_gastos_dl() {
        $this->addPermisoGlobal('comun');
        
        $tabla = "du_gastos_dl";
        $datosTabla = $this->infoTable($tabla);
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $id_seq = $datosTabla['id_seq'];
        
        $a_sql = [];
        $a_sql[0] = "DROP SEQUENCE IF EXISTS $id_seq CASCADE;" ;
        $this->executeSql($a_sql);
        
        $this->eliminar($nom_tabla);
        
        $this->delPermisoGlobal('comun');
    }

    public function create_du_grupos_dl() {
        $this->addPermisoGlobal('comun');
        
        $tabla_padre = "du_grupos";
        $tabla = "du_grupos_dl";
        $datosTabla = $this->infoTable($tabla);
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];
        $id_seq = $datosTabla['id_seq'];
        
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                    )
                INHERITS (global.$tabla_padre);";
        
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
        
        $datosTablaA = $this->infoTable('u_cdc_dl');
        $nom_tabla_activ = $datosTablaA['nom_tabla'];
        $a_sql[] = "ALTER TABLE $nom_tabla ADD CONSTRAINT ${tabla}_id_ubi_padre_fk
                    FOREIGN KEY (id_ubi_padre) REFERENCES $nom_tabla_activ(id_ubi) ON DELETE CASCADE; ";
        $a_sql[] = "ALTER TABLE $nom_tabla ADD CONSTRAINT ${tabla}_id_ubi_hijo_fk
                    FOREIGN KEY (id_ubi_hijo) REFERENCES $nom_tabla_activ(id_ubi) ON DELETE CASCADE; ";
        
        // No va con tablas heredadas
        $a_sql[] = "CREATE INDEX ${tabla}_id_ubi_padre ON $nom_tabla USING btree (id_ubi_padre); ";
        $a_sql[] = "CREATE INDEX ${tabla}_id_ubi_hijo ON $nom_tabla USING btree (id_ubi_hijo); ";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";
        
        $this->executeSql($a_sql);
        
        $this->delPermisoGlobal('comun');
    }
    public function eliminar_du_grupos_dl() {
        $this->addPermisoGlobal('comun');
        
        $tabla = "du_grupos_dl";
        $datosTabla = $this->infoTable($tabla);
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $id_seq = $datosTabla['id_seq'];
        
        $a_sql = [];
        $a_sql[0] = "DROP SEQUENCE IF EXISTS $id_seq CASCADE;" ;
        $this->executeSql($a_sql);
        
        $this->eliminar($nom_tabla);
        
        $this->delPermisoGlobal('comun');
    }
}