<?php

namespace pasarela\db;

use core\ConfigGlobal;
use devel\model\DBAbstract;

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
        $this->eliminar_pasarela();
    }

    public function createAll()
    {
        $this->create_pasarela();
    }

    /**
     * En la BD Común (global).
     */
    public function create_pasarela()
    {
        $this->addPermisoGlobal('comun');

        $tabla = "pasarela";
        $nom_tabla = $this->getNomTabla($tabla);
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                    id_schema integer NOT NULL,
                    nom_parametro text NOT NULL,
                    json_valor jsonb 
                    );";

        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->user_orbix";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun');
    }

    public function eliminar_pasarela(): void
    {
        $this->addPermisoGlobal('comun');

        $tabla = "pasarela";
        $nom_tabla = $this->getNomTabla($tabla);
        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('comun');
    }
}
