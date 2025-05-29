<?php

namespace src\inventario\domain;

use src\inventario\application\repositories\TipoDocRepository;
use src\shared\DatosInfoRepo;

/* No vale el underscore en el nombre */

class InfoTipoDoc extends DatosInfoRepo
{

    public function __construct()
    {
        $this->setTxtTitulo(_("tipo de documentos"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar esta tipo de documento?"));
        $this->setTxtBuscar(_("buscar en 'detalle'"));
        $this->setTxtExplicacion();

        $this->setClase('src\\inventario\\domain\\entity\\TipoDoc');
        $this->setMetodoGestor('getTipoDocs');
        $this->setPau('p');
    }

    public function getColeccion()
    {
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        if (empty($this->k_buscar)) {
            $aWhere['_ordre'] = 'nom_doc';
            $aOperador = [];
        } else {
            $aWhere['nom_doc'] = $this->k_buscar;
            $aOperador['nom_doc'] = 'sin_acentos';
        }

        $ColeccionRepository = new TipoDocRepository();
        $Coleccion = $ColeccionRepository->getTipoDocs($aWhere, $aOperador);

        return $Coleccion;
    }
}
