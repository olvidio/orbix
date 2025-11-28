<?php

namespace src\inventario\domain\contracts;

use PDO;
use src\inventario\domain\entity\TipoDoc;
use src\inventario\domain\value_objects\TipoDocId;

/**
 * Interfaz de la clase TipoDoc y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 12/3/2025
 */
interface TipoDocRepositoryInterface
{

    public function getArrayTipoDoc(): array;

    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo TipoDoc
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo TipoDoc
     */
    public function getTipoDocs(array $aWhere = [], array $aOperators = []): array|false;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(TipoDoc $TipoDoc): bool;

    public function Guardar(TipoDoc $TipoDoc): bool;

    public function getErrorTxt(): string;

    public function getoDbl(): PDO;

    public function setoDbl(PDO $oDbl): void;

    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param TipoDocId $id_tipo_doc
     * @return array|bool
     */
    public function datosById(TipoDocId $id_tipo_doc): array|bool;

    /**
     * Busca la clase con id_tipo_doc en el repositorio.
     */
    public function findById(TipoDocId $id_tipo_doc): ?TipoDoc;

    public function getNewId();
}