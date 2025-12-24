<?php

namespace src\actividades\domain\contracts;

use PDO;
use src\actividades\domain\entity\TipoDeActividad;


/**
 * Interfaz de la clase TipoDeActividad y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 9/12/2025
 */
interface TipoDeActividadRepositoryInterface
{

    public function getArrayTiposActividad(string $sid_tipo_activ = '......'): array;

    public function getTiposDeProcesos($sid_tipo_activ = '......', $bdl_propia = true, $sfsv = ''): array;

    public function getId_tipoPosibles($regexp, $expr_txt): array;

    public function getNom_tipoPosibles($num_digitos, $expr_txt): array;

    public function getAsistentesPosibles($aText, $regexp): array;

    public function getActividadesPosibles(int $num_digitos, array $aText, string $expr_txt): array;

    public function getSfsvPosibles($aText): array;


    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo TipoDeActividad
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo TipoDeActividad
     */
    public function getTiposDeActividad(array $aWhere = [], array $aOperators = []): array|false;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(TipoDeActividad $TipoDeActividad): bool;

    public function Guardar(TipoDeActividad $TipoDeActividad): bool;

    public function getErrorTxt(): string;

    public function getoDbl(): PDO;

    public function setoDbl(PDO $oDbl): void;

    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_tipo_activ
     * @return array|bool
     */
    public function datosById(int $id_tipo_activ): array|bool;

    /**
     * Busca la clase con id_tipo_activ en el repositorio.
     */
    public function findById(int $id_tipo_activ): ?TipoDeActividad;
}