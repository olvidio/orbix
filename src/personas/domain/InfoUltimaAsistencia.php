<?php

namespace src\personas\domain;

use src\personas\domain\contracts\UltimaAsistenciaRepositoryInterface;
use src\shared\domain\DatosInfoRepo;

/* No vale el underscore en el nombre */

class InfoUltimaAsistencia extends DatosInfoRepo
{

    public function __construct()
    {
        $this->setTxtTitulo(_("dossier de última aistencia a tipo de actividad"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar esta actividad?"));
        $this->setTxtBuscar();
        $this->setTxtExplicacion();

        $this->setClase('src\\personas\\domain\\entity\\UltimaAsistencia');
        $this->setMetodoGestor('getUltimasAsistencias');
        $this->setPau('p');

        $this->setRepositoryInterface(UltimaAsistenciaRepositoryInterface::class);
    }

    public function getId_dossier()
    {
        return 1006;
    }

    public function getColeccion()
    {
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        if (!empty($this->id_pau)) {
            $aWhere['id_nom'] = $this->id_pau;
        }
        if (empty($this->k_buscar)) {
            $aWhere['_ordre'] = 'f_ini';
            $aOperador = [];
        } else {
            //$aWhere['f_ini'] = $this->k_buscar;
        }


        $oLista = $GLOBALS['container']->get($this->repoInterface);
        $Coleccion = $oLista->getUltimasAsistencias($aWhere, $aOperador);

        return $Coleccion;
    }

}