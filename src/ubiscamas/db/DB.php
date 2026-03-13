<?php

namespace src\ubiscamas\db;

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

        $this->esquema = 'public';
    }

    public function dropAll()
    {
        $this->eliminar_du_camas();
        $this->eliminar_du_habitaciones();
    }

    public function createAll()
    {
        $this->create_du_habitaciones();
        $this->create_du_camas();
    }

    /**
     * En la BD Comun (public).
     * OJO Corresponde al esquema public, no al global.
     */
    public function create_du_habitaciones()
    {
        $esquema_org = $this->esquema;
        $this->esquema = 'public';

        $this->addPermisoGlobal('comun');

        $tabla = "du_habitaciones";
        $nom_tabla = $this->getNomTabla($tabla);

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                id_schema int NOT NULL,
                id_ubi int NOT NULL,
                id_habitacion uuid NOT NULL,
                orden int NOT NULL,
                nombre text,
                numero_camas int,
                numero_camas_vip int,
                planta text,
                sillon bool,
                adaptada bool,
                fumador bool,
                tipoLavabo int,
                despacho bool
                ); ";


        $a_sql[] = "CREATE INDEX IF NOT EXISTS {$tabla}_id_ubi_idx ON $nom_tabla USING btree (id_ubi); ";
        $a_sql[] = "CREATE INDEX IF NOT EXISTS {$tabla}_id_habitacion_idx ON $nom_tabla USING btree (id_habitacion); ";

        //$a_sql[] = "CREATE UNIQUE INDEX IF NOT EXISTS {$tabla}_udx ON $nom_tabla USING btree (id_schema,id_ubi); ";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT 3000";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->user_orbix";
        // Asegurarme que todos pueden leer:
        $a_sql[] = "GRANT SELECT,DELETE ON $nom_tabla TO PUBLIC; ";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun');
        // Devolver los valores al estado original
        $this->esquema = $esquema_org;
    }

    public function eliminar_du_habitaciones()
    {
        $esquema_org = $this->esquema;
        $this->esquema = 'public';

        $this->addPermisoGlobal('comun');

        $tabla = "du_habitaciones";
        $nom_tabla = $this->getNomTabla($tabla);
        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('comun');
        // Devolver los valores al estado original
        $this->esquema = $esquema_org;
    }

    /**
     * En la BD comun (global).
     */
    public function create_du_camas()
    {
        $this->addPermisoGlobal('comun');

        $tabla = "du_camas";
        $nom_tabla = $this->getNomTabla($tabla);
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                id_schema integer NOT NULL,
                id_cama uuid DEFAULT gen_random_uuid() PRIMARY KEY,
                id_habitacion integer NOT NULL,
                descripcion text NOT NULL,
                larga boolean,
                vip boolean
                ); ";

        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->user_orbix";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun');
    }

    public function eliminar_du_camas()
    {
        $this->addPermisoGlobal('comun');

        $tabla = "du_camas";
        $nom_tabla = $this->getNomTabla($tabla);
        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('comun');
    }

}
    