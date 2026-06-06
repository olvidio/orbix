<?php

namespace src\actividades\domain\contracts;

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

    /**
     * @return list<array<string, mixed>>
     */
    public function actividadesDeUnaCasa(int $id_ubi, DateTimeLocal $oFini, DateTimeLocal $oFfin, int $cdc_sel = 0): array;

    public function getCoincidencia(ActividadAll $oActividad): bool;

    /**
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<int|null>
     */
    public function getUbis(array $aWhere = [], array $aOperators = []): array;

    /**
     * @return array<int, string>
     */
    public function getArrayActividadesDeTipo(string $sid_tipo = '......', string $scondicion = ''): array;

    /**
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return array<string, int>
     */
    public function getArrayIdsWithKeyFini(array $aWhere = [], array $aOperators = []): array;

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo ActividadAll
     *
     * @param array<string, mixed> $aWhere asociativo con los valores para cada campo de la BD.
     * @param array<string, string> $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return list<ActividadAll>
     */
    public function getActividades(array $aWhere = [], array $aOperators = []): array;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(ActividadAll $ActividadAll, bool $registrarCambios = true): bool;

    public function Guardar(ActividadAll $ActividadAll, bool $registrarCambios = true): bool;

    public function getErrorTxt(): string;



    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_activ): array|false;

    /**
     * Busca la clase con id_activ en el repositorio.
     */
    public function findById(int $id_activ): ?ActividadAll;
}
