<?php

namespace src\dossiers\domain\contracts;

use PDO;
use src\dossiers\domain\entity\Dossier;
use src\dossiers\domain\value_objects\DossierPk as DossierPkAlias;


/**
 * Interfaz de la clase Dossier y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 2/12/2025
 */
interface DossierRepositoryInterface
{

    public function DossiersNotEmpty($pau = '', $id = ''): array|false;

    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Dossier
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo Dossier
     */
    public function getDossieres(array $aWhere = [], array $aOperators = []): array|false;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Dossier $Dossier): bool;

    public function Guardar(Dossier $Dossier): bool;

    public function getErrorTxt(): string;

    public function getoDbl(): PDO;

    public function setoDbl(PDO $oDbl): void;

    public function getNomTabla(): string;

    /**
     * @deprecated usar datosByPkVO
     */
    public function datosById(int $id_tipo_dossier, int $id_pau, string $tabla): array|bool;

    /**
     * Obtiene los datos por clave primaria compuesta usando un Value Object
     */
    public function datosByPk(DossierPkAlias $pk): array|bool;

    /**
     * @deprecated usar findByPkVO
     */
    public function findById(int $id_tipo_dossier, int $id_pau, string $tabla): ?Dossier;

    /**
     * Busca la clase por clave primaria compuesta usando un Value Object
     */
    public function findByPk(DossierPkAlias $pk): ?Dossier;
}