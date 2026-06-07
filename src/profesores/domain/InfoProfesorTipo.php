<?php

namespace src\profesores\domain;

/* No vale el underscore en el nombre */

use src\profesores\domain\contracts\ProfesorTipoRepositoryInterface;
use src\shared\domain\DatosInfoRepo;

class InfoProfesorTipo extends DatosInfoRepo
{

    public function __construct(
        private ProfesorTipoRepositoryInterface $profesorTipoRepository,
    ) {
        $this->setTxtTitulo(_("tipos de profesores"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar este tipo de profesor?"));
        $this->setTxtBuscar(_("buscar un tipo de profesor"));
        $this->setTxtExplicacion();

        $this->setClase('src\\profesores\\domain\\entity\\ProfesorTipo');
        $this->setMetodoGestor('getProfesorTipos');

        $this->setRepositoryInterface(ProfesorTipoRepositoryInterface::class);
    }

    /**
     * @return list<object>
     */
    public function getColeccion(): array
    {
        $aWhere = [];
        $aOperador = [];
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        if (empty($this->k_buscar)) {
            $aWhere = array('_ordre' => 'tipo_profesor');
            $aOperador = [];
        } else {
            $aWhere = array('tipo_profesor' => $this->k_buscar);
            $aOperador = array('tipo_profesor' => 'sin_acentos');
        }
        return $this->profesorTipoRepository->getProfesorTipos($aWhere, $aOperador);
    }
}