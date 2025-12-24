<?php

namespace src\zonassacd\domain;

use src\shared\domain\DatosInfoRepo;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;

/* No vale el underscore en el nombre */

class InfoZona extends DatosInfoRepo
{

    public function __construct()
    {
        $this->setTxtTitulo(_("zonas de misas"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar esta zona?"));
        $this->setTxtBuscar(_("zona a buscar"));
        $this->setTxtExplicacion();

        $this->setClase('src\\zonassacd\\domain\\entity\\Zona');
        $this->setMetodoGestor('getZonas');

        $this->setRepositoryInterface(ZonaRepositoryInterface::class);
    }

    public function getColeccion()
    {
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        if (empty($this->k_buscar)) {
            $aWhere = array('_ordre' => 'orden');
            $aOperador = [];
        } else {
            $aWhere = array('nom' => $this->k_buscar);
            $aOperador = array('nom' => 'sin_acentos');
        }
        $oLista = $GLOBALS['container']->get($this->getRepositoryInterface());
        $Coleccion = $oLista->getZonas($aWhere, $aOperador);

        return $Coleccion;
    }
}