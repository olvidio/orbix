<?php

namespace src\notas\domain\contracts;

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
    public function getUltimaActa(int|string $any, string $sRegion = '?'): int;

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo ActaDl
     *
     * @param array<string, mixed> $aWhere asociativo con los valores para cada campo de la BD.
     * @param array<string, string> $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return list<\src\notas\domain\entity\Acta> Una colección de objetos de tipo Acta
     */
    /** @param array<string, mixed> $aWhere */

    /**
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<\src\notas\domain\entity\Acta>
     */
    public function getActas(array $aWhere = [], array $aOperators = []): array;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Acta $Acta): bool;

    public function Guardar(Acta $Acta): bool;

    public function getErrorTxt(): string;



    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param string $acta
     * @return array<string, mixed>|false
     */
    public function datosById(string $acta): array|false;

    /**
     * Busca la clase con acta en el repositorio.
     */
    public function findById(string $acta): ?Acta;
}