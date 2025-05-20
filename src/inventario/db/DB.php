<?php

namespace src\inventario\db;

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
        $this->vf = substr($esquema_sfsv, -1); // solo la v o la f.

        $this->role = '"' . $role . '"';
        $this->role_vf = '"' . $esquema_sfsv . '"';

        $this->esquema = 'global';
    }

    public function dropAll()
    {
        $this->eliminar_whereis();
        $this->eliminar_ubis();
        $this->eliminar_tipo_documento();
        $this->eliminar_lugares();
        $this->eliminar_equipajes();
        $this->eliminar_egm();
        $this->eliminar_documentos();
        $this->eliminar_colecciones();
    }

    public function createAll()
    {
        $this->create_colecciones();
        $this->create_documentos();
        $this->create_egm();
        $this->create_equipajes();
        $this->create_lugares();
        $this->create_tipo_documento();
        $this->create_ubis();
        $this->create_whereis();
    }

    /**
     * En el esquema sv
     *   OJO Corresponde al esquema sf/sv, no al comun.
     */
    public function create_colecciones()
    {
        $this->addPermisoGlobal('sfsv');
        $tabla = "i_colecciones";
        $nom_tabla = $this->getNomTabla($tabla);
        $a_sql = [];

        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
            id_schema integer NOT NULL,
            id_coleccion integer NOT NULL,
            nom_coleccion text NOT NULL,
            agrupar boolean DEFAULT false
            );";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->user_orbix";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('sfsv');
    }


    public function eliminar_colecciones()
    {
        $this->addPermisoGlobal('sfsv');
        $tabla = "i_colecciones";
        $nom_tabla = $this->getNomTabla($tabla);

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('sfsv');
    }

    /**
     * En el esquema sfsv
     */
    public function create_documentos()
    {
        $this->addPermisoGlobal('sfsv');
        $tabla = "i_documentos";
        $nom_tabla = $this->getNomTabla($tabla);
        $a_sql = [];

        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
            id_schema integer NOT NULL,
            id_doc integer NOT NULL,
            id_tipo_doc integer NOT NULL,
            id_ubi integer NOT NULL,
            id_lugar integer,
            f_recibido date,
            f_asignado date,
            observ text,
            observ_ctr text,
            f_ult_comprobacion date,
            en_busqueda boolean DEFAULT false,
            perdido boolean DEFAULT false,
            f_perdido date,
            eliminado boolean DEFAULT false,
            f_eliminado date,
            num_reg integer,
            num_ini integer,
            num_fin integer,
            identificador text,
            num_ejemplares integer
            );";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->user_orbix";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('sfsv');
    }


    public function eliminar_documentos()
    {
        $this->addPermisoGlobal('sfsv');
        $tabla = "i_documentos";
        $nom_tabla = $this->getNomTabla($tabla);

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('sfsv');
    }

    /**
     * En el esquema sv
     *   OJO Corresponde al esquema sf/sv, no al comun.
     */
    public function create_egm()
    {
        $this->addPermisoGlobal('sfsv');
        $tabla = "i_egm";
        $nom_tabla = $this->getNomTabla($tabla);
        $a_sql = [];

        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
            id_schema integer NOT NULL,
            id_item integer NOT NULL,
            id_equipaje integer,
            id_grupo integer,
            id_lugar integer,
            texto text
            );";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->user_orbix";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('sfsv');
    }


    public function eliminar_egm()
    {
        $this->addPermisoGlobal('sfsv');
        $tabla = "i_egm";
        $nom_tabla = $this->getNomTabla($tabla);

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('sfsv');
    }

    /**
     * En el esquema sv
     *   OJO Corresponde al esquema sf/sv, no al comun.
     */
    public function create_equipajes()
    {
        $this->addPermisoGlobal('sfsv');
        $tabla = "i_equipajes";
        $nom_tabla = $this->getNomTabla($tabla);
        $a_sql = [];

        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
            id_schema integer NOT NULL,
            id_equipaje integer NOT NULL,
            ids_activ text,
            lugar text,
            f_ini date,
            f_fin date,
            id_ubi_activ integer,
            nom_equipaje text,
            cabecera text,
            pie text,
            cabecerab text,
            firma text
            );";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->user_orbix";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('sfsv');
    }


    public function eliminar_equipajes()
    {
        $this->addPermisoGlobal('sfsv');
        $tabla = "i_equipajes";
        $nom_tabla = $this->getNomTabla($tabla);

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('sfsv');
    }

    /**
     * En el esquema sv
     *   OJO Corresponde al esquema sf/sv, no al comun.
     */
    public function create_lugares()
    {
        $this->addPermisoGlobal('sfsv');
        $tabla = "i_lugares";
        $nom_tabla = $this->getNomTabla($tabla);
        $a_sql = [];

        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
            id_schema integer NOT NULL,
            id_lugar integer NOT NULL,
            id_ubi integer NOT NULL,
            nom_lugar text NOT NULL
            );";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->user_orbix";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('sfsv');
    }


    public function eliminar_lugares()
    {
        $this->addPermisoGlobal('sfsv');
        $tabla = "i_lugares";
        $nom_tabla = $this->getNomTabla($tabla);

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('sfsv');
    }

    /**
     * En el esquema sv
     *   OJO Corresponde al esquema sf/sv, no al comun.
     */
    public function create_tipo_documento()
    {
        $this->addPermisoGlobal('sfsv');
        $tabla = "i_tipo_documento";
        $nom_tabla = $this->getNomTabla($tabla);
        $a_sql = [];

        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
            id_schema integer NOT NULL,
            id_tipo_doc integer NOT NULL,
            nom_doc text,
            sigla text NOT NULL,
            observ text,
            id_coleccion integer,
            bajo_llave boolean DEFAULT false,
            vigente boolean DEFAULT true,
            numerado boolean DEFAULT false NOT NULL
            );";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->user_orbix";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('sfsv');
    }


    public function eliminar_tipo_documento()
    {
        $this->addPermisoGlobal('sfsv');
        $tabla = "i_tipo_documento";
        $nom_tabla = $this->getNomTabla($tabla);

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('sfsv');
    }

    /**
     * En el esquema sv
     *   OJO Corresponde al esquema sf/sv, no al comun.
     */
    public function create_ubis()
    {
        $this->addPermisoGlobal('sfsv');
        $tabla = "i_ubis";
        $nom_tabla = $this->getNomTabla($tabla);
        $a_sql = [];

        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
            id_schema integer NOT NULL,
            id_ubi integer NOT NULL,
            nom_ubi text NOT NULL,
            id_ubi_activ integer
            );";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->user_orbix";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('sfsv');
    }


    public function eliminar_ubis()
    {
        $this->addPermisoGlobal('sfsv');
        $tabla = "i_ubis";
        $nom_tabla = $this->getNomTabla($tabla);

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('sfsv');
    }

    /**
     * En el esquema sv
     *   OJO Corresponde al esquema sf/sv, no al comun.
     */
    public function create_whereis()
    {
        $this->addPermisoGlobal('sfsv');
        $tabla = "i_whereis";
        $nom_tabla = $this->getNomTabla($tabla);
        $a_sql = [];

        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
            id_schema integer NOT NULL,
            id_item_whereis integer NOT NULL,
            id_item_egm integer,
            id_doc integer
            );";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->user_orbix";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('sfsv');
    }


    public function eliminar_whereis()
    {
        $this->addPermisoGlobal('sfsv');
        $tabla = "i_whereis";
        $nom_tabla = $this->getNomTabla($tabla);

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('sfsv');
    }


}