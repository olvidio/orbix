<?php

namespace profesores\model;


/* No vale el underscore en el nombre */

use core\DatosInfo;

class Info1018 extends DatosInfo
{

    public function __construct()
    {
        $this->setTxtTitulo(_("dossier de nombramientos del studium generale"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar este nombramiento?"));
        $this->setTxtBuscar(_("todos"));
        $this->setTxtExplicacion();

        $this->setClase('profesores\\model\\entity\\Profesor');
        $this->setMetodoGestor('getProfesores');
        $this->setPau('p');
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
            $aOperador = '';
        } else {
            //$aWhere['congreso'] = $this->k_buscar;
            //$aOperador['congreso'] ='sin_acentos';
        }
        $oLista = new entity\GestorProfesor();
        $Coleccion = $oLista->getProfesores($aWhere, $aOperador);

        return $Coleccion;
    }
}