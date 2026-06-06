<?php

namespace src\asignaturas\domain;

/* No vale el underscore en el nombre */

use src\asignaturas\domain\contracts\AsignaturaTipoRepositoryInterface;
use src\asignaturas\domain\entity\AsignaturaTipo;
use src\shared\domain\DatosInfoRepo;

class InfoAsignaturaTipo extends DatosInfoRepo
{
    public function __construct(
        private AsignaturaTipoRepositoryInterface $asignaturaTipoRepository,
    ) {
        $this->setTxtTitulo(_("tipos de asignaturas"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar este tipo de asignatura?"));
        $this->setTxtBuscar(_("buscar un tipo de asignatura"));
        $this->setTxtExplicacion();

        $this->setClase('src\\asignaturas\\domain\\entity\\AsignaturaTipo');
        $this->setMetodoGestor('getAsignaturaTipos');

        $this->setRepositoryInterface(AsignaturaTipoRepositoryInterface::class);
    }

    /**
     * @return list<AsignaturaTipo>
     */
    public function getColeccion(): array
    {
        if (empty($this->k_buscar)) {
            $aWhere = ['_ordre' => 'tipo_asignatura'];
            $aOperador = [];
        } else {
            $aWhere = ['tipo_asignatura' => $this->k_buscar];
            $aOperador = ['tipo_asignatura' => 'sin_acentos'];
        }

        return $this->asignaturaTipoRepository->getAsignaturaTipos($aWhere, $aOperador);
    }
}
