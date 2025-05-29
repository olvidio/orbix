<?php

namespace src\inventario\domain;

use src\inventario\application\repositories\ColeccionRepository;
use src\shared\DatosInfoRepo;

/* No vale el underscore en el nombre */

class InfoColeccion extends DatosInfoRepo
{

    public function __construct()
    {
        $this->setTxtTitulo(_("colecciones"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar esta colección?"));
        $this->setTxtBuscar();
        $this->setTxtExplicacion();

        $this->setClase('src\\inventario\\domain\\entity\\Coleccion');
        $this->setMetodoGestor('getColecciones');
        $this->setPau('p');
    }

    public function getColeccion()
    {
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        if (empty($this->k_buscar)) {
            $aWhere['_ordre'] = 'nom_coleccion';
            $aOperador = [];
        } else {
            $aWhere['nom_coleccion'] = $this->k_buscar;
            $aOperador['nom_coleccion'] = 'sin_acentos';
        }

        $ColeccionRepository = new ColeccionRepository();
        $Coleccion = $ColeccionRepository->getColecciones($aWhere, $aOperador);

        return $Coleccion;
    }
}
