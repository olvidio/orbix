<?php

namespace src\inventario\domain;

use src\inventario\application\repositories\LugarRepository;
use src\shared\DatosInfoRepo;

/* No vale el underscore en el nombre */

class InfoLugar extends DatosInfoRepo
{

    public function __construct()
    {
        $this->setTxtTitulo(_("centro o casa"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar esta casa/centro?"));
        $this->setTxtBuscar();
        $this->setTxtExplicacion();

        $this->setClase('src\\inventario\\domain\\entity\\Lugar');
        $this->setMetodoGestor('getLugares');
        $this->setPau('p');
    }

    public function getColeccion()
    {
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        if (empty($this->k_buscar)) {
            $aWhere['_ordre'] = 'nom_lugar';
            $aOperador = [];
        } else {
            $aWhere['nom_lugar'] = $this->k_buscar;
            $aOperador['nom_lugar'] = 'sin_acentos';
        }

        $ColeccionRepository = new LugarRepository();
        $Coleccion = $ColeccionRepository->getLugares($aWhere, $aOperador);

        return $Coleccion;
    }
}
