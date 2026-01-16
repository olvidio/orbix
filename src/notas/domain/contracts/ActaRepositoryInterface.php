<?php

namespace src\notas\domain\contracts;

use PDO;
use src\notas\domain\entity\Acta;


/**
 * Interfaz de la clase ActaDl y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 27/12/2025
 */
interface ActaRepositoryInterface
{
    public function getUltimaActa($any, $sRegion = '?'): int;

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo ActaDl
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo ActaDl
     */
    public function getActas(array $aWhere = [], array $aOperators = []): array|false;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Acta $Acta): bool;

    public function Guardar(Acta $Acta): bool;

    public function getErrorTxt(): string;

    public function getoDbl(): PDO;

    public function setoDbl(PDO $oDbl): void;

    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param string $acta
     * @return array|bool
     */
    public function datosById(string $acta): array|bool;

    /**
     * Busca la clase con acta en el repositorio.
     */
    public function findById(string $acta): ?Acta;
}