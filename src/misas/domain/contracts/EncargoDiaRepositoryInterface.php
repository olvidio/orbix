<?php

namespace src\misas\domain\contracts;

use src\misas\domain\entity\EncargoDia;
use src\misas\domain\value_objects\EncargoDiaId;
use src\shared\domain\criteria\Criteria;

interface EncargoDiaRepositoryInterface
{
    /**
     * EncargoDia que cumplen el criterio.
     *
     * @return list<EncargoDia>
     */
    public function matching(Criteria $criteria): array;

    public function Eliminar(EncargoDia $EncargoDia): bool;

    public function Guardar(EncargoDia $EncargoDia): bool;

    public function getErrorTxt(): string;

    public function getNomTabla(): string;

    /**
     * @return array<string, mixed>|false
     */
    public function datosById(EncargoDiaId $vo): array|false;

    public function findById(EncargoDiaId $vo): ?EncargoDia;
}
