<?php

namespace src\actividadestudios\domain\contracts;

use src\actividadestudios\domain\entity\ActividadAsignatura;
use src\actividadestudios\domain\value_objects\ActividadAsignaturaPk;

interface ActividadAsignaturaRepositoryInterface
{
    /**
     * @return array<int, array{nombre_asignatura: mixed, creditos: mixed}>
     */
    public function getAsignaturasCa(int $id_activ, string $tipo = ''): array;

    /**
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<ActividadAsignatura>
     */
    public function getActividadAsignaturas(array $aWhere = [], array $aOperators = []): array;

    public function Eliminar(ActividadAsignatura $ActividadAsignatura): bool;

    public function Guardar(ActividadAsignatura $ActividadAsignatura): bool;

    public function getErrorTxt(): string;

    public function getNomTabla(): string;

    /**
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_activ, int $id_asignatura): array|false;

    /**
     * @return array<string, mixed>|false
     */
    public function datosByPk(ActividadAsignaturaPk $pk): array|false;

    public function findById(int $id_activ, int $id_asignatura): ?ActividadAsignatura;

    public function findByPk(ActividadAsignaturaPk $pk): ?ActividadAsignatura;
}
