<?php

namespace src\dbextern\domain\contracts;

use src\dbextern\domain\entity\IdMatchPersona;
use src\shared\domain\criteria\Criteria;

interface IdMatchPersonaRepositoryInterface
{
    /**
     * @return list<IdMatchPersona>
     */
    public function matching(Criteria $criteria): array;

    public function Eliminar(IdMatchPersona $IdMatchPersona): bool;

    public function Guardar(IdMatchPersona $IdMatchPersona): bool;

    public function getErrorTxt(): string;

    public function getNomTabla(): string;

    /**
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_listas): array|false;

    public function findById(int $id_listas): ?IdMatchPersona;
}
