<?php

namespace tablonanuncios\db;

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
        $this->eliminar_tablon_anuncios();
    }

    public function createAll()
    {
        $this->create_tablon_anuncios();
    }

    /**
     * En el esquema sv
     *   OJO Corresponde al esquema sf/sv, no al comun.
     */
    public function create_tablon_anuncios()
    {
        $this->addPermisoGlobal('sfsv');

        $tabla = "tablon_anuncios";
        $nom_tabla = $this->getNomTabla($tabla);
        $a_sql = [];
        // longitud campo certificado igual al de acta
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                    uuid_item uuid NOT NULL,
                    usuario_creador varchar(20),
                    esquema_emisor varchar(20) NOT NULL,
                    esquema_destino varchar(20),
                    texto_anuncio text NOT NULL,
                    idioma varchar(12),
                    tablon text,
                    tanotado timestamp NOT NULL,
                    teliminado timestamp,
                    categoria smallint
                    );";

        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->user_orbix";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('sfsv');
    }

    public function eliminar_tablon_anuncios()
    {
        $this->addPermisoGlobal('sfsv');

        $tabla = "tablon_anuncios";
        $nom_tabla = $this->getNomTabla($tabla);
        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('sfsv');
    }

}