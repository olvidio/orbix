<?php

namespace src\asistentes\domain\contracts;

use PDO;
use src\asistentes\domain\entity\Asistente;


/**
 * Interfaz de la clase Asistente y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 16/12/2025
 */
interface AsistenteRepositoryInterface
{


    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Asistente
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo Asistente
     */
    public function getAsistentes(array $aWhere = [], array $aOperators = []): array|false;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Asistente $Asistente): bool;

    public function Guardar(Asistente $Asistente): bool;

    public function getErrorTxt(): string;

    public function getoDbl(): PDO;

    public function setoDbl(PDO $oDbl): void;

    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_activ
     * @return array|bool
     */
    public function datosById(int $id_activ, int $id_nom): array|bool;

    /**
     * Busca la clase con id_activ en el repositorio.
     */
    public function findById(int $id_activ, int $id_nom): ?Asistente;
}