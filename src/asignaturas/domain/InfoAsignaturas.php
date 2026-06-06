<?php

namespace src\asignaturas\domain;

use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\entity\Asignatura;
use src\shared\domain\DatosInfoRepo;

/* No vale el underscore en el nombre */

class InfoAsignaturas extends DatosInfoRepo
{
    public function __construct(
        private AsignaturaRepositoryInterface $asignaturaRepository,
    ) {
        $this->setTxtTitulo(_("asignaturas"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar esta asignatura?"));
        $this->setTxtBuscar(_("buscar en nombre largo"));
        $this->setTxtExplicacion();

        $this->setClase('src\\asignaturas\\domain\\entity\\Asignatura');
        $this->setMetodoGestor('getAsignaturas');

        $this->setRepositoryInterface(AsignaturaRepositoryInterface::class);
    }

    /**
     * @return list<Asignatura>
     */
    public function getColeccion(): array
    {
        $aWhere = [];
        $aOperador = [];
        if (!empty($this->k_buscar)) {
            $aWhere['nombre_asignatura'] = $this->k_buscar;
            $aOperador['nombre_asignatura'] = 'sin_acentos';
        }
        $aWhere['id_asignatura'] = 3000;
        $aOperador['id_asignatura'] = '<';
        $aWhere['_ordre'] = 'id_nivel';

        return $this->asignaturaRepository->getAsignaturas($aWhere, $aOperador);
    }
}
