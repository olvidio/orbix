<?php

namespace src\dossiers\domain\contracts;

use PDO;
use src\dossiers\domain\entity\TipoDossier;
use src\dossiers\domain\value_objects\TipoDossierId;


/**
 * Interfaz de la clase TipoDossier y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 2/12/2025
 */
interface TipoDossierRepositoryInterface
{
    /* -------------------- NUEVOS MÉTODOS basados en Value Objects --------- */

    /**
     * Busca la clase con id_tipo_dossier en el repositorio usando VO.
     */
    public function findByIdVo(TipoDossierId $id): ?TipoDossier;

    /**
     * Devuelve los campos de la base de datos en un array asociativo usando VO.
     * Devuelve false si no existe la fila en la base de datos
     */
    public function datosByIdVo(TipoDossierId $id): array|bool;

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo TipoDossier
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo TipoDossier
     */
    public function getTiposDossiers(array $aWhere = [], array $aOperators = []): array|false;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(TipoDossier $TipoDossier): bool;

    public function Guardar(TipoDossier $TipoDossier): bool;

    public function getErrorTxt(): string;

    public function getoDbl(): PDO;

    public function setoDbl(PDO $oDbl): void;

    public function getNomTabla(): string;

    public function datosById(int $id_tipo_dossier): array|bool;

    public function findById(int $id_tipo_dossier): ?TipoDossier;
}