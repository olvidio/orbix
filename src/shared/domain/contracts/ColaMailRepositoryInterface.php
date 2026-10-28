<?php

namespace src\shared\domain\contracts;

use src\shared\domain\criteria\Criteria;
use src\shared\domain\entity\ColaMail;


/**
 * Interfaz de la clase ColaMail y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 1/3/2024
 */
interface ColaMailRepositoryInterface
{

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * Mails de la cola que cumplen el criterio.
     *
     * @return list<ColaMail>
     */
    public function matching(Criteria $criteria): array;

    /**
     * devuelve una colección (array) de objetos de tipo ColaMail
     *
     * @deprecated usar matching(Criteria)
     * @param array<string, mixed> $aWhere asociativo con los valores para cada campo de la BD.
     * @param array<string, string> $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array<int, ColaMail>
     */
    public function getColaMails(array $aWhere = [], array $aOperators = []): array;

    public function deleteColaMails(string $date_iso): void;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(ColaMail $ColaMail): bool;

    public function Guardar(ColaMail $ColaMail): bool;

    public function getErrorTxt(): string;



    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @return array<string, mixed>|false
     */
    public function datosById(string $uuid_item): array|false;

    /**
     * Busca la clase con uuid_item en el repositorio.
     */
    public function findById(string $uuid_item): ?ColaMail;
}
