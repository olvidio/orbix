<?php

namespace src\profesores\domain;


/* No vale el underscore en el nombre */

use src\profesores\domain\contracts\ProfesorStgrRepositoryInterface;
use src\shared\domain\DatosInfoRepo;

class InfoProfesorStgr extends DatosInfoRepo
{

    public function __construct()
    {
        $this->setTxtTitulo(_("dossier de nombramientos del studium generale"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar este nombramiento?"));
        $this->setTxtBuscar(_("todos"));
        $this->setTxtExplicacion();

        $this->setClase('src\\profesores\\domain\\entity\\ProfesorStgr');
        $this->setMetodoGestor('getProfesores');
        $this->setPau('p');

        $this->setRepositoryInterface(ProfesorStgrRepositoryInterface::class);
    }

    public function getId_dossier()
    {
        return 1018;
    }

    public function getColeccion()
    {
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        if (!empty($this->id_pau)) {
            $aWhere['id_nom'] = $this->id_pau;
        }
        if (empty($this->k_buscar)) {
            $aWhere['_ordre'] = 'f_nombramiento DESC';
            $aOperador = [];
        } else {
            //$aWhere['congreso'] = $this->k_buscar;
            //$aOperador['congreso'] ='sin_acentos';
        }
        $oLista = $GLOBALS['container']->get($this->repoInterface);
        $Coleccion = $oLista->getProfesoresStgr($aWhere, $aOperador);

        return $Coleccion;
    }
}