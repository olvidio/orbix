<?php

namespace profesores\model;

use core;

/* No vale el underscore en el nombre */

class Info1012 extends core\datosInfo
{

    public function __construct()
    {
        $this->setTxtTitulo(_("dossier de publicaciones"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar esta publicación?"));
        $this->setTxtBuscar();
        $this->setTxtExplicacion();

        $this->setClase('profesores\\model\\entity\\ProfesorPublicacion');
        $this->setMetodoGestor('getProfesorPublicaciones');
        $this->setPau('p');
    }

    public function getId_dossier()
    {
        return 1012;
    }

    public function getColeccion()
    {
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        if (!empty($this->id_pau)) {
            $aWhere['id_nom'] = $this->id_pau;
        }
        if (empty($this->k_buscar)) {
            $aWhere['_ordre'] = 'f_publicacion DESC';
            $aOperador = '';
        } else {
            $aWhere['titulo'] = $this->k_buscar;
            $aOperador['titulo'] = 'sin_acentos';
        }
        $oLista = new entity\GestorProfesorPublicacion();
        $Coleccion = $oLista->getProfesorPublicaciones($aWhere, $aOperador);

        return $Coleccion;
    }
}