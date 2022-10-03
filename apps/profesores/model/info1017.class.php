<?php

namespace profesores\model;

use core;

/* No vale el underscore en el nombre */

class Info1017 extends core\datosInfo
{

    public function __construct()
    {
        $this->setTxtTitulo(_("dossier de títulos de postgrado"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar este título?"));
        $this->setTxtBuscar();
        $this->setTxtExplicacion();

        $this->setClase('profesores\\model\\entity\\TituloEst');
        $this->setMetodoGestor('getTitulosEst');
        $this->setPau('p');
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
            $aOperador = '';
        } else {
            $aWhere['titulo'] = $this->k_buscar;
            $aOperador['titulo'] = 'sin_acentos';
        }
        $oLista = new entity\GestorTituloEst();
        $Coleccion = $oLista->getTitulosEst($aWhere, $aOperador);

        return $Coleccion;
    }
}
