<?php

namespace pasarela\db;

use core\ConfigGlobal;
use devel\model\DBAbstract;

/**
 * crear las tablas necesarias para el esquema.
 * Heredadas de global
 */
class DBEsquema extends DBAbstract
{

    private $dir_base = ConfigGlobal::DIR . "/apps/pasarela/db";

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
        $this->eliminar_pasarela_dl();
    }

    public function createAll()
    {
        $this->create_pasarela_dl();
    }

    public function llenarAll()
    {
    }

    private function infoTable($tabla)
    {
        $datosTabla = [];
        $datosTabla['tabla'] = $tabla;
        switch ($tabla) {
            case "pasarela_dl":
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
    public function create_pasarela_dl()
    {
        $this->addPermisoGlobal('comun');

        $tabla_padre = "pasarela";
        $tabla = "pasarela_dl";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                ) 
            INHERITS (global.$tabla_padre);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY (nom_parametro); ";

        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun');
    }

    public function eliminar_pasarela_dl()
    {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        $tabla = "pasarela_dl";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('comun');
    }

}