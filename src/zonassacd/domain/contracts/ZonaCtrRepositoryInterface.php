<?php

declare(strict_types=1);

namespace src\zonassacd\domain\contracts;

interface ZonaCtrRepositoryInterface
{
    /**
     * @return list<int>
     */
    public function idUbisDeZona(int $id_zona): array;

    public function zonaDeCentro(int $id_ubi): ?int;

    /**
     * @param list<int> $idUbis
     * @return array<int, int> id_ubi => id_zona
     */
    public function mapaZonaPorCentro(array $idUbis): array;

    /** `null` borra la fila (centro sin zona). */
    public function asignar(int $id_ubi, ?int $id_zona): bool;

    public function eliminarPorZona(int $id_zona): int;

    public function getErrorTxt(): string;
}
