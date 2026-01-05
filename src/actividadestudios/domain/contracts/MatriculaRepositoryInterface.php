<?php

namespace src\actividadestudios\domain\contracts;

use PDO;
use src\actividadestudios\domain\entity\Matricula;


/**
 * Interfaz de la clase Matricula y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 29/12/2025
 */
interface MatriculaRepositoryInterface
{

    public function getMatriculasPendientes(?int $id_nom = null): array;

    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Matricula
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo Matricula
     */
    public function getMatriculas(array $aWhere = [], array $aOperators = []): array|false;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Matricula $Matricula): bool;

    public function Guardar(Matricula $Matricula): bool;

    public function getErrorTxt(): string;

    public function getoDbl(): PDO;

    public function setoDbl(PDO $oDbl): void;

    public function getNomTabla(): string;

    public function datosById(int $id_activ, int $id_asignatura, int $id_nom): array|bool;

    public function findById(int $id_activ, int $id_asignatura, int $id_nom): ?Matricula;
}