<?php

namespace profesores\model;

use core;

/* No vale el underscore en el nombre */

class Info1024 extends core\datosInfo
{

    public function __construct()
    {
        $this->setTxtTitulo(_("congresos a los que ha asistido una persona"));
        $this->setTxtEliminar();
        $this->setTxtBuscar();
        $this->setTxtExplicacion();

        $this->setClase('profesores\\model\\entity\\ProfesorCongreso');
        $this->setMetodoGestor('getProfesorCongresos');
        $this->setPau('p');
    }

    public function getId_dossier()
    {
        return 1024;
    }

    public function getColeccion()
    {
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        if (!empty($this->id_pau)) {
            $aWhere['id_nom'] = $this->id_pau;
        }
        if (empty($this->k_buscar)) {
            $aWhere['_ordre'] = 'f_ini DESC';
            $aOperador = '';
        } else {
            $aWhere['congreso'] = $this->k_buscar;
            $aOperador['congreso'] = 'sin_acentos';
        }
        $oLista = new entity\GestorProfesorCongreso();
        $Coleccion = $oLista->getProfesorCongresos($aWhere, $aOperador);

        return $Coleccion;
    }
}