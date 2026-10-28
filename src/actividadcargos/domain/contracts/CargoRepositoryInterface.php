<?php

namespace src\actividadcargos\domain\contracts;

use src\actividadcargos\domain\entity\Cargo;
use src\shared\domain\criteria\Criteria;

/**
 * Interfaz de la clase Cargo y su Repositorio
 */
interface CargoRepositoryInterface
{
    /**
     * @return list<int>
     */
    public function getArrayIdCargosSacd(): array;

    /**
     * @return array<int|string, string>
     */
    public function getArrayCargos(string $tipo_cargo = ''): array;

    /**
     * Cargos que cumplen el criterio.
     *
     * @return list<Cargo>
     */
    public function matching(Criteria $criteria): array;

    /**
     * @deprecated usar matching(Criteria)
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<Cargo>
     */
    public function getCargos(array $aWhere = [], array $aOperators = []): array;

    public function Eliminar(Cargo $Cargo): bool;

    public function Guardar(Cargo $Cargo): bool;

    public function getErrorTxt(): string;

    public function getNomTabla(): string;

    /**
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_cargo): array|false;

    public function findById(int $id_cargo): ?Cargo;

    public function getNewId(): int;
}
