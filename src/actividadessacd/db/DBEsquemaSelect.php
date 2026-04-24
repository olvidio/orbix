<?php

namespace src\actividadessacd\db;

use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\persistence\postgresql\DBRefresh;

/**
 * crear las tablas necesarias para el esquema select,
 * para permitir la sincronización.
 */
class DBEsquemaSelect extends DBEsquema
{

    public function dropAllSelect(): void
    {
        $this->eliminar_atn_sacd_textos_select();
    }

    public function createAllSelect(): void
    {
        $this->create_atn_sacd_textos_select();
        // renovar subscripciones
        $DBRefresh = new DBRefresh();
        $DBRefresh->refreshSubscriptionModulo('sv-e');
    }

    /**
     * En la BD sv-e (esquema).
     */
    public function create_atn_sacd_textos_select(): void
    {
        // OJO Corresponde al esquema sf-e/sv-e, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"' . $this->esquema . '"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('sfsv-e_select');

        $tabla = "a_sacd_textos";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];
        $nompkey = $tabla . '_pkey';
        /* Los constraint de 'primary key' y 'foreign key' deben estar en la creación de la tabla,
         *  que permite la clausula 'IF EXISTS'.  De otro modo da error cuando se está activando un módulo
         *  que ya había sido instalado y se había desactivado, pero no borrado.
         */
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                        CONSTRAINT $nompkey PRIMARY KEY (id_item),
                        CONSTRAINT a_sacd_textos_ukey UNIQUE (idioma,clave)
                ) 
            INHERITS (global.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('sfsv-e_select');
        // Devolver los valores al estado original
        $this->esquema = $esquema_org;
        $this->role = $role_org;
    }

    public function eliminar_atn_sacd_textos_select(): void
    {
        // OJO Corresponde al esquema sf-e/sv-e, no al comun.
        $this->eliminarDeSVESelect("a_sacd_textos");
    }

}