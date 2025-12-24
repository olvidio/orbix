<?php

namespace src\profesores\domain;


/* No vale el underscore en el nombre */

use src\profesores\domain\contracts\ProfesorJuramentoRepositoryInterface;
use src\shared\domain\DatosInfoRepo;

class InfoProfesorJuaramento extends DatosInfoRepo
{

    public function __construct()
    {
        $this->setTxtTitulo(_("dossier juramento del studium generale"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar esto?"));
        $this->setTxtBuscar(_("todos"));
        $this->setTxtExplicacion();

        $this->setClase('src\\profesores\\domain\\entity\\ProfesorJuramento');
        $this->setMetodoGestor('getProfesorJuramentos');
        $this->setPau('p');

        $this->setRepositoryInterface(ProfesorJuramentoRepositoryInterface::class);
    }

    public function getId_dossier()
    {
        return 1021;
    }

    public function getColeccion()
    {
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        if (!empty($this->id_pau)) {
            $aWhere['id_nom'] = $this->id_pau;
        }
        if (empty($this->k_buscar)) {
            $aWhere['_ordre'] = 'f_juramento DESC';
            $aOperador = [];
        } else {
            //$aWhere['congreso'] = $this->k_buscar;
            //$aOperador['congreso'] ='sin_acentos';
        }
        $oLista = $GLOBALS['container']->get($this->repoInterface);
        $Coleccion = $oLista->getProfesorJuramentos($aWhere, $aOperador);

        return $Coleccion;
    }
}