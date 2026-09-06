<?php

namespace zonassacd\db;

use src\shared\config\ConfigGlobal;
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

    public function dropAll(): void
    {
        $this->ejecutarDropAllGlobal(function (): void {
            $this->eliminar_zonas_ctr();
            $this->eliminar_zonas();
            $this->eliminar_zonas_grupos();
            $this->eliminar_zonas_sacd();
        });
    }

    public function createAll(): void
    {
        $this->ejecutarCreateAllGlobal(function (): void {
            $this->create_zonas();
            $this->create_zonas_grupos();
            $this->create_zonas_sacd();
            $this->create_zonas_ctr();
        });
    }

    /**
     * En la BD comun (global).
     */
    public function create_zonas(): void
    {
        $this->addPermisoGlobal($this->permisoGlobalEffective('comun'));
        $tabla = "zonas";
        $nom_tabla = $this->getNomTabla($tabla);
        $a_sql = [];

        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
            id_schema integer NOT NULL,
            id_zona integer NOT NULL,
            nombre_zona text NOT NULL,
            orden smallint,
            id_grupo integer,
            id_nom integer
            );";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->user_orbix";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal($this->permisoGlobalEffective('comun'));
    }

    public function eliminar_zonas(): void
    {
        $this->addPermisoGlobal($this->permisoGlobalEffective('comun'));
        $tabla = "zonas";
        $nom_tabla = $this->getNomTabla($tabla);
        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal($this->permisoGlobalEffective('comun'));
    }

    public function create_zonas_grupos(): void
    {
        $this->addPermisoGlobal($this->permisoGlobalEffective('comun'));
        $tabla = "zonas_grupos";
        $nom_tabla = $this->getNomTabla($tabla);
        $a_sql = [];

        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                id_schema integer NOT NULL,
                id_grupo integer NOT NULL,
                nombre_grupo text,
                orden smallint
            );";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->user_orbix";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal($this->permisoGlobalEffective('comun'));
    }

    public function eliminar_zonas_grupos(): void
    {
        $this->addPermisoGlobal($this->permisoGlobalEffective('comun'));
        $tabla = "zonas_grupos";
        $nom_tabla = $this->getNomTabla($tabla);
        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal($this->permisoGlobalEffective('comun'));
    }

    public function create_zonas_sacd(): void
    {
        $this->addPermisoGlobal($this->permisoGlobalEffective('comun'));
        $tabla = "zonas_sacd";
        $nom_tabla = $this->getNomTabla($tabla);
        $a_sql = [];

        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                id_schema integer NOT NULL,
                id_item integer NOT NULL,
                id_nom integer NOT NULL,
                id_zona smallint NOT NULL,
                propia boolean DEFAULT true NOT NULL,
                dw1 bool DEFAULT true,
                dw2 bool DEFAULT true,
                dw3 bool DEFAULT true,
                dw4 bool DEFAULT true,
                dw5 bool DEFAULT true,
                dw6 bool DEFAULT true,
                dw7 bool DEFAULT true
            );";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->user_orbix";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal($this->permisoGlobalEffective('comun'));
    }

    public function eliminar_zonas_sacd(): void
    {
        $this->addPermisoGlobal($this->permisoGlobalEffective('comun'));
        $tabla = "zonas_sacd";
        $nom_tabla = $this->getNomTabla($tabla);
        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal($this->permisoGlobalEffective('comun'));
    }

    /**
     * Relación centro-zona. Sustituye a la columna id_zona de las tablas de centros.
     */
    public function create_zonas_ctr(): void
    {
        $this->addPermisoGlobal($this->permisoGlobalEffective('comun'));
        $tabla = "zonas_ctr";
        $nom_tabla = $this->getNomTabla($tabla);
        $a_sql = [];

        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                id_schema integer NOT NULL,
                id_ubi integer NOT NULL,
                id_zona integer NOT NULL
            );";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->user_orbix";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal($this->permisoGlobalEffective('comun'));
    }

    public function eliminar_zonas_ctr(): void
    {
        $this->addPermisoGlobal($this->permisoGlobalEffective('comun'));
        $tabla = "zonas_ctr";
        $nom_tabla = $this->getNomTabla($tabla);
        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal($this->permisoGlobalEffective('comun'));
    }

}
