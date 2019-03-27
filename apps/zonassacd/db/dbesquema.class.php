<?php
namespace zonassacd\db;
use core\ConfigGlobal;
use devel\model\DBAbstract;

/**
 * crear las tablas necesarias para el esquema.
 * Heredadas de global
 */
class DBEsquema extends DBAbstract {

    private $dir_base = ConfigGlobal::DIR."/apps/zonassacd/db";
    
    public function __construct($esquema_sfsv=NULL) {
        if (empty($esquema_sfsv)) {
            $esquema_sfsv = ConfigGlobal::mi_region_dl();
        }
        $this->esquema = substr($esquema_sfsv,0,-1); // quito la v o la f.
        $this->role = '"'. $this->esquema .'"';
        $this->role_vf = '"'. $esquema_sfsv .'"';
    }
    
    public function dropAll() {
        $this->eliminar_zonas();
        $this->eliminar_zonas_grupos();
        $this->eliminar_zonas_sacd();
    }
    
    public function createAll() {
        $this->create_zonas();
        $this->create_zonas_grupos();
        $this->create_zonas_sacd();
    }
    
    public function llenarAll() {
        $this->llenar_zonas();
        $this->llenar_zonas_grupos();
    }
    
    private function infoTable($tabla) {
        $datosTabla = [];
        $datosTabla['tabla'] = $tabla;
        switch ($tabla) {
            case "zonas":
                $nom_tabla = $this->getNomTabla($tabla);
                $campo_seq = 'id_zona';
                $id_seq = $nom_tabla."_".$campo_seq."_seq";
                break;
            case "zonas_grupos":
                $nom_tabla = $this->getNomTabla($tabla);
                $campo_seq = 'id_grupo';
                $id_seq = $nom_tabla."_".$campo_seq."_seq";
                break;
            case "zonas_sacd":
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
    
    public function create_zonas() {
        // OJO Corresponde al esquema sf/sv, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"'. $this->esquema .'"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('svsf');
        
        $tabla = "zonas";
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
        
        
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY (id_zona); ";
        
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role; ";
        
        // Foreign key en la tabla de centros (solo la sv, la sf está en otra base de datos y no se puede):
        // Sólo se puede si el campo id_zona de u_centros_dl está vacio.
        $a_sql[] = "UPDATE u_centros_dl SET id_zona = NULL; ";
        $a_sql[] = "ALTER TABLE u_centros_dl
                    ADD CONSTRAINT u_centros_dl_id_zona_fk FOREIGN KEY (id_zona) REFERENCES zonas(id_zona) ON UPDATE CASCADE ON DELETE SET NULL; ";
        
        $this->executeSql($a_sql);
        
        $this->delPermisoGlobal('svsf');
        // Devolver los valores al estodo original
        $this->esquema = $esquema_org;
        $this->role = $role_org;
    }
    public function eliminar_zonas() {
        // OJO Corresponde al esquema sf/sv, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"'. $this->esquema .'"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('svsf');
        
        $datosTabla = $this->infoTable("zonas");
        
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
    
    public function create_zonas_grupos() {
        // OJO Corresponde al esquema sf/sv, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"'. $this->esquema .'"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('svsf');
        
        $tabla = "zonas_grupos";
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
        
        
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY (id_grupo); ";
        
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role; ";
        
        $this->executeSql($a_sql);
        
        $this->delPermisoGlobal('svsf');
        // Devolver los valores al estodo original
        $this->esquema = $esquema_org;
        $this->role = $role_org;
    }
    public function eliminar_zonas_grupos() {
        // OJO Corresponde al esquema sf/sv, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"'. $this->esquema .'"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('svsf');
        
        $datosTabla = $this->infoTable("zonas_grupos");
        
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
    
    public function create_zonas_sacd() {
        // OJO Corresponde al esquema sf/sv, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"'. $this->esquema .'"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('svsf');
        
        $tabla = "zonas_sacd";
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
        
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role; ";
        
        $a_sql[] = "ALTER TABLE $nom_tabla
                        ADD CONSTRAINT zonas_sacd_id_nom_key UNIQUE (id_nom, id_zona); ";
        
        $a_sql[] = "ALTER TABLE $nom_tabla
                        ADD CONSTRAINT zonas_sacd_id_zona_fkey FOREIGN KEY (id_zona) REFERENCES zonas(id_zona) ON DELETE CASCADE; ";
        
        $this->executeSql($a_sql);
        
        $this->delPermisoGlobal('svsf');
        // Devolver los valores al estodo original
        $this->esquema = $esquema_org;
        $this->role = $role_org;
    }
    public function eliminar_zonas_sacd() {
        // OJO Corresponde al esquema sf/sv, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"'. $this->esquema .'"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('svsf');
        
        $datosTabla = $this->infoTable("zonas_sacd");
        
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
    
    //// LLENAR 
    public function llenar_zonas() {
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"'. $this->esquema .'"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('svsf');
        $this->setConexion('svsf');
        
        $datosTabla = $this->infoTable("zonas");
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];
        $id_seq = $datosTabla['id_seq'];
        $filename = $datosTabla['filename'];
        $oDbl = $this->oDbl;
        
        $a_sql = [];
        // Como tiene un forign key, si se añade CASCADE, borrará todos los centros
        // hay que borrar el fk y volverlo a crear.
        $a_sql[] = "ALTER TABLE u_centros_dl DROP CONSTRAINT u_centros_dl_id_zona_fk; ";
        $a_sql[] = "ALTER TABLE zonas_sacd DROP CONSTRAINT zonas_sacd_id_zona_fkey; ";
        $a_sql[] = "TRUNCATE $nom_tabla RESTART IDENTITY;" ;
        $this->executeSql($a_sql);
        
        $delimiter = "\t";
        $null_as = "\\\\N";
        $fields = "id_zona, nombre_zona, orden, id_grupo, id_nom";
        
        // Comprobar que existe el fichero (la ruta esta bien...
        if (!file_exists($filename)) {
            $msg = sprintf(_("no existe el fichero: %s"),$filename);
            exit ($msg);
        }
        
        $oDbl->pgsqlCopyFromFile($nom_tabla, $filename, $delimiter, $null_as, $fields);
        // Fix sequences
        $a_sql = [];
        $a_sql[0] = "SELECT SETVAL('$id_seq', (SELECT MAX($campo_seq) FROM $nom_tabla) )";
        $this->executeSql($a_sql);
        
        $a_sql = [];
        $a_sql[] = "UPDATE u_centros_dl SET id_zona = NULL; ";
        $a_sql[] = "ALTER TABLE u_centros_dl
                    ADD CONSTRAINT u_centros_dl_id_zona_fk FOREIGN KEY (id_zona) REFERENCES zonas(id_zona) ON UPDATE CASCADE ON DELETE SET NULL; ";
        $a_sql[] = "ALTER TABLE $nom_tabla
                        ADD CONSTRAINT zonas_sacd_id_zona_fkey FOREIGN KEY (id_zona) REFERENCES zonas(id_zona) ON DELETE CASCADE; ";
        $this->executeSql($a_sql);
        
        $this->delPermisoGlobal('svsf');
    }
    public function llenar_zonas_grupos() {
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"'. $this->esquema .'"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('svsf');
        $this->setConexion('svsf');
        $datosTabla = $this->infoTable("zonas_grupos");
        
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
        $fields = "id_grupo, nombre_grupo, orden";
        
        // Comprobar que existe el fichero (la ruta esta bien...
        if (!file_exists($filename)) {
            $msg = sprintf(_("no existe el fichero: %s"),$filename);
            exit ($msg);
        }
        
        $oDbl->pgsqlCopyFromFile($nom_tabla, $filename, $delimiter, $null_as, $fields);
        // Fix sequences
        $a_sql[0] = "SELECT SETVAL('$id_seq', (SELECT MAX($campo_seq) FROM $nom_tabla) )";
        $this->executeSql($a_sql);
        
        $this->delPermisoGlobal('svsf');
    }
    public function llenar_zonas_sacd() {
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"'. $this->esquema .'"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('svsf');
        $this->setConexion('svsf');
        $datosTabla = $this->infoTable("zonas_sacd");
        
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
        $fields = "id_item, id_nom, id_zona, propia";
        
        // Comprobar que existe el fichero (la ruta esta bien...
        if (!file_exists($filename)) {
            $msg = sprintf(_("no existe el fichero: %s"),$filename);
            exit ($msg);
        }
        
        $oDbl->pgsqlCopyFromFile($nom_tabla, $filename, $delimiter, $null_as, $fields);
        // Fix sequences
        $a_sql[0] = "SELECT SETVAL('$id_seq', (SELECT MAX($campo_seq) FROM $nom_tabla) )";
        $this->executeSql($a_sql);
        
        $this->delPermisoGlobal('svsf');
    }
}