<?php

namespace src\encargossacd\domain\contracts;

use PDO;
use src\encargossacd\domain\entity\EncargoTipo;


/**
 * Interfaz de la clase EncargoTipo y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 23/12/2025
 */
interface EncargoTipoRepositoryInterface
{

   public function id_tipo_encargo($grupo, $nom_tipo): string ;

   public function encargo_de_tipo($id_tipo_enc): array ;

    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo EncargoTipo
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo EncargoTipo
     */
    public function getEncargoTipos(array $aWhere = [], array $aOperators = []): array|false;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(EncargoTipo $EncargoTipo): bool;

    public function Guardar(EncargoTipo $EncargoTipo): bool;

    public function getErrorTxt(): string;

    public function getoDbl(): PDO;

    public function setoDbl(PDO $oDbl): void;

    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_tipo_enc
     * @return array|bool
     */
    public function datosById(int $id_tipo_enc): array|bool;

    /**
     * Busca la clase con id_tipo_enc en el repositorio.
     */
    public function findById(int $id_tipo_enc): ?EncargoTipo;
}