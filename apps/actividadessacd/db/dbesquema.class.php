<?php
namespace actividadessacd\db;
use core\ConfigGlobal;
use devel\model\DBAbstract;

/**
 * crear las tablas necesarias para el esquema.
 * Heredadas de global
 */
class DBEsquema extends DBAbstract {

    private $dir_base = ConfigGlobal::DIR."/apps/actividadessacd/db";
    
    public function __construct($esquema_sfsv=NULL) {
        if (empty($esquema_sfsv)) {
            $esquema_sfsv = ConfigGlobal::mi_region_dl();
        }
        $this->esquema = substr($esquema_sfsv,0,-1); // quito la v o la f.
        $this->role = '"'. $this->esquema .'"';
        $this->role_vf = '"'. $esquema_sfsv .'"';
    }
    
    public function dropAll() {
        $this->eliminar_atn_sacd_textos();
    }
    
    public function createAll() {
        $this->create_atn_sacd_textos();
    }
    
    public function llenarAll() {
        $this->llenar_atn_sacd_textos();
    }
    
    private function infoTable($tabla) {
        $datosTabla = [];
        $datosTabla['tabla'] = $tabla;
        switch ($tabla) {
			case "a_sacd_textos":
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
     * En la BD Sf/sv (esquema).
     */
    public function create_atn_sacd_textos() {
        // OJO Corresponde al esquema sf/sv, no al comun.
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"'. $this->esquema .'"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('sfsv-e');

        $tabla = "a_sacd_textos";
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
        
        $a_sql[] = "ALTER TABLE $nom_tabla ADD CONSTRAINT a_sacd_textos_ukey
                    UNIQUE (idioma,clave); ";

        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";
        
        $this->executeSql($a_sql);

        $this->delPermisoGlobal('sfsv-e');
    }
    public function eliminar_atn_sacd_textos() {
        // OJO Corresponde al esquema sf/sv, no al comun.
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"'. $this->esquema .'"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('sfsv-e');

        $datosTabla = $this->infoTable("a_sacd_textos");
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $id_seq = $datosTabla['id_seq'];
        
        $a_sql = [];
        $a_sql[0] = "DROP SEQUENCE IF EXISTS $id_seq CASCADE;" ;
        $this->executeSql($a_sql);

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('sfsv-e');
    }


    /* ###################### LLENAR TABLAS ################################ */

    public function llenar_atn_sacd_textos() {
        // OJO Corresponde al esquema sf/sv, no al comun.
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"'. $this->esquema .'"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('sfsv-e');
        $this->setConexion('sfsv-e');
        $datosTabla = $this->infoTable("a_sacd_textos");
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $filename = $datosTabla['filename'];
        $oDbl = $this->oDbl;
        
        $a_sql = [];
        $a_sql[0] = "TRUNCATE $nom_tabla RESTART IDENTITY;" ;
        $this->executeSql($a_sql);
        
        $delimiter = "\t"; 
        $null_as = "\\\\N";
        $fields = "idioma, clave, texto";
        
        // Comprobar que existe el fichero (la ruta esta bien...
        if (!file_exists($filename)) {
            $msg = sprintf(_("no existe el fichero: %s"),$filename);
            exit ($msg);
        }
        
        $oDbl->pgsqlCopyFromFile($nom_tabla, $filename, $delimiter, $null_as, $fields);
        
        $this->delPermisoGlobal('sfsv-e');
    }

 }
