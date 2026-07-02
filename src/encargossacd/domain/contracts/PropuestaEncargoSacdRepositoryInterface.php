<?php

namespace src\encargossacd\domain\contracts;

use src\encargossacd\domain\entity\PropuestaEncargoSacd;

interface PropuestaEncargoSacdRepositoryInterface
{
    /**
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<PropuestaEncargoSacd>
     */
    public function getPropuestasEncargoSacd(array $aWhere = [], array $aOperators = []): array;

    public function findById(int $id_item): ?PropuestaEncargoSacd;

    public function existenLasTablas(): bool;

    public function Guardar(PropuestaEncargoSacd $propuesta): bool;

    public function Eliminar(PropuestaEncargoSacd $propuesta): bool;

    public function getNewId(): int;

    public function getErrorTxt(): string;
}
