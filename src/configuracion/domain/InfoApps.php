<?php

namespace src\configuracion\domain;

/* No vale el underscore en el nombre */

use src\configuracion\application\repositories\AppRepository;
use src\shared\domain\DatosInfoRepo;

class InfoApps extends DatosInfoRepo
{

    public function __construct()
    {
        $this->setTxtTitulo(_("aplicaciones"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar esta aplicación?"));
        $this->setTxtBuscar(_("buscar una aplicación"));
        $this->setTxtExplicacion();

        $this->setClase('src\\configuracion\\domain\\entity\\App');
        $this->setMetodoGestor('getApps');
    }

    public function getColeccion()
    {
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        if (empty($this->k_buscar)) {
            $aWhere = array('_ordre' => 'nom');
            $aOperador = [];
        } else {
            $aWhere = array('nom' => $this->k_buscar);
            $aOperador = array('nom' => 'sin_acentos');
        }
        $oLista = new AppRepository();
        $Coleccion = $oLista->getApps($aWhere, $aOperador);

        return $Coleccion;
    }
}
