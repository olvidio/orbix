<?php

namespace src\asignaturas\domain\contracts;

use src\asignaturas\domain\entity\Asignatura;
use src\asignaturas\domain\value_objects\PlanEstudios;

/**
 * Interfaz de la clase Asignatura y su Repositorio
 */
interface AsignaturaRepositoryInterface
{
    /**
     * @param array<string, mixed> $aWhere
     */
    public function getJsonAsignaturas(array $aWhere): string;

    /**
     * @return array<int|string, array{nombre_asignatura: mixed, creditos: mixed}>
     */
    public function getArrayAsignaturasCreditos(): array;

    /**
     * @return array<int|string, string>
     */
    public function getArrayAsignaturasConSeparador(
        bool $op_genericas = true,
        ?int $planEstudios = PlanEstudios::PLAN_2026,
    ): array;

    public function getListaOpGenericas(string $formato = ''): string;

    /**
     * @return array<int|string, string|null>
     */
    public function getArrayAsignaturas(): array;

    /**
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     */
    public function getAsignaturasAsJson(array $aWhere = [], array $aOperators = []): string;

    /**
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<Asignatura>
     */
    public function getAsignaturas(array $aWhere = [], array $aOperators = []): array;

    public function Eliminar(Asignatura $Asignatura): bool;

    public function Guardar(Asignatura $Asignatura): bool;

    public function getErrorTxt(): string;

    public function getNomTabla(): string;

    /**
     * @return array<string, mixed>|false
     * @param list<int>|int|null $plan_estudios
     */
    public function datosById(int $id_asignatura, int|array|null $plan_estudios = null): array|false;

    /**
     * @param list<int>|int|null $plan_estudios
     */
    public function findById(int $id_asignatura, int|array|null $plan_estudios = null): ?Asignatura;

    public function getNewId(): int;
}
