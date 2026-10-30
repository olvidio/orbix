<?php

namespace src\misas\domain\contracts;

use src\misas\domain\entity\EncargoCtr;
use src\misas\domain\value_objects\EncargoCtrId;
use src\shared\domain\criteria\Criteria;

interface EncargoCtrRepositoryInterface
{
    /**
     * @return list<EncargoCtr>
     */
    public function getEncargosCentro(int $id_ubi): array;

    /**
     * @return list<EncargoCtr>
     */
    public function getCentrosEncargo(int $id_enc): array;

    /**
     * EncargoCtr que cumplen el criterio.
     *
     * @return list<EncargoCtr>
     */
    public function matching(Criteria $criteria): array;

    public function Eliminar(EncargoCtr $EncargoCtr): bool;

    public function Guardar(EncargoCtr $EncargoCtr): bool;

    public function getErrorTxt(): string;

    public function getNomTabla(): string;

    /**
     * @return array<string, mixed>|false
     */
    public function datosById(EncargoCtrId $uuid_item): array|false;

    public function findById(EncargoCtrId $uuid_item): ?EncargoCtr;
}
