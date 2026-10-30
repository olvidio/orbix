<?php

namespace src\asignaturas\domain\contracts;

use src\asignaturas\domain\entity\Sector;
use src\shared\domain\criteria\Criteria;

/**
 * Interfaz de la clase Sector y su Repositorio
 */
interface SectorRepositoryInterface
{
    /**
     * @return array<int|string, list<int|string>>
     */
    public function getArraySectoresPorDepartamento(): array;

    /**
     * @return array<int|string, string>
     */
    public function getArraySectores(): array;

    /**
     * Sector que cumplen el criterio.
     *
     * @return list<Sector>
     */
    public function matching(Criteria $criteria): array;

    public function Eliminar(Sector $Sector): bool;

    public function Guardar(Sector $Sector): bool;

    public function getErrorTxt(): string;

    public function getNomTabla(): string;

    /**
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_sector): array|false;

    public function findById(int $id_sector): ?Sector;

    public function getNewId(): int;
}
