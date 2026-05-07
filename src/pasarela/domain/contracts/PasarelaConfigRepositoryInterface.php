<?php

namespace src\pasarela\domain\contracts;

use src\pasarela\domain\entity\PasarelaConfig;

interface PasarelaConfigRepositoryInterface
{

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo ConfigSchema
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|bool Una colección de objetos de tipo ConfigSchema
     */
    public function getPasarelaConfigs(array $aWhere = [], array $aOperators = []): array|bool;

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
     * @return array|bool
     */
    public function datosById(string $nom_parametro): array|bool;

    /**
     * Busca la clase con parametro en el repositorio.
     */
    public function findById(string $nom_parametro): ?PasarelaConfig;
}