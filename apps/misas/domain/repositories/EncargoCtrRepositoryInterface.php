<?php

namespace misas\domain\repositories;

use misas\domain\EncargoCtrId;
use misas\domain\entity\EncargoCtr;
use PDO;

/**
 * Interfaz de la clase EncargoDia y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 20/3/2023
 */
interface EncargoCtrRepositoryInterface
{

    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo EncargoCtr
     *
     * @param int $id_ubi
     * @return array|FALSE Una colección de objetos de tipo EncargoCtr
     */
    public function getEncargosCentro(int $id_ubi): array|false;

    /**
     * devuelve una colección (array) de objetos de tipo EncargoCtr
     *
     * @param int $id_enc
     * @return array|FALSE Una colección de objetos de tipo EncargoCtr
     */
    public function getCentrosEncargo(int $id_enc): array|false;

    /**
     * devuelve una colección (array) de objetos de tipo EncargoCtr
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|FALSE Una colección de objetos de tipo EncargoCtr
     */
    public function getEncargosCentros(array $aWhere = [], array $aOperators = []): array|false;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(EncargoCtr $EncargoCtr): bool;

    public function Guardar(EncargoCtr $EncargoCtr): bool;

    public function getErrorTxt(): string;

    public function getoDbl(): PDO;

    public function setoDbl(PDO $oDbl): void;

    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param EncargoCtrId $uuid_item
     * @return array|bool
     */
    public function datosById(EncargoCtrId $uuid_item): array|bool;

    /**
     * Busca la clase con id_item en el repositorio.
     */
    public function findById(EncargoCtrId $uuid_item): ?EncargoCtr;

}