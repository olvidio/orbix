<?php

namespace src\pasarela\domain\contracts;

use src\pasarela\domain\entity\PasarelaConfig;

interface PasarelaConfigRepositoryInterface
{

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo ConfigSchema
     *
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<PasarelaConfig>
     */
    public function getPasarelaConfigs(array $aWhere = [], array $aOperators = []): array;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(PasarelaConfig $PasarelaConfig): bool;

    public function Guardar(PasarelaConfig $PasarelaConfig): bool;

    public function getErrorTxt(): string;


    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param string $nom_parametro
     * @return array<string, mixed>|false
     */
    public function datosById(string $nom_parametro): array|false;

    /**
     * Busca la clase con parametro en el repositorio.
     */
    public function findById(string $nom_parametro): ?PasarelaConfig;
}