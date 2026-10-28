<?php

namespace src\zonassacd\domain\contracts;

use src\shared\domain\criteria\Criteria;
use src\zonassacd\domain\entity\ZonaGrupo;

interface ZonaGrupoRepositoryInterface
{
    /**
     * @return array<int|string, string>
     */
    public function getArrayZonaGrupos(string $sCondicion = ''): array;

    /**
     * Grupos de zona que cumplen el criterio.
     *
     * @return list<ZonaGrupo>
     */
    public function matching(Criteria $criteria): array;

    /**
     * @deprecated usar matching(Criteria)
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<ZonaGrupo>
     */
    public function getZonasGrupo(array $aWhere = [], array $aOperators = []): array;

    public function Eliminar(ZonaGrupo $ZonaGrupo): bool;

    public function Guardar(ZonaGrupo $ZonaGrupo): bool;

    public function getErrorTxt(): string;

    public function getNomTabla(): string;

    /**
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_grupo): array|false;

    public function findById(int $id_grupo): ?ZonaGrupo;

    public function getNewId(): int;
}
