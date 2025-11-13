<?php

namespace misas\db;

use core\ConfigGlobal;
use src\configuracion\domain\DBAbstract;

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
        $this->vf = substr($esquema_sfsv, -1); // solo la v o la f.

        $this->role = '"' . $role . '"';
        $this->role_vf = '"' . $esquema_sfsv . '"';

        $this->esquema = 'global';
    }

    public function dropAll()
    {
        $this->eliminar_cuadricula();
        $this->eliminar_iniciales();
        $this->eliminar_rel_encargo_ctr();
    }

    public function createAll()
    {
        //$this->create_plantillas();
        $this->create_cuadricula();
        $this->create_iniciales();
        $this->create_rel_encargo_ctr();
    }

    /**
     * En el esquema comun
     */
    public function create_iniciales()
    {
        $this->addPermisoGlobal('comun');
        $tabla = "misa_iniciales";
        $nom_tabla = $this->getNomTabla($tabla);
        $a_sql = [];

        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
            id_schema integer NOT NULL,
            id_nom integer,
            iniciales text,
            color varchar(6)
            );";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->user_orbix";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun');
    }


    public function eliminar_iniciales()
    {
        $this->addPermisoGlobal('comun');
        $tabla = "misa_iniciales";
        $nom_tabla = $this->getNomTabla($tabla);

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('comun');
    }

    /**
     * En el esquema comun
     */
    public function create_cuadricula()
    {
        $this->addPermisoGlobal('comun');
        $tabla = "misa_cuadricula";
        $nom_tabla = $this->getNomTabla($tabla);
        $a_sql = [];

        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
            id_schema integer NOT NULL,
            uuid_item uuid NOT NULL,
            id_enc integer NOT NULL,
            tstart timestamp,
            tend timestamp,
            id_nom integer,
            observ text,
            status smallint
            );";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->user_orbix";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun');
    }


    public function eliminar_cuadricula()
    {
        $this->addPermisoGlobal('comun');
        $tabla = "misa_cuadricula";
        $nom_tabla = $this->getNomTabla($tabla);

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('comun');
    }

    /**
     * En el esquema comun
     */
    public function create_rel_encargo_ctr()
    {
        $this->addPermisoGlobal('comun');
        $tabla = "misa_rel_encargo_ctr";
        $nom_tabla = $this->getNomTabla($tabla);
        $a_sql = [];

        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
            id_schema integer NOT NULL,
            uuid_item uuid NOT NULL,
            id_enc integer NOT NULL,
            id_ubi integer NOT NULL
            );";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->user_orbix";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun');
    }


    public function eliminar_rel_encargo_ctr()
    {
        $this->addPermisoGlobal('comun');
        $tabla = "misa_rel_encargo_ctr";
        $nom_tabla = $this->getNomTabla($tabla);

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('comun');
    }


}