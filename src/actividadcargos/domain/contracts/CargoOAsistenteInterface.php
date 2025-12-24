<?php

namespace src\actividadcargos\domain\contracts;

interface CargoOAsistenteInterface
{

    public function getCargoOAsistente(int $iid_nom): array;

    public function getSolapes(array $cPersonas, array $cActividades): array;
}