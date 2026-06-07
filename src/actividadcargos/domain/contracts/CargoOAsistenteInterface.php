<?php

namespace src\actividadcargos\domain\contracts;

use src\actividadcargos\domain\entity\CargoOAsistente;

interface CargoOAsistenteInterface
{
    /**
     * @return list<CargoOAsistente>
     */
    public function getCargoOAsistente(int $iid_nom): array;

    /**
     * @param iterable<\src\personas\domain\entity\PersonaGlobal|\src\personas\domain\entity\PersonaSacd> $cPersonas
     * @param iterable<\src\actividades\domain\entity\ActividadAll> $cActividades
     * @return array<int, list<int>>
     */
    public function getSolapes(iterable $cPersonas, iterable $cActividades): array;
}
