<?php

namespace src\actividadestudios\domain\contracts;

use src\actividadestudios\domain\entity\Matricula;
use src\shared\domain\criteria\Criteria;
use src\actividadestudios\domain\value_objects\ActividadMatriculaPk;

interface MatriculaRepositoryInterface
{
    /**
     * @return list<Matricula>
     */
    public function getMatriculasPendientes(?int $id_nom = null): array;

    /**
     * Matricula que cumplen el criterio.
     *
     * @return list<Matricula>
     */
    public function matching(Criteria $criteria): array;

    /**
     * @deprecated usar matching(Criteria)
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<Matricula>
     */
    public function getMatriculas(array $aWhere = [], array $aOperators = []): array;

    public function Eliminar(Matricula $Matricula): bool;

    public function Guardar(Matricula $Matricula): bool;

    public function getErrorTxt(): string;

    public function getNomTabla(): string;

    /**
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_activ, int $id_asignatura, int $id_nom): array|false;

    /**
     * @return array<string, mixed>|false
     */
    public function datosByPk(ActividadMatriculaPk $pk): array|false;

    public function findById(int $id_activ, int $id_asignatura, int $id_nom): ?Matricula;

    public function findByPk(ActividadMatriculaPk $pk): ?Matricula;
}
