<?php

namespace certificados\db;

use core\ConfigGlobal;
use devel\model\DBAbstract;
use ubis\model\entity\GestorDelegacion;

/**
 * crear las tablas necesarias para el esquema.
 * Heredadas de [global] En este caso public
 */
class DBEsquema extends DBAbstract
{

    public function __construct($esquema_sfsv = NULL)
    {
        if (empty($esquema_sfsv)) {
            $esquema_sfsv = ConfigGlobal::mi_region_dl();
        }
        $this->esquema = substr($esquema_sfsv, 0, -1); // quito la v o la f.
        $this->vf = substr($esquema_sfsv, -1); // solo la v o la f.
        $this->role = '"' . $this->esquema . '"';
        $this->role_vf = '"' . $esquema_sfsv . '"';
    }

    public function dropAll(): void
    {
        $a_reg = explode('-', $this->esquema);
        $dl = $a_reg[1];
        $gesDelegeacion = new GestorDelegacion();
        if ($gesDelegeacion->soy_region_stgr($dl)) {
            $this->eliminar_e_certificados_emitidos();
        }
        // caso especial H-Hv
        if ($this->esquema !== 'H-H') {
            $this->eliminar_e_certificados_recibidos();
        }
    }

    public function createAll(): void
    {
        $a_reg = explode('-', $this->esquema);
        $dl = $a_reg[1];
        $gesDelegeacion = new GestorDelegacion();
        if ($gesDelegeacion->soy_region_stgr($dl)) {
            $this->create_e_certificados_emitidos();
        }
        // caso especial H-Hv
        if ($this->esquema !== 'H-H') {
            $this->create_e_certificados_recibidos();
        }
    }

    public function llenarAll(): void
    {
    }

    private function infoTable($tabla)
    {
        $datosTabla = [];
        switch ($tabla) {
            case "e_certificados_emitidos":
                $datosTabla['tabla'] = "e_certificados_rstgr";
                $nom_tabla = $this->getNomTabla("e_certificados_rstgr");
                $campo_seq = 'id_item';
                $id_seq = $nom_tabla . "_" . $campo_seq . "_seq";
                break;
            case "e_certificados_recibidos":
                $datosTabla['tabla'] = "e_certificados_dl";
                $nom_tabla = $this->getNomTabla("e_certificados_dl");
                $campo_seq = 'id_item';
                $id_seq = $nom_tabla . "_" . $campo_seq . "_seq";
                break;
        }
        $datosTabla['nom_tabla'] = $nom_tabla;
        $datosTabla['campo_seq'] = $campo_seq;
        $datosTabla['id_seq'] = $id_seq;
        return $datosTabla;
    }

    public function create_e_certificados_emitidos(): void
    {
        // OJO Corresponde al esquema sf/sv, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;

        $nom_tabla_parent = 'public';
        if ($this->vf === 'v') {
            $nom_tabla_parent = 'publicv';
            $this->esquema .= 'v';
        }
        if ($this->vf === 'f') {
            $nom_tabla_parent = 'publicf';
            $this->esquema .= 'f';
        }
        $this->role = '"' . $this->esquema . '"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('sfsv');

        $tabla = "e_certificados_emitidos";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];
        $id_seq = $datosTabla['id_seq'];

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                )
            INHERITS ($nom_tabla_parent.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";

        //secuencia
        $a_sql[] = "CREATE SEQUENCE IF NOT EXISTS $id_seq;";
        $a_sql[] = "ALTER SEQUENCE $id_seq
                    INCREMENT BY 1
                    MINVALUE 1
                    MAXVALUE 9223372036854775807
                    START WITH 1
                    NO CYCLE;";
        $a_sql[] = "ALTER SEQUENCE $id_seq OWNER TO $this->role;";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER $campo_seq SET DEFAULT nextval('$id_seq'::regclass); ";

        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY (id_item); ";

        $a_sql[] = "ALTER TABLE $nom_tabla ADD CONSTRAINT e_certificados_rstgr_ukey
                    UNIQUE (id_nom,f_certificado); ";

        $a_sql[] = "CREATE INDEX e_certificados_rstgr_key ON $nom_tabla USING btree (certificado);";

        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role;";


        $this->executeSql($a_sql);

        $this->delPermisoGlobal('sfsv');
        // Devolver los valores al estado original
        $this->esquema = $esquema_org;
        $this->role = $role_org;
    }

    public function create_e_certificados_recibidos(): void
    {
        // OJO Corresponde al esquema sf/sv, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;

        $nom_tabla_parent = 'public';
        if ($this->vf === 'v') {
            $nom_tabla_parent = 'publicv';
            $this->esquema .= 'v';
        }
        if ($this->vf === 'f') {
            $nom_tabla_parent = 'publicf';
            $this->esquema .= 'f';
        }
        $this->role = '"' . $this->esquema . '"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('sfsv');

        $tabla = "e_certificados_recibidos";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];
        $id_seq = $datosTabla['id_seq'];

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                )
            INHERITS ($nom_tabla_parent.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";

        //secuencia
        $a_sql[] = "CREATE SEQUENCE IF NOT EXISTS $id_seq;";
        $a_sql[] = "ALTER SEQUENCE $id_seq
                    INCREMENT BY 1
                    MINVALUE 1
                    MAXVALUE 9223372036854775807
                    START WITH 1
                    NO CYCLE;";
        $a_sql[] = "ALTER SEQUENCE $id_seq OWNER TO $this->role;";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER $campo_seq SET DEFAULT nextval('$id_seq'::regclass); ";

        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY (id_item); ";

        $a_sql[] = "ALTER TABLE $nom_tabla ADD CONSTRAINT e_certificados_dl_ukey
                    UNIQUE (id_nom,f_certificado); ";

        $a_sql[] = "CREATE INDEX e_certificados_dl_key ON $nom_tabla USING btree (certificado);";

        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role;";


        $this->executeSql($a_sql);

        $this->delPermisoGlobal('sfsv');
        // Devolver los valores al estado original
        $this->esquema = $esquema_org;
        $this->role = $role_org;
    }

    public function eliminar_e_certificados_emitidos(): void
    {
        // OJO Corresponde al esquema sf/sv, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"' . $this->esquema . '"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('sfsv');

        $datosTabla = $this->infoTable("e_certificados_emitidos");

        $nom_tabla = $datosTabla['nom_tabla'];

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('sfsv');
        // Devolver los valores al estado original
        $this->esquema = $esquema_org;
        $this->role = $role_org;
    }

    public function eliminar_e_certificados_recibidos(): void
    {
        // OJO Corresponde al esquema sf/sv, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"' . $this->esquema . '"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('sfsv');

        $datosTabla = $this->infoTable("e_certificados_recibidos");

        $nom_tabla = $datosTabla['nom_tabla'];

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('sfsv');
        // Devolver los valores al estado original
        $this->esquema = $esquema_org;
        $this->role = $role_org;
    }

}