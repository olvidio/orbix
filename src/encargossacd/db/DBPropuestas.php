<?php

namespace src\encargossacd\db;

use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\persistence\postgresql\DBRefresh;
use src\utils_database\domain\entity\DBAbstract;

/**
 * Tablas staging de propuestas de encargos SACD (sv-e y sv-e_select).
 */
class DBPropuestas extends DBAbstract
{
    public function __construct(?string $esquema_sfsv = null)
    {
        if ($esquema_sfsv === null || $esquema_sfsv === '') {
            $esquema_sfsv = ConfigGlobal::mi_region_dl();
        }
        $this->esquema = substr($esquema_sfsv, 0, -1);
        $this->role = '"' . $this->esquema . '"';
        $this->role_vf = '"' . $esquema_sfsv . '"';
    }

    public function createAll(): void
    {
        $this->eliminar_propuesta_encargo_sacd_horario();
        $this->eliminar_propuesta_encargos_sacd();
        $this->create_propuesta_encargos_sacd();
        $this->create_propuesta_encargo_sacd_horario();

        if (DBAbstract::hasServerSelect()) {
            $this->eliminar_propuesta_encargo_sacd_horario_select();
            $this->eliminar_propuesta_encargos_sacd_select();
            $this->create_propuesta_encargos_sacd_select();
            $this->create_propuesta_encargo_sacd_horario_select();

            $DBRefresh = new DBRefresh();
            $DBRefresh->refreshSubscriptionModulo('sv-e');
        }
    }

    public function eliminarAll(): void
    {
        $this->eliminar_propuesta_encargo_sacd_horario();
        $this->eliminar_propuesta_encargos_sacd();

        if (DBAbstract::hasServerSelect()) {
            $this->eliminar_propuesta_encargo_sacd_horario_select();
            $this->eliminar_propuesta_encargos_sacd_select();
        }
    }

    public function create_propuesta_encargos_sacd(): void
    {
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"' . $this->esquema . '"';
        $this->addPermisoGlobal('sfsv-e');

        $nom_tabla = $this->getNomTabla('propuesta_encargos_sacd');
        $nom_tabla_enc = $this->getNomTabla('encargos_sacd');
        $campo_seq = 'id_item';
        $id_seq = $nom_tabla . "_" . $campo_seq . "_seq";

        $a_sql = [];
        $a_sql[] = "CREATE TABLE $nom_tabla AS
                SELECT id_schema, id_item, id_enc, id_nom, modo, f_ini, f_fin, id_nom AS id_nom_new
                FROM $nom_tabla_enc WHERE f_fin IS NULL";
        $a_sql[] = "CREATE SEQUENCE IF NOT EXISTS $id_seq";
        $a_sql[] = "ALTER SEQUENCE $id_seq
                    INCREMENT BY 1
                    MINVALUE 1
                    MAXVALUE 9223372036854775807
                    START WITH 1
                    NO CYCLE";
        $a_sql[] = "ALTER SEQUENCE $id_seq OWNER TO $this->role";
        $a_sql[] = "ALTER TABLE $nom_tabla ALTER $campo_seq SET DEFAULT nextval('$id_seq'::regclass)";
        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        $a_sql[] = "ALTER TABLE $nom_tabla ADD CONSTRAINT propuesta_encargos_sacd_ukey UNIQUE ($campo_seq)";
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY ($campo_seq)";
        $a_sql[] = "ALTER TABLE $nom_tabla ADD CONSTRAINT propuesta_encargos_sacd_id_enc_ukey
                    UNIQUE (id_enc, id_nom_new, modo, f_ini)";
        $a_sql[] = "SELECT setval('$id_seq', COALESCE((SELECT MAX($campo_seq)+1 FROM $nom_tabla), 1), FALSE)
                    FROM information_schema.key_column_usage
                    WHERE constraint_name LIKE '%pkey%'
                    ORDER BY table_name";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";

        $this->executeSql($a_sql);
        $this->delPermisoGlobal('sfsv-e');
        $this->esquema = $esquema_org;
        $this->role = $role_org;
    }

    public function eliminar_propuesta_encargos_sacd(): void
    {
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"' . $this->esquema . '"';
        $this->addPermisoGlobal('sfsv-e');

        $nom_tabla = $this->getNomTabla('propuesta_encargos_sacd');
        $campo_seq = 'id_item';
        $id_seq = $nom_tabla . "_" . $campo_seq . "_seq";

        $a_sql = [];
        $a_sql[] = "DROP SEQUENCE IF EXISTS $id_seq CASCADE";
        $a_sql[] = "DROP TABLE IF EXISTS $nom_tabla CASCADE";
        $this->executeSql($a_sql);

        $this->delPermisoGlobal('sfsv-e');
        $this->esquema = $esquema_org;
        $this->role = $role_org;
    }

    public function create_propuesta_encargo_sacd_horario(): void
    {
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"' . $this->esquema . '"';
        $this->addPermisoGlobal('sfsv-e');

        $nom_tabla = $this->getNomTabla('propuesta_encargo_sacd_horario');
        $nom_tabla_prop = $this->getNomTabla('propuesta_encargos_sacd');
        $nom_tabla_hor = $this->getNomTabla('encargo_sacd_horario');
        $campo_seq = 'id_item';
        $id_seq = $nom_tabla . "_" . $campo_seq . "_seq";

        $a_sql = [];
        $a_sql[] = "CREATE TABLE $nom_tabla AS (
            SELECT h.*
            FROM $nom_tabla_hor h JOIN $nom_tabla_prop e ON (h.id_item_tarea_sacd = e.id_item)
            WHERE h.f_fin IS NULL )";
        $a_sql[] = "CREATE SEQUENCE IF NOT EXISTS $id_seq";
        $a_sql[] = "ALTER SEQUENCE $id_seq
                    INCREMENT BY 1
                    MINVALUE 1
                    MAXVALUE 9223372036854775807
                    START WITH 1
                    NO CYCLE";
        $a_sql[] = "ALTER SEQUENCE $id_seq OWNER TO $this->role";
        $a_sql[] = "ALTER TABLE $nom_tabla ALTER $campo_seq SET DEFAULT nextval('$id_seq'::regclass)";
        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        $a_sql[] = "ALTER TABLE $nom_tabla ADD CONSTRAINT propuesta_encargo_sacd_horario_ukey UNIQUE ($campo_seq)";
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY (id_enc, id_item, id_nom)";
        $a_sql[] = "ALTER TABLE ONLY $nom_tabla ADD CONSTRAINT propuesta_encargo_sacd_horario_id_item_tarea_sacd_fkey
                     FOREIGN KEY (id_item_tarea_sacd) REFERENCES $nom_tabla_prop(id_item) ON DELETE CASCADE";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";

        $this->executeSql($a_sql);
        $this->delPermisoGlobal('sfsv-e');
        $this->esquema = $esquema_org;
        $this->role = $role_org;
    }

    public function eliminar_propuesta_encargo_sacd_horario(): void
    {
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"' . $this->esquema . '"';
        $this->addPermisoGlobal('sfsv-e');

        $nom_tabla = $this->getNomTabla('propuesta_encargo_sacd_horario');
        $campo_seq = 'id_item';
        $id_seq = $nom_tabla . "_" . $campo_seq . "_seq";

        $a_sql = [];
        $a_sql[] = "DROP SEQUENCE IF EXISTS $id_seq CASCADE";
        $a_sql[] = "DROP TABLE IF EXISTS $nom_tabla CASCADE";
        $this->executeSql($a_sql);

        $this->delPermisoGlobal('sfsv-e');
        $this->esquema = $esquema_org;
        $this->role = $role_org;
    }

    public function create_propuesta_encargos_sacd_select(): void
    {
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"' . $this->esquema . '"';
        $this->addPermisoGlobal('sfsv-e_select');

        $nom_tabla = $this->getNomTabla('propuesta_encargos_sacd');
        $nom_tabla_enc = $this->getNomTabla('encargos_sacd');
        $campo_seq = 'id_item';

        $a_sql = [];
        $a_sql[] = "CREATE TABLE $nom_tabla AS
                SELECT id_schema, id_item, id_enc, id_nom, modo, f_ini, f_fin, id_nom AS id_nom_new
                FROM $nom_tabla_enc WHERE false";
        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        $a_sql[] = "ALTER TABLE $nom_tabla ADD CONSTRAINT propuesta_encargos_sacd_pkey PRIMARY KEY ($campo_seq)";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";

        $this->executeSql($a_sql);
        $this->delPermisoGlobal('sfsv-e_select');
        $this->esquema = $esquema_org;
        $this->role = $role_org;
    }

    public function eliminar_propuesta_encargos_sacd_select(): void
    {
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"' . $this->esquema . '"';
        $this->addPermisoGlobal('sfsv-e_select');

        $nom_tabla = $this->getNomTabla('propuesta_encargos_sacd');

        $a_sql = ["DROP TABLE IF EXISTS $nom_tabla CASCADE"];
        $this->executeSql($a_sql);

        $this->delPermisoGlobal('sfsv-e_select');
        $this->esquema = $esquema_org;
        $this->role = $role_org;
    }

    public function create_propuesta_encargo_sacd_horario_select(): void
    {
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"' . $this->esquema . '"';
        $this->addPermisoGlobal('sfsv-e_select');

        $nom_tabla = $this->getNomTabla('propuesta_encargo_sacd_horario');
        $nom_tabla_hor = $this->getNomTabla('encargo_sacd_horario');
        $nom_tabla_prop = $this->getNomTabla('propuesta_encargos_sacd');

        $a_sql = [];
        $a_sql[] = "CREATE TABLE $nom_tabla AS (
            SELECT h.*
            FROM $nom_tabla_hor h JOIN $nom_tabla_prop e ON (h.id_item_tarea_sacd = e.id_item)
            WHERE false )";
        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        $a_sql[] = "ALTER TABLE $nom_tabla ADD CONSTRAINT propuesta_encargo_sacd_horario_pkey PRIMARY KEY (id_enc, id_item, id_nom)";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";

        $this->executeSql($a_sql);
        $this->delPermisoGlobal('sfsv-e_select');
        $this->esquema = $esquema_org;
        $this->role = $role_org;
    }

    public function eliminar_propuesta_encargo_sacd_horario_select(): void
    {
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"' . $this->esquema . '"';
        $this->addPermisoGlobal('sfsv-e_select');

        $nom_tabla = $this->getNomTabla('propuesta_encargo_sacd_horario');

        $a_sql = ["DROP TABLE IF EXISTS $nom_tabla CASCADE"];
        $this->executeSql($a_sql);

        $this->delPermisoGlobal('sfsv-e_select');
        $this->esquema = $esquema_org;
        $this->role = $role_org;
    }
}
