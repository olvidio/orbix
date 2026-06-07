<?php

namespace src\encargossacd\domain\contracts;

use src\encargossacd\domain\entity\EncargoHorario;


/**
 * Interfaz de la clase EncargoHorario y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 23/12/2025
 */
interface EncargoHorarioRepositoryInterface
{

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo EncargoHorario
     *
     * @param array<string, mixed> $aWhere asociativo con los valores para cada campo de la BD.
     * @param array<string, string> $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return list<EncargoHorario> Una colección de objetos de tipo EncargoHorario
     */
    public function getEncargoHorarios(array $aWhere = [], array $aOperators = []): array;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(EncargoHorario $EncargoHorario): bool;

    public function Guardar(EncargoHorario $EncargoHorario): bool;

    public function getErrorTxt(): string;



    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_item_h
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_item_h): array|false;

    /**
     * Busca la clase con id_item_h en el repositorio.
     */
    public function findById(int $id_item_h): ?EncargoHorario;

    public function getNewId(): int;
}