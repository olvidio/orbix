<?php

namespace src\encargossacd\domain\contracts;

use src\encargossacd\domain\entity\EncargoSacd;


/**
 * Interfaz de la clase EncargoSacd y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 23/12/2025
 */
interface EncargoSacdRepositoryInterface
{

    /**
     * @param list<int> $a_Id_enc
     */
    /**
     * @param list<int> $a_Id_enc
     */
    public function deleteEncargos(array $a_Id_enc): string;
    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo EncargoSacd
     *
     * @param array<string, mixed> $aWhere asociativo con los valores para cada campo de la BD.
     * @param array<string, string> $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return list<EncargoSacd> Una colección de objetos de tipo EncargoSacd
     */
    public function getEncargosSacd(array $aWhere = [], array $aOperators = []): array;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(EncargoSacd $EncargoSacd): bool;

    public function Guardar(EncargoSacd $EncargoSacd): bool;

    public function getErrorTxt(): string;



    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_item
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_item): array|false;

    /**
     * Busca la clase con id_item en el repositorio.
     */
    public function findById(int $id_item): ?EncargoSacd;

    public function getNewId(): int;
}