<?php

namespace src\actividadestudios\domain\contracts;

use PDO;
use src\actividadestudios\domain\entity\ActividadAsignatura;


/**
 * Interfaz de la clase ActividadAsignatura y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 29/12/2025
 */
interface ActividadAsignaturaRepositoryInterface
{
    public function getAsignaturasCa(int $id_activ, string $tipo = ''): array;

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo ActividadAsignatura
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo ActividadAsignatura
     */
    public function getActividadAsignaturas(array $aWhere = [], array $aOperators = []): array|false;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(ActividadAsignatura $ActividadAsignatura): bool;

    public function Guardar(ActividadAsignatura $ActividadAsignatura): bool;

    public function getErrorTxt(): string;

    public function getoDbl(): PDO;

    public function setoDbl(PDO $oDbl): void;

    public function getNomTabla(): string;

    public function datosById(int $id_activ, int $id_asignatura): array|bool;

    public function findById(int $id_activ, int $id_asignatura): ?ActividadAsignatura;
}