<?php

namespace src\actividades\domain\contracts;

use PDO;
use src\actividades\domain\entity\ActividadAll;
use src\shared\domain\value_objects\DateTimeLocal;


/**
 * Interfaz de la clase ActividadAll y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 2/12/2025
 */
interface ActividadAllRepositoryInterface
{

    public function actividadesDeUnaCasa(int $id_ubi, DateTimeLocal $oFini, DateTimeLocal $oFfin, $cdc_sel = 0): array|false;

    public function getCoincidencia($oActividad): bool;

    public function getUbis($aWhere = [], $aOperators = []): array;

    public function getArrayActividadesDeTipo($sid_tipo = '......', $scondicion = ''): array;

    public function getArrayIdsWithKeyFini($aWhere = [], $aOperators = []): array;

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo ActividadAll
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo ActividadAll
     */
    public function getActividades(array $aWhere = [], array $aOperators = []): array|false;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(ActividadAll $ActividadAll): bool;

    public function Guardar(ActividadAll $ActividadAll): bool;

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
    public function datosById(int $id_activ): array|bool;

    /**
     * Busca la clase con id_activ en el repositorio.
     */
    public function findById(int $id_activ): ?ActividadAll;
}