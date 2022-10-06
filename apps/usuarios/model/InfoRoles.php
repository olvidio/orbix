<?php

namespace usuarios\model;

use core;

/* No vale el underscore en el nombre */

class InfoRoles extends core\datosInfo
{

    public function __construct()
    {
        $this->setTxtTitulo(_("tipos de rol que puede tener un usuario"));
        $this->setTxtEliminar();
        $this->setTxtBuscar(_("rol a buscar"));
        $this->setTxtExplicacion();

        $this->setClase('usuarios\\model\\entity\\Role');
        $this->setMetodoGestor('getRoles');
    }

    public function getColeccion()
    {
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        if (empty($this->k_buscar)) {
            $aWhere = array();
            $aOperador = array();
        } else {
            $aWhere = array('role' => $this->k_buscar);
            $aOperador = array('role' => 'sin_acentos');
        }
        $aWhere['id_role'] = 3; // para asegurarme que no se borra.
        $aOperador['id_role'] = '>'; // para asegurarme que no se borra.
        $oLista = new entity\GestorRole();
        $Coleccion = $oLista->getRoles($aWhere, $aOperador);

        return $Coleccion;
    }
}
