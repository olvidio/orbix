<?php

namespace src\personas\domain;

use src\personas\domain\contracts\TrasladoRepositoryInterface;
use src\shared\domain\DatosInfoRepo;

/* No vale el underscore en el nombre */

class InfoTraslado extends DatosInfoRepo
{

    public function __construct()
    {
        $this->setTxtTitulo(_("dossier de traslados"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar este traslado?"));
        $this->setTxtBuscar();
        $this->setTxtExplicacion();

        $this->setClase('src\\personas\\domain\\entity\\Traslado');
        $this->setMetodoGestor('getTraslados');
        $this->setPau('p');

        $this->setRepositoryInterface(TrasladoRepositoryInterface::class);
    }

    public function getId_dossier()
    {
        return 1004;
    }

    public function getColeccion()
    {
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        if (!empty($this->id_pau)) {
            $aWhere['id_nom'] = $this->id_pau;
        }
        if (empty($this->k_buscar)) {
            $aWhere['_ordre'] = 'f_traslado';
            $aOperador = [];
        } else {
            //$aWhere['f_ini'] = $this->k_buscar;
        }


        $oLista = $GLOBALS['container']->get($this->repoInterface);
        $Coleccion = $oLista->getTraslados($aWhere, $aOperador);

        return $Coleccion;
    }

}