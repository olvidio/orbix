<?php

namespace actividadescentro\db;

use core\ConfigGlobal;
use devel\model\DBAbstract;

/**
 * crear las tablas necesarias para el esquema.
 * Heredadas de global
 */
class DBEsquema extends DBAbstract
{

    private $dir_base = ConfigGlobal::DIR . "/apps/actividadescentro/db";

    public function __construct($esquema_sfsv = NULL)
    {
        if (empty($esquema_sfsv)) {
            $esquema_sfsv = ConfigGlobal::mi_region_dl();
        }
        $this->esquema = substr($esquema_sfsv, 0, -1); // quito la v o la f.
        $this->role = '"' . $this->esquema . '"';
        $this->role_vf = '"' . $esquema_sfsv . '"';
    }

    public function dropAll()
    {
        $this->eliminar_da_ctr_encargados();
        // eliminar las tablas en la DBSelect para la sincronización.
        if (DBAbstract::hasServerSelect()) {
            $oDBEsquemaSelect = new DBEsquemaSelect();
            $oDBEsquemaSelect->dropAllSelect();
        }
    }

    public function createAll()
    {
        $this->create_da_ctr_encargados();
        // crear las tablas en la DBSelect para la sincronización.
        if (DBAbstract::hasServerSelect()) {
            $oDBEsquemaSelect = new DBEsquemaSelect();
            $oDBEsquemaSelect->createAllSelect();
        }
    }

    public function llenarAll()
    {
        //$this->llenar_da_ctr_encargados();
    }

    protected function infoTable($tabla)
    {
        $datosTabla = [];
        $datosTabla['tabla'] = $tabla;
        switch ($tabla) {
            case "da_ctr_encargados":
                $nom_tabla = $this->getNomTabla($tabla);
                $campo_seq = '';
                $id_seq = '';
                break;
        }
        $datosTabla['nom_tabla'] = $nom_tabla;
        $datosTabla['campo_seq'] = $campo_seq;
        $datosTabla['id_seq'] = $id_seq;
        $datosTabla['filename'] = $this->dir_base . "/$tabla.csv";
        return $datosTabla;
    }


    /**
     * En la BD Comun (esquema).
     */
    public function create_da_ctr_encargados()
    {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        $tabla = "da_ctr_encargados";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                ) 
            INHERITS (global.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";


        $a_sql[] = "ALTER TABLE $nom_tabla ADD CONSTRAINT da_ctr_encargados_id_activ_id_ubi_key
                    UNIQUE (id_activ, id_ubi); ";

        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY (id_activ, id_ubi); ";

        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun');
    }

    public function eliminar_da_ctr_encargados()
    {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        $datosTabla = $this->infoTable("da_ctr_encargados");

        $nom_tabla = $datosTabla['nom_tabla'];
        $id_seq = $datosTabla['id_seq'];

        $a_sql = [];
        $a_sql[0] = "DROP SEQUENCE IF EXISTS $id_seq CASCADE;";
        $this->executeSql($a_sql);

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('comun');
    }


    /* ###################### LLENAR TABLAS ################################ */

    public function llenar_da_ctr_encargados()
    {
        $this->addPermisoGlobal('comun');
        $this->setConexion('comun');
        $datosTabla = $this->infoTable("da_ctr_encargados");

        $nom_tabla = $datosTabla['nom_tabla'];
        $filename = $datosTabla['filename'];
        $oDbl = $this->oDbl;

        $a_sql = [];
        $a_sql[0] = "TRUNCATE $nom_tabla RESTART IDENTITY;";
        $this->executeSql($a_sql);

        $delimiter = "\t";
        $null_as = "\\\\N";
        $fields = "id_activ, id_ubi, num_orden, encargo";

        // Comprobar que existe el fichero (la ruta esta bien...
        if (!file_exists($filename)) {
            $msg = sprintf(_("no existe el fichero: %s"), $filename);
            exit ($msg);
        }

        $oDbl->pgsqlCopyFromFile($nom_tabla, $filename, $delimiter, $null_as, $fields);

        $this->delPermisoGlobal('comun');
    }

}