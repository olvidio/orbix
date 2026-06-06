<?php

namespace src\asignaturas\domain\contracts;

use src\asignaturas\domain\entity\AsignaturaTipo;

/**
 * Interfaz de la clase AsignaturaTipo y su Repositorio
 */
interface AsignaturaTipoRepositoryInterface
{
    /**
     * @return array<int|string, string>
     */
    public function getArrayAsignaturaTipos(): array;

    /**
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<AsignaturaTipo>
     */
    public function getAsignaturaTipos(array $aWhere = [], array $aOperators = []): array;

    public function Eliminar(AsignaturaTipo $AsignaturaTipo): bool;

    public function Guardar(AsignaturaTipo $AsignaturaTipo): bool;

    public function getErrorTxt(): string;

    public function getNomTabla(): string;

    /**
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_tipo): array|false;

    public function findById(int $id_tipo): ?AsignaturaTipo;
}
