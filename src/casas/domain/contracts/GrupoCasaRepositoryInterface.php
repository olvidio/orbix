<?php

namespace src\casas\domain\contracts;

use src\casas\domain\entity\GrupoCasa;

interface GrupoCasaRepositoryInterface
{
    /**
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<GrupoCasa>
     */
    public function getGrupoCasas(array $aWhere = [], array $aOperators = []): array;

    public function Eliminar(GrupoCasa $GrupoCasa): bool;

    public function Guardar(GrupoCasa $GrupoCasa): bool;

    public function getErrorTxt(): string;

    public function getNomTabla(): string;

    /**
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_item): array|false;

    public function findById(int $id_item): ?GrupoCasa;

    public function getNewId(): int;
}
