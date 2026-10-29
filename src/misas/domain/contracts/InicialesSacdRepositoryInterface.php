<?php

namespace src\misas\domain\contracts;

use src\misas\domain\entity\InicialesSacd;
use src\shared\domain\criteria\Criteria;

interface InicialesSacdRepositoryInterface
{
    /**
     * InicialesSacd que cumplen el criterio.
     *
     * @return list<InicialesSacd>
     */
    public function matching(Criteria $criteria): array;

    /**
     * @deprecated usar matching(Criteria)
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<InicialesSacd>
     */
    public function getInicialesSacd(array $aWhere = [], array $aOperators = []): array;

    public function Eliminar(InicialesSacd $InicialesSacd): bool;

    public function Guardar(InicialesSacd $InicialesSacd): bool;

    public function getErrorTxt(): string;

    public function getNomTabla(): string;

    /**
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_nom): array|false;

    public function findById(int $id_nom): ?InicialesSacd;
}
