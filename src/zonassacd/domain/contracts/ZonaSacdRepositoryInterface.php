<?php

namespace src\zonassacd\domain\contracts;

use src\zonassacd\domain\entity\ZonaSacd;

interface ZonaSacdRepositoryInterface
{
    /**
     * @return list<int>
     */
    public function getIdSacdsDeZona(int $iid_zona): array;

    /**
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<ZonaSacd>
     */
    public function getZonasSacds(array $aWhere = [], array $aOperators = []): array;

    public function Eliminar(ZonaSacd $ZonaSacd): bool;

    public function Guardar(ZonaSacd $ZonaSacd): bool;

    public function getErrorTxt(): string;

    public function getNomTabla(): string;

    /**
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_item): array|false;

    public function findById(int $id_item): ?ZonaSacd;

    public function getNewId(): int;
}
