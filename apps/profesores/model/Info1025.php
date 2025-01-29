<?php

namespace profesores\model;


/* No vale el underscore en el nombre */

use core\DatosInfo;

class Info1025 extends DatosInfo
{

    public function __construct()
    {
        $this->setTxtTitulo(_("dossier de actividad docente"));
        $this->setTxtEliminar();
        $this->setTxtBuscar(_("todos"));
        $this->setTxtExplicacion();

        $this->setClase('profesores\\model\\entity\\ProfesorDocenciaStgr');
        $this->setMetodoGestor('getProfesorDocenciasStgr');
        $this->setPau('p');
    }

    public function getId_dossier()
    {
        return 1025;
    }

    public function getColeccion()
    {
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        if (!empty($this->id_pau)) {
            $aWhere['id_nom'] = $this->id_pau;
        }
        if (empty($this->k_buscar)) {
            $aWhere['_ordre'] = 'curso_inicio DESC';
            $aOperador = '';
        } else {
            //$aWhere['congreso'] = $this->k_buscar;
            //$aOperador['congreso'] ='sin_acentos';
        }
        $oLista = new entity\GestorProfesorDocenciaStgr();
        $Coleccion = $oLista->getProfesorDocenciasStgr($aWhere, $aOperador);

        return $Coleccion;
    }
}