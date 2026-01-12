<?php

namespace src\actividadescentro\db;

use core\ConfigGlobal;
use src\utils_database\domain\entity\DBAbstract;

/**
 * Crear las tablas necesaria a nivel de aplicación (global).
 * Cada esquema deberá crear las suyas, heredadas de estas.
 */
class DB extends DBAbstract
{

    public function __construct()
    {
        $esquema_sfsv = ConfigGlobal::mi_region_dl();
        $role = substr($esquema_sfsv, 0, -1); // quito la v o la f.
        $this->role = '"' . $role . '"';
        $this->role_vf = '"' . $esquema_sfsv . '"';

        $this->esquema = 'global';
    }

    public function dropAll()
    {
        $this->eliminar_da_ctr_encargados();
    }

    public function createAll()
    {
        $this->create_da_ctr_encargados();
    }

    /**
     * En la BD Comun (global).
     */
    public function create_da_ctr_encargados()
    {
        $this->addPermisoGlobal('comun');

        $tabla = "da_ctr_encargados";
        $nom_tabla = $this->getNomTabla($tabla);
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                    id_schema integer NOT NULL,
                    id_activ bigint NOT NULL,
                    id_ubi integer NOT NULL,
                    num_orden smallint,
                    encargo text
                    );";

        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->user_orbix";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun');
    }

    public function eliminar_da_ctr_encargados()
    {
        $this->addPermisoGlobal('comun');

        $tabla = "da_ctr_encargados";
        $nom_tabla = $this->getNomTabla($tabla);
        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('comun');
    }

}