<?php

namespace src\encargossacd\domain\contracts;

use src\encargossacd\domain\entity\PropuestaEncargoSacd;
use src\shared\domain\criteria\Criteria;

interface PropuestaEncargoSacdRepositoryInterface
{
    /**
     * PropuestaEncargoSacd que cumplen el criterio.
     *
     * @return list<PropuestaEncargoSacd>
     */
    public function matching(Criteria $criteria): array;

    public function findById(int $id_item): ?PropuestaEncargoSacd;

    public function existenLasTablas(): bool;

    public function Guardar(PropuestaEncargoSacd $propuesta): bool;

    public function Eliminar(PropuestaEncargoSacd $propuesta): bool;

    public function getNewId(): int;

    public function getErrorTxt(): string;
}
