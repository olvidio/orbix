<?php

namespace src\personas\domain;

/* No vale el underscore en el nombre */

use src\personas\application\repositories\SituacionRepository;
use src\shared\domain\DatosInfoRepo;

class InfoSituacion extends DatosInfoRepo
{

    public function __construct()
    {
        $this->setTxtTitulo(_("tipos de situación"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar este tipo de situación?"));
        $this->setTxtBuscar(_("buscar un tipo de situación"));
        $this->setTxtExplicacion();

        $this->setClase('src\\personas\\domain\\entity\\Situacion');
        $this->setMetodoGestor('getSituaciones');
    }

    public function getColeccion()
    {
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        if (empty($this->k_buscar)) {
            $aWhere = array('_ordre' => 'situacion');
            $aOperador = [];
        } else {
            $aWhere = array('situacion' => $this->k_buscar);
            $aOperador = array('situacion' => 'sin_acentos');
        }
        $oLista = new SituacionRepository();
        $Coleccion = $oLista->getSituaciones($aWhere, $aOperador);

        return $Coleccion;
    }
}