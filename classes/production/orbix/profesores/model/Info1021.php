<?php

namespace profesores\model;


/* No vale el underscore en el nombre */

use core\DatosInfo;

class Info1021 extends DatosInfo
{

    public function __construct()
    {
        $this->setTxtTitulo(_("dossier juramento del studium generale"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar esto?"));
        $this->setTxtBuscar(_("todos"));
        $this->setTxtExplicacion();

        $this->setClase('profesores\\model\\entity\\ProfesorJuramento');
        $this->setMetodoGestor('getProfesorJuramentos');
        $this->setPau('p');
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
            $aOperador = '';
        } else {
            //$aWhere['congreso'] = $this->k_buscar;
            //$aOperador['congreso'] ='sin_acentos';
        }
        $oLista = new entity\GestorProfesorJuramento();
        $Coleccion = $oLista->getProfesorJuramentos($aWhere, $aOperador);

        return $Coleccion;
    }
}