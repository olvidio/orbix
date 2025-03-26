<?php

namespace inventario\model;

use core\DatosInfoRepo;
use inventario\domain\repositories\LugarRepository;
use inventario\domain\repositories\UbiInventarioRepository;

/* No vale el underscore en el nombre */

class InfoUbiInventario extends DatosInfoRepo
{

    public function __construct()
    {
        $this->setTxtTitulo(_("ubis"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar esta casa/centro?"));
        $this->setTxtBuscar();
        $this->setTxtExplicacion();

        $this->setClase('inventario\\domain\\entity\\UbiInventario');
        $this->setMetodoGestor('getUbisInventario');
        $this->setPau('p');
    }

    public function getColeccion()
    {
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        if (empty($this->k_buscar)) {
            $aWhere['_ordre'] = 'nom_ubi';
            $aOperador = [];
        } else {
            $aWhere['nom_ubi'] = $this->k_buscar;
            $aOperador['nom_ubi'] = 'sin_acentos';
        }

        $ColeccionRepository = new UbiInventarioRepository();
        $Coleccion = $ColeccionRepository->getUbisInventario($aWhere, $aOperador);

        return $Coleccion;
    }
}
