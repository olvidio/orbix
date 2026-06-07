<?php

namespace src\misas\domain\contracts;

use src\misas\domain\entity\Plantilla;

interface PlantillaRepositoryInterface
{
    /**
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<Plantilla>
     */
    public function getPlantillas(array $aWhere = [], array $aOperators = []): array;

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
