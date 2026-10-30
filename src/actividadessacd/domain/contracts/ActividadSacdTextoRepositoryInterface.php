<?php

namespace src\actividadessacd\domain\contracts;

use src\shared\domain\criteria\Criteria;
use src\actividadessacd\domain\entity\ActividadSacdTexto;

interface ActividadSacdTextoRepositoryInterface
{
    /**
     * @return list<ActividadSacdTexto>
     */
    public function matching(Criteria $criteria): array;

    public function Eliminar(ActividadSacdTexto $ActividadSacdTexto): bool;

    public function Guardar(ActividadSacdTexto $ActividadSacdTexto): bool;

    public function getErrorTxt(): string;

    public function getNomTabla(): string;

    /**
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_item): array|false;

    public function findById(int $id_item): ?ActividadSacdTexto;

    public function getNewId(): int|string;
}
