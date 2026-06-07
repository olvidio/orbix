<?php

namespace src\procesos\domain;

use src\procesos\domain\contracts\ActividadTareaRepositoryInterface;
use src\shared\domain\DatosInfoRepo;

/* No vale el underscore en el nombre */

class InfoTareas extends DatosInfoRepo
{
    public function __construct(
        private readonly ActividadTareaRepositoryInterface $actividadTareaRepository,
    ) {
        $this->setTxtTitulo(_("Tipos de tareas que se pueden realizar en una actividad"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar esta tarea?"));
        $this->setTxtBuscar(_("tarea a buscar"));
        $this->setTxtExplicacion();

        $this->setClase('src\\procesos\\domain\\entity\\ActividadTarea');
        $this->setMetodoGestor('getActividadTareas');

        $this->setRepositoryInterface(ActividadTareaRepositoryInterface::class);
    }

    /**
     * @return list<object>
     */
    public function getColeccion(): array
    {
        if (empty($this->k_buscar)) {
            $aWhere = ['_ordre' => 'desc_tarea'];
            $aOperador = [];
        } else {
            $aWhere = ['nom' => $this->k_buscar];
            $aOperador = ['nom' => 'sin_acentos'];
        }

        return $this->actividadTareaRepository->getActividadTareas($aWhere, $aOperador);
    }
}
