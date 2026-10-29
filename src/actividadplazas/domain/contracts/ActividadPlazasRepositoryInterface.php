<?php

namespace src\actividadplazas\domain\contracts;

use JsonException;
use src\actividadplazas\domain\entity\ActividadPlazas;
use src\shared\domain\criteria\Criteria;


/**
 * Interfaz de la clase ActividadPlazas y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 16/12/2025
 */
interface ActividadPlazasRepositoryInterface
{

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * ActividadPlazas que cumplen el criterio.
     *
     * @return list<ActividadPlazas>
     */
    public function matching(Criteria $criteria): array;

    /**
     * @deprecated usar matching(Criteria)
     * @param array<string, mixed> $aWhere asociativo con los valores para cada campo de la BD.
     * @param array<string, string> $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return list<ActividadPlazas>
     * @throws JsonException
     */
    public function getActividadesPlazas(array $aWhere = [], array $aOperators = []): array;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(ActividadPlazas $ActividadPlazas): bool;

    public function Guardar(ActividadPlazas $ActividadPlazas): bool;

    public function getErrorTxt(): string;



    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @return array<string, mixed>|false
     * @throws JsonException
     */
    public function datosById(int $id_activ): array|false;

    /**
     * Busca la clase con id_activ en el repositorio.
     * @throws JsonException
     */
    public function findById(int $id_activ): ?ActividadPlazas;
}