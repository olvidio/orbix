<?php

namespace src\ubis\domain;

/* No vale el underscore en el nombre */

use src\shared\domain\DatosInfoRepo;
use src\ubis\application\repositories\DelegacionRepository;

class InfoDelegaciones extends DatosInfoRepo
{
    public function __construct()
    {
        $this->setTxtTitulo(_("delegaciones"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar esta delegación?"));
        $this->setTxtBuscar(_("buscar una delegación (sigla)"));
        $this->setTxtExplicacion();

        $this->setClase('src\\ubis\\domain\\entity\\Delegacion');
        $this->setMetodoGestor('getDelegaciones');
    }

    public function getColeccion()
    {
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        if (empty($this->k_buscar)) {
            $aWhere = ['_ordre' => 'region'];
            $aOperador = [];
        } else {
            $aWhere = ['dl' => $this->k_buscar];
            $aOperador = ['dl' => 'sin_acentos'];
        }
        $oLista = new DelegacionRepository();
        $Coleccion = $oLista->getDelegaciones($aWhere, $aOperador);

        return $Coleccion;
    }
}
