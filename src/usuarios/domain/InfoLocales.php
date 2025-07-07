<?php

namespace src\usuarios\domain;

/* No vale el underscore en el nombre */

use src\shared\domain\DatosInfoRepo;
use src\usuarios\application\repositories\LocalRepository;

class InfoLocales extends DatosInfoRepo
{

    public function __construct()
    {
        $this->setTxtTitulo(_("idiomas posibles para la aplicación"));
        $this->setTxtEliminar();
        $this->setTxtBuscar(_("idioma a buscar"));
        $this->setTxtExplicacion();

        $this->setClase('src\\usuarios\\model\\entity\\Local');
        $this->setMetodoGestor('getLocales');
    }

    public function getColeccion()
    {
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        if (empty($this->k_buscar)) {
            $aWhere = [];
            $aOperador = [];
        } else {
            $aWhere = array('nom_idioma' => $this->k_buscar);
            $aOperador = array('nom_idioma' => 'sin_acentos');
        }
        $aWhere['_ordre'] = 'activo DESC,nom_idioma ASC';
        $oLista = new LocalRepository();
        $Coleccion = $oLista->getLocales($aWhere, $aOperador);

        return $Coleccion;
    }
}
