<?php

namespace profesores\model;

use core;

/* No vale el underscore en el nombre */

class Info1019 extends core\DatosInfo
{

    public function __construct()
    {
        $this->setTxtTitulo(_("dossier de ampliación de docencia del studium generale"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar esta fila?"));
        $this->setTxtBuscar(_("todos"));
        $this->setTxtExplicacion();

        $this->setClase('profesores\\model\\entity\\ProfesorAmpliacion');
        $this->setMetodoGestor('getProfesorAmpliaciones');
        $this->setPau('p');
    }

    public function getId_dossier()
    {
        return 1019;
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
        $oLista = new entity\GestorProfesorAmpliacion();
        $Coleccion = $oLista->getProfesorAmpliaciones($aWhere, $aOperador);

        return $Coleccion;
    }
}