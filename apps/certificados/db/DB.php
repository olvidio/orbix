<?php

namespace certificados\db;

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

        $this->esquema = 'public';
    }

    public function dropAll()
    {
        $this->eliminar_e_certificados();
    }

    public function createAll()
    {
        $this->create_e_certificados();
    }

    /**
     * En el esquema sv
     *   OJO Corresponde al esquema sf/sv, no al comun.
     */
    public function create_e_certificados()
    {
        $this->addPermisoGlobal('sfsv');

        $tabla = "e_certificados";
        $nom_tabla = $this->getNomTabla($tabla);
        $a_sql = [];
        // longitud campo certificado igual al de acta
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                    id_schema integer NOT NULL,
                    id_item integer,
                    id_nom integer,
                    nom text,
                    idioma varchar(12),
                    destino text,
                    certificado varchar(50),
                    f_certificado date,
                    propio bool,
                    firmado bool,
                    documento bytea
                    );";

        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->user_orbix";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('sfsv');
    }

    public function eliminar_e_certificados()
    {
        $this->addPermisoGlobal('sfsv');

        $tabla = "e_certificados";
        $nom_tabla = $this->getNomTabla($tabla);
        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('sfsv');
    }

}