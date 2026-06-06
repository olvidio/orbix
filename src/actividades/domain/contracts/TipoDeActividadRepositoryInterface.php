<?php

namespace src\actividades\domain\contracts;

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

    /**
     * @return array<int, string>
     */
    public function getArrayTiposActividad(string $sid_tipo_activ = '......'): array;

    /**
     * @return list<int>
     */
    public function getTiposDeProcesos(string $sid_tipo_activ = '......', bool $bdl_propia = true, string $sfsv = ''): array;

    /**
     * @return array<int|string, true>
     */
    public function getId_tipoPosibles(string $regexp, string $filtro_regexp_txt): array;

    /**
     * @return array{tipo_nom: array<int|string, string>, nom_tipo: array<int, string>}
     */
    public function getNom_tipoPosibles(int $num_digitos, string $filtro_regexp_txt): array;

    /**
     * @param array<int|string, string> $aText
     * @return array<int|string, string>
     */
    public function getAsistentesPosibles(array $aText, string $filtro_regex_txt): array;

    /**
     * @param array<int|string, string> $aText
     * @return array<int|string, string>
     */
    public function getActividadesPosibles(int $num_digitos, array $aText, string $expr_txt): array;

    /**
     * @param array<int|string, string> $aText
     * @return array<int|string, string>
     */
    public function getSfsvPosibles(array $aText): array;


    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo TipoDeActividad
     *
     * @param array<string, mixed> $aWhere asociativo con los valores para cada campo de la BD.
     * @param array<string, string> $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return list<TipoDeActividad>
     */
    public function getTiposDeActividades(array $aWhere = [], array $aOperators = []): array;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(TipoDeActividad $TipoDeActividad): bool;

    public function Guardar(TipoDeActividad $TipoDeActividad): bool;

    public function getErrorTxt(): string;



    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_tipo_activ): array|false;

    /**
     * Busca la clase con id_tipo_activ en el repositorio.
     */
    public function findById(int $id_tipo_activ): ?TipoDeActividad;
}
