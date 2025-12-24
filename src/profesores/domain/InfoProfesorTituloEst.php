<?php

namespace src\profesores\domain;


/* No vale el underscore en el nombre */

use src\profesores\domain\contracts\ProfesorTituloEstRepositoryInterface;
use src\shared\domain\DatosInfoRepo;

class InfoProfesorTituloEst extends DatosInfoRepo
{

    public function __construct()
    {
        $this->setTxtTitulo(_("dossier de títulos de postgrado"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar este título?"));
        $this->setTxtBuscar();
        $this->setTxtExplicacion();

        $this->setClase('src\\profesores\\domain\\entity\\ProfesorTituloEst');
        $this->setMetodoGestor('getProfesorTitulosEst');
        $this->setPau('p');

        $this->setRepositoryInterface(ProfesorTituloEstRepositoryInterface::class);
    }

    public function getId_dossier()
    {
        return 1017;
    }

    public function getColeccion()
    {
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        if (!empty($this->id_pau)) {
            $aWhere['id_nom'] = $this->id_pau;
        }
        if (empty($this->k_buscar)) {
            $aWhere['_ordre'] = 'year DESC';
            $aOperador = [];
        } else {
            $aWhere['titulo'] = $this->k_buscar;
            $aOperador['titulo'] = 'sin_acentos';
        }
        $oLista = $GLOBALS['container']->get($this->repoInterface);
        $Coleccion = $oLista->getProfesorTitulosEst($aWhere, $aOperador);

        return $Coleccion;
    }
}
