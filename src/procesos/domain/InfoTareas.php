<?php

namespace src\procesos\domain;

use src\procesos\domain\contracts\ActividadTareaRepositoryInterface;
use src\procesos\domain\criteria\ActividadTareaField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
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
            $criteria = Criteria::create()->orderBy(ActividadTareaField::DescTarea);
        } else {
            $criteria = Criteria::create()->where(Filter::matchesUnaccented(ActividadTareaField::DescTarea, $this->k_buscar));
        }

        return $this->actividadTareaRepository->matching($criteria);
    }
}
