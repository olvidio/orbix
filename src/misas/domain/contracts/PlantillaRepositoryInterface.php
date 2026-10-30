<?php

namespace src\misas\domain\contracts;

use src\misas\domain\entity\Plantilla;
use src\shared\domain\criteria\Criteria;

interface PlantillaRepositoryInterface
{
    /**
     * Plantilla que cumplen el criterio.
     *
     * @return list<Plantilla>
     */
    public function matching(Criteria $criteria): array;

    public function Eliminar(Plantilla $Plantilla): bool;

    public function Guardar(Plantilla $Plantilla): bool;

    public function getErrorTxt(): string;

    public function getNomTabla(): string;

    /**
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_item): array|false;

    public function findById(int $id_item): ?Plantilla;

    public function getNewId(): int;
}
