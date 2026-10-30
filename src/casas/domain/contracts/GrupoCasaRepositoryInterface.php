<?php

namespace src\casas\domain\contracts;

use src\casas\domain\entity\GrupoCasa;
use src\shared\domain\criteria\Criteria;

interface GrupoCasaRepositoryInterface
{
    /**
     * Grupos de casas que cumplen el criterio.
     *
     * @return list<GrupoCasa>
     */
    public function matching(Criteria $criteria): array;


    public function Eliminar(GrupoCasa $GrupoCasa): bool;

    public function Guardar(GrupoCasa $GrupoCasa): bool;

    public function getErrorTxt(): string;

    public function getNomTabla(): string;

    /**
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_item): array|false;

    public function findById(int $id_item): ?GrupoCasa;

    public function getNewId(): int;
}
