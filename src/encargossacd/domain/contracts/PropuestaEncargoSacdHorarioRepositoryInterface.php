<?php

namespace src\encargossacd\domain\contracts;

use src\encargossacd\domain\entity\EncargoSacdHorario;
use src\shared\domain\criteria\Criteria;

interface PropuestaEncargoSacdHorarioRepositoryInterface
{
    /**
     * EncargoSacdHorario que cumplen el criterio.
     *
     * @return list<EncargoSacdHorario>
     */
    public function matching(Criteria $criteria): array;

    /**
     * @deprecated usar matching(Criteria)
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<EncargoSacdHorario>
     */
    public function getEncargoSacdHorarios(array $aWhere = [], array $aOperators = []): array;

    public function Guardar(EncargoSacdHorario $horario): bool;

    public function cambiarSacd(int $id_enc, int $id_sacd_old, int $id_sacd_new): bool;

    public function getNewId(): int;

    public function getErrorTxt(): string;
}
