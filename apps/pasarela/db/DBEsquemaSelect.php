<?php

namespace pasarela\db;

use core\DBRefresh;

/**
 * crear las tablas necesarias para el esquema select,
 * para permitir la sincronización.
 */
class DBEsquemaSelect extends DBEsquema
{

    public function dropAllSelect()
    {
        $this->eliminar_pasarela_dl_select();
    }

    public function createAllSelect()
    {
        $this->create_pasarela_dl_select();
        // renovar subscripciones
        $DBRefresh = new DBRefresh();
        $DBRefresh->refreshSubscriptionModulo('comun');
    }

    /**
     * En la BD Comun (esquema).
     */
    public function create_pasarela_dl_select()
    {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun_select');

        $tabla_padre = "pasarela";
        $tabla = "pasarela_dl";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];
        $nompkey = $tabla . '_pkey';
        /* Los constraint de 'primary key' y 'foreign key' deben estar en la creación de la tabla,
         *  que permite la clausula 'IF EXISTS'.  De otro modo da error cuando se está activando un módulo
         *  que ya había sido instalado y se había desactivado, pero no borrado.
         */

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                        CONSTRAINT $nompkey PRIMARY KEY (nom_parametro)
                ) 
            INHERITS (global.$tabla_padre);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";


        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun_select');
    }

    public function eliminar_pasarela_dl_select()
    {
        $datosTabla = $this->infoTable("pasarela_dl");
        $nom_tabla = $datosTabla['nom_tabla'];

        $this->eliminarDeComunSelect($nom_tabla);
    }

}