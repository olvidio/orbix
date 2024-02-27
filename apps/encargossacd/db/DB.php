<?php

namespace encargossacd\db;

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
        $this->eliminar_encargos_sacd();
        $this->eliminar_encargo_sacd_observ();
        $this->eliminar_encargo_sacd_horario_excepcion();
        $this->eliminar_encargo_sacd_horario();
        $this->eliminar_encargo_horario_excepcion();
        $this->eliminar_encargo_horario();
        $this->eliminar_encargos();
        $this->eliminar_encargo_tipo();
        $this->eliminar_encargo_datos_cgi();
        $this->eliminar_encargo_textos();

    }

    public function createAll()
    {
        $this->create_encargo_tipo();
        $this->create_encargos();
        $this->create_encargo_horario();
        $this->create_encargo_horario_excepcion();
        $this->create_encargo_sacd_horario();
        $this->create_encargo_sacd_horario_excepcion();
        $this->create_encargo_sacd_observ();
        $this->create_encargos_sacd();
        $this->create_encargo_datos_cgi();
        $this->create_encargo_textos();
    }

    /**
     * En la BD sf/sv (global).
     */
    public function create_encargos_sacd()
    {
        $this->addPermisoGlobal('sfsv-e');

        $tabla = "encargos_sacd";
        $nom_tabla = $this->getNomTabla($tabla);
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                    id_schema integer NOT NULL,
                    id_item integer NOT NULL,
                    id_enc integer NOT NULL,
                    id_nom integer NOT NULL,
                    modo integer NOT NULL,
                    f_ini date NOT NULL,
                    f_fin date
                    );";

        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->user_orbix";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('sfsv-e');
    }

    public function eliminar_encargos_sacd()
    {
        $this->addPermisoGlobal('sfsv-e');

        $tabla = "encargos_sacd";
        $nom_tabla = $this->getNomTabla($tabla);
        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('sfsv-e');
    }

    public function create_encargo_tipo()
    {
        $this->addPermisoGlobal('sfsv-e');

        $tabla = "encargo_tipo";
        $nom_tabla = $this->getNomTabla($tabla);
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                    id_schema integer NOT NULL,
                    id_tipo_enc integer NOT NULL,
                    tipo_enc text NOT NULL,
                    mod_horario smallint DEFAULT 1 NOT NULL
                    );";

        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->user_orbix";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('sfsv-e');
    }

    public function eliminar_encargo_tipo()
    {
        $this->addPermisoGlobal('sfsv-e');

        $tabla = "encargo_tipo";
        $nom_tabla = $this->getNomTabla($tabla);
        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('sfsv-e');
    }

    public function create_encargos()
    {
        $this->addPermisoGlobal('sfsv-e');

        $tabla = "encargos";
        $nom_tabla = $this->getNomTabla($tabla);
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                    id_schema integer NOT NULL,
                    id_enc integer NOT NULL,
                    id_tipo_enc integer NOT NULL,
                    sf_sv smallint DEFAULT 2 NOT NULL,
                    id_ubi integer,
                    id_zona integer,
                    desc_enc character varying(150),
                    idioma_enc character varying(15),
                    desc_lugar character varying(150),
                    observ text,
                    orden smallint,
                    prioridad smallint
                    );";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->user_orbix";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('sfsv-e');
    }

    public function eliminar_encargos()
    {
        $this->addPermisoGlobal('sfsv-e');

        $tabla = "encargos";
        $nom_tabla = $this->getNomTabla($tabla);
        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('sfsv-e');
    }

    public function create_encargo_horario()
    {
        $this->addPermisoGlobal('sfsv-e');

        $tabla = "encargo_horario";
        $nom_tabla = $this->getNomTabla($tabla);
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                    id_schema integer NOT NULL,
                    id_enc integer NOT NULL,
                    id_item_h integer NOT NULL,
                    f_ini date NOT NULL,
                    f_fin date,
                    dia_ref character varying(1),
                    dia_num smallint,
                    mas_menos character varying(1),
                    dia_inc integer,
                    h_ini time without time zone,
                    h_fin time without time zone,
                    n_sacd smallint,
                    mes smallint
                    );";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->user_orbix";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('sfsv-e');
    }

    public function eliminar_encargo_horario()
    {
        $this->addPermisoGlobal('sfsv-e');

        $tabla = "encargo_horario";
        $nom_tabla = $this->getNomTabla($tabla);
        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('sfsv-e');
    }

    public function create_encargo_horario_excepcion()
    {
        $this->addPermisoGlobal('sfsv-e');

        $tabla = "encargo_horario_excepcion";
        $nom_tabla = $this->getNomTabla($tabla);
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                    id_schema integer NOT NULL,
                    id_enc integer NOT NULL,
                    id_item_ex integer NOT NULL,
                    id_item_h integer NOT NULL,
                    f_ini date NOT NULL,
                    f_fin date,
                    desc_ex text,
                    horario boolean,
                    dia_ref character varying(1),
                    dia_num smallint,
                    mas_menos character varying(1),
                    dia_inc smallint,
                    h_ini time without time zone,
                    h_fin time without time zone,
                    n_sacd smallint,
                    mes smallint
                    );";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->user_orbix";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('sfsv-e');
    }

    public function eliminar_encargo_horario_excepcion()
    {
        $this->addPermisoGlobal('sfsv-e');

        $tabla = "encargo_horario_excepcion";
        $nom_tabla = $this->getNomTabla($tabla);
        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('sfsv-e');
    }

    public function create_encargo_sacd_horario()
    {
        $this->addPermisoGlobal('sfsv-e');

        $tabla = "encargo_sacd_horario";
        $nom_tabla = $this->getNomTabla($tabla);
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                    id_schema integer NOT NULL,
                    id_item integer NOT NULL,
                    id_enc integer NOT NULL,
                    id_nom integer NOT NULL,
                    f_ini date NOT NULL,
                    f_fin date,
                    dia_ref character varying(1),
                    dia_num smallint,
                    mas_menos character varying(1),
                    dia_inc smallint,
                    h_ini time without time zone,
                    h_fin time without time zone,
                    id_item_tarea_sacd integer NOT NULL
                    );";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->user_orbix";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('sfsv-e');
    }

    public function eliminar_encargo_sacd_horario()
    {
        $this->addPermisoGlobal('sfsv-e');

        $tabla = "encargo_sacd_horario";
        $nom_tabla = $this->getNomTabla($tabla);
        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('sfsv-e');
    }

    public function create_encargo_sacd_horario_excepcion()
    {
        $this->addPermisoGlobal('sfsv-e');

        $tabla = "encargo_sacd_horario_excepcion";
        $nom_tabla = $this->getNomTabla($tabla);
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                    id_schema integer NOT NULL,
                    id_item_ex integer NOT NULL,
                    id_enc integer NOT NULL,
                    id_item_h integer NOT NULL,
                    f_ini date NOT NULL,
                    f_fin date,
                    desc_ex text,
                    horario boolean,
                    dia_ref character varying(1),
                    dia_num smallint,
                    mas_menos character varying(1),
                    dia_inc smallint,
                    h_ini time without time zone,
                    h_fin time without time zone,
                    mes smallint
                    );";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->user_orbix";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('sfsv-e');
    }

    public function eliminar_encargo_sacd_horario_excepcion()
    {
        $this->addPermisoGlobal('sfsv-e');

        $tabla = "encargo_sacd_horario_excepcion";
        $nom_tabla = $this->getNomTabla($tabla);
        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('sfsv-e');
    }

    public function create_encargo_sacd_observ()
    {
        $this->addPermisoGlobal('sfsv-e');

        $tabla = "encargo_sacd_observ";
        $nom_tabla = $this->getNomTabla($tabla);
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                    id_schema integer NOT NULL,
                    id_item integer NOT NULL,
                    id_nom integer NOT NULL,
                    observ text
                    );";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->user_orbix";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('sfsv-e');
    }

    public function eliminar_encargo_sacd_observ()
    {
        $this->addPermisoGlobal('sfsv-e');

        $tabla = "encargo_sacd_observ";
        $nom_tabla = $this->getNomTabla($tabla);
        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('sfsv-e');
    }

    public function create_encargo_datos_cgi()
    {
        $this->addPermisoGlobal('sfsv-e');

        $tabla = "encargo_datos_cgi";
        $nom_tabla = $this->getNomTabla($tabla);
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                    id_schema integer NOT NULL,
                    id_item integer NOT NULL,
                    id_ubi integer NOT NULL,
                    curso_ini_any integer NOT NULL,
                    curso_fin_any integer,
                    num_alum integer
                    );";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->user_orbix";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('sfsv-e');
    }

    public function eliminar_encargo_datos_cgi()
    {
        $this->addPermisoGlobal('sfsv-e');

        $tabla = "encargo_datos_cgi";
        $nom_tabla = $this->getNomTabla($tabla);
        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('sfsv-e');
    }

    public function create_encargo_textos()
    {
        $this->addPermisoGlobal('sfsv-e');

        $tabla = "encargo_textos";
        $nom_tabla = $this->getNomTabla($tabla);
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                    id_schema integer NOT NULL,
                    id_item integer NOT NULL,
                    idioma character varying(3) NOT NULL,
                    clave text NOT NULL,
                    texto text
                    );";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->user_orbix";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('sfsv-e');
    }

    public function eliminar_encargo_textos()
    {
        $this->addPermisoGlobal('sfsv-e');

        $tabla = "encargo_textos";
        $nom_tabla = $this->getNomTabla($tabla);
        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('sfsv-e');
    }

}
