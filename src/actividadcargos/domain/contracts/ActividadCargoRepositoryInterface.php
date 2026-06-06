<?php

namespace src\actividadcargos\domain\contracts;

use src\actividadcargos\domain\entity\ActividadCargo;

/**
 * Interfaz de la clase ActividadCargo y su Repositorio
 */
interface ActividadCargoRepositoryInterface
{
    /**
     * @return list<int>
     */
    public function getActividadIdSacds(int $iid_activ): array;

    /**
     * @return list<\src\personas\domain\entity\Persona>
     */
    public function getActividadSacds(int $iid_activ): array;

    /**
     * @param array<string, mixed> $aWhereNom
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return array<string, ActividadCargo>
     */
    public function getActividadCargosDeAsistente(array $aWhereNom, array $aWhere = [], array $aOperators = []): array;

    /**
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperador
     * @param array<string, mixed> $aWhereAct
     * @param array<string, string> $aOperadorAct
     * @return array<int, array<string, mixed>>
     */
    public function getAsistenteCargoDeActividad(array $aWhere, array $aOperador = [], array $aWhereAct = [], array $aOperadorAct = []): array;

    /**
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperador
     * @param array<string, mixed> $aWhereAct
     * @param array<string, string> $aOperadorAct
     * @return array<int, array<string, mixed>>
     */
    public function getCargoDeActividad(array $aWhere, array $aOperador = [], array $aWhereAct = [], array $aOperadorAct = []): array;

    /**
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<ActividadCargo>
     */
    public function getActividadCargos(array $aWhere = [], array $aOperators = []): array;

    public function Eliminar(ActividadCargo $ActividadCargo): bool;

    public function Guardar(ActividadCargo $ActividadCargo): bool;

    public function getErrorTxt(): string;

    public function getNomTabla(): string;

    /**
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_item): array|false;

    public function findById(int $id_item): ?ActividadCargo;

    public function getNewId(): int;
}
