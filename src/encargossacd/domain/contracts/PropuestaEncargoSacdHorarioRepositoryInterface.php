<?php

namespace src\encargossacd\domain\contracts;

use src\encargossacd\domain\entity\EncargoSacdHorario;

interface PropuestaEncargoSacdHorarioRepositoryInterface
{
    /**
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<EncargoSacdHorario>
     */
    public function getEncargoSacdHorarios(array $aWhere = [], array $aOperators = []): array;

    public function crearTabla(): bool;

    public function borrarTabla(): bool;

    public function Guardar(EncargoSacdHorario $horario): bool;

    public function cambiarSacd(int $id_enc, int $id_sacd_old, int $id_sacd_new): bool;

    public function getNewId(): int;

    public function getErrorTxt(): string;
}
