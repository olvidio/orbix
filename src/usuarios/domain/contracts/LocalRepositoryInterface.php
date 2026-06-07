<?php

namespace src\usuarios\domain\contracts;

use src\usuarios\domain\entity\Local;

/**
 * Interfaz de la clase Local y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 24/4/2025
 */
interface LocalRepositoryInterface
{
    /**
     * @return array<int|string, string>
     */
    public function getArrayLocales(): array;
    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Local
     *
     * @param array<string, mixed> $aWhere asociativo con los valores para cada campo de la BD.
     * @param array<string, string> $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return list<Local> Una colección de objetos de tipo Local
     */
    public function getLocales(array $aWhere = [], array $aOperators = []): array;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Local $Local): bool;

    public function Guardar(Local $Local): bool;

    public function getErrorTxt(): string;



    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param string $id_locale
     * @return array<string, mixed>|false
     */
    public function datosById(string $id_locale): array|false;

    /**
     * Busca la clase con id_locale en el repositorio.
     */
    public function findById(string $id_locale): ?Local;
}