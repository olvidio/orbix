<?php

namespace src\actividadescentro\domain\contracts;

use PDO;
use src\actividadescentro\domain\entity\CentroEncargado;
use src\actividadescentro\domain\value_objects\CentroEncargadoPk;


/**
 * Interfaz de la clase CentroEncargado y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 19/12/2025
 */
interface CentroEncargadoRepositoryInterface
{

    public function getProximasActividadesDeCentro(int $id_ubi, string $f_ini_act_iso): string;

    public function getActividadesDeCentros(int $iid_ubi, string $scondicion = ''): array;

    public function getCentrosEncargadosActividad(int $iid_activ): array;
    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo CentroEncargado
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo CentroEncargado
     */
    public function getCentrosEncargados(array $aWhere = [], array $aOperators = []): array|false;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(CentroEncargado $CentroEncargado): bool;

    public function Guardar(CentroEncargado $CentroEncargado): bool;

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
    public function datosById(int $id_activ, int $id_ubi): array|bool;

    public function datosByPk(CentroEncargadoPk $pk): array|bool;

    /**
     * Busca la clase con id_activ en el repositorio.
     */
    public function findById(int $id_activ, int $id_ubi): ?CentroEncargado;

    public function findByPk(CentroEncargadoPk $pk): ?CentroEncargado;
}