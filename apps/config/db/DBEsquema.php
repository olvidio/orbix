<?php

namespace config\db;

use core\ConfigGlobal;
use devel\model\DBAbstract;

/**
 * crear las tablas necesarias para el esquema.
 * Heredadas de global
 */
class DBEsquema extends DBAbstract
{

    private $dir_base = ConfigGlobal::DIR . "/apps/config/db";

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
        $this->eliminar_x_config_schema();
    }

    public function createAll()
    {
        $this->create_x_config_schema();
    }

    public function llenarAll()
    {
        $this->llenar_x_config_schema();
    }

    private function infoTable($tabla)
    {
        $datosTabla = [];
        $datosTabla['tabla'] = $tabla;
        switch ($tabla) {
            case "x_config_schema":
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
     * En la BD Comun.
     */
    public function create_x_config_schema()
    {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        $tabla = "x_config_schema";
        $tabla_padre = "x_config_schema";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];
        $id_seq = $datosTabla['id_seq'];

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                ) 
            INHERITS (global.$tabla_padre);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";

        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY (parametro); ";
        $a_sql[] = "CREATE UNIQUE INDEX ${tabla}_udx ON $nom_tabla USING btree (parametro); ";

        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun');
    }

    public function eliminar_x_config_schema()
    {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        $datosTabla = $this->infoTable("x_config_schema");

        $nom_tabla = $datosTabla['nom_tabla'];

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('comun');
    }


    /* ###################### LLENAR TABLAS ################################ */

    public function llenar_x_config_schema()
    {
        $this->addPermisoGlobal('comun');
        $datosTabla = $this->infoTable("x_config_schema");

        $nom_tabla = $datosTabla['nom_tabla'];
        $filename = $datosTabla['filename'];
        $oDbl = $this->oDbl;

        $a_sql = [];
        $a_sql[0] = "TRUNCATE $nom_tabla RESTART IDENTITY;";
        $this->executeSql($a_sql);

        $delimiter = "\t";
        $null_as = "\\\\N";
        $fields = "parametro, valor";

        // Comprobar que existe el fichero (la ruta esta bien...
        if (!file_exists($filename)) {
            $msg = sprintf(_("no existe el fichero: %s"), $filename);
            exit ($msg);
        }

        $oDbl->pgsqlCopyFromFile($nom_tabla, $filename, $delimiter, $null_as, $fields);

        $this->delPermisoGlobal('comun');
    }

}