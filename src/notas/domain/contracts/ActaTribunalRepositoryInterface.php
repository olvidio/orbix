<?php

namespace src\notas\domain\contracts;

use src\notas\domain\entity\ActaTribunal;


/**
 * Interfaz de la clase ActaTribunalDl y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 27/12/2025
 */
interface ActaTribunalRepositoryInterface
{

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo ActaTribunalDl
     *
     * @param array<string, mixed> $aWhere asociativo con los valores para cada campo de la BD.
     * @param array<string, string> $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return list<\src\notas\domain\entity\ActaTribunal> Una colección de objetos de tipo ActaTribunal
     */
    /** @param array<string, mixed> $aWhere */

    /**
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<\src\notas\domain\entity\ActaTribunal>
     */
    public function getActasTribunales(array $aWhere = [], array $aOperators = []): array;

    /**
     * Autocomplete JSON de examinadores para actas recientes.
     */
    public function getJsonExaminadores(string $sQuery = ''): string;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(ActaTribunal $ActaTribunal): bool;

    public function Guardar(ActaTribunal $ActaTribunal): bool;

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
    public function findById(int $id_item): ?ActaTribunal;

}