<?php

namespace cartaspresentacion\db;

use core\ConfigGlobal;
use devel\model\DBAbstract;

/**
 * crear las tablas necesarias para el esquema.
 * Heredadas de [global] En este caso public
 */
class DBEsquema extends DBAbstract
{

    private $dir_base = ConfigGlobal::DIR . "/apps/cartaspresentacion/db";

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

    public function dropAll()
    {
        $this->eliminar_presentacion();
    }

    public function createAll()
    {
        $this->create_presentacion();
        // NO crear las tablas en la DBSelect para la sincronización.
        // Las tablas están en el servidor interno
    }

    public function llenarAll()
    {
    }

    protected function infoTable($tabla)
    {
        $datosTabla = [];
        switch ($tabla) {
            case "du_presentacion":
                $datosTabla['tabla'] = "du_presentacion_dl";
                $nom_tabla = $this->getNomTabla("du_presentacion_dl");
                $campo_seq = '';
                $id_seq = $nom_tabla . "_" . $campo_seq . "_seq";
                break;
        }
        $datosTabla['nom_tabla'] = $nom_tabla;
        $datosTabla['campo_seq'] = $campo_seq;
        $datosTabla['id_seq'] = $id_seq;
        $datosTabla['filename'] = $this->dir_base . "/$tabla.csv";
        return $datosTabla;
    }

    public function create_presentacion()
    {
        // OJO Corresponde al esquema sf/sv, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"' . $this->esquema . '"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('sfsv');

        $tabla = "du_presentacion";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];
        $id_seq = $datosTabla['id_seq'];

        $nom_tabla_parent = 'public';
        if ($this->vf === 'v') {
            $nom_tabla_parent = 'publicv';
        }
        if ($this->vf === 'f') {
            $nom_tabla_parent = 'publicf';
        }
        $nompkey = $tabla . '_pkey';
        /* Los constraint de 'primary key' y 'foreign key' deben estar en la creación de la tabla,
         *  que permite la clausula 'IF EXISTS'.  De otro modo da error cuando se está activando un módulo
         *  que ya había sido instalado y se había desactivado, pero no borrado.
         */

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                        CONSTRAINT $nompkey PRIMARY KEY (id_ubi, id_direccion)
                )
            INHERITS ($nom_tabla_parent.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role; ";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('sfsv');
        // Devolver los valores al estado original
        $this->esquema = $esquema_org;
        $this->role = $role_org;
    }

    public function eliminar_presentacion()
    {
        // OJO Corresponde al esquema sf/sv, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"' . $this->esquema . '"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('sfsv');

        $datosTabla = $this->infoTable("du_presentacion");

        $nom_tabla = $datosTabla['nom_tabla'];

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('sfsv');
        // Devolver los valores al estado original
        $this->esquema = $esquema_org;
        $this->role = $role_org;
    }

}