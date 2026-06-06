<?php

namespace src\actividades\domain\contracts;

use src\actividades\domain\entity\Repeticion;
use src\actividades\domain\value_objects\RepeticionId;

/**
 * Interfaz de la clase Repeticion y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 18/11/2025
 */
interface RepeticionRepositoryInterface
{

    /**
     * @return array<int, string>
     */
    public function getArrayRepeticion(): array;

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Repeticion
     *
     * @param array<string, mixed> $aWhere asociativo con los valores para cada campo de la BD.
     * @param array<string, string> $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return list<Repeticion>
     */
    public function getRepeticiones(array $aWhere = [], array $aOperators = []): array;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Repeticion $Repeticion): bool;

    public function Guardar(Repeticion $Repeticion): bool;

    public function getErrorTxt(): string;



    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @return array<string, mixed>|false
     */
    public function datosById(RepeticionId $id_repeticion): array|false;

    /**
     * Busca la clase con id_repeticion en el repositorio.
     */
    public function findById(RepeticionId $id_repeticion): ?Repeticion;

    public function getNewId(): int;

    public function getNewIdVo(): RepeticionId;
}
