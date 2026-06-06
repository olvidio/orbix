<?php

namespace src\asignaturas\domain;

use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\entity\Asignatura;
use src\shared\domain\DatosInfoRepo;

/* No vale el underscore en el nombre */

class InfoOpcionales extends DatosInfoRepo
{
    public function __construct(
        private AsignaturaRepositoryInterface $asignaturaRepository,
    ) {
        $this->setTxtTitulo(_("asignaturas opcionales"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar esta opcional?"));
        $this->setTxtBuscar(_("buscar una asignatura opcional"));
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
        $aOperador['id_asignatura'] = '>';
        $aWhere['_ordre'] = 'nombre_corto';

        return $this->asignaturaRepository->getAsignaturas($aWhere, $aOperador);
    }
}
