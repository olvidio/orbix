<?php

namespace src\asignaturas\domain\contracts;

use src\asignaturas\domain\entity\Departamento;

/**
 * Interfaz de la clase Departamento y su Repositorio
 */
interface DepartamentoRepositoryInterface
{
    /**
     * @return array<int|string, string>
     */
    public function getArrayDepartamentos(): array;

    /**
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<Departamento>
     */
    public function getDepartamentos(array $aWhere = [], array $aOperators = []): array;

    public function Eliminar(Departamento $Departamento): bool;

    public function Guardar(Departamento $Departamento): bool;

    public function getErrorTxt(): string;

    public function getNomTabla(): string;

    /**
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_departamento): array|false;

    public function findById(int $id_departamento): ?Departamento;

    public function getNewId(): int;
}
