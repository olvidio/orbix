<?php

namespace src\ubis\domain\contracts;

use src\ubis\domain\entity\CentroEx;
use src\shared\domain\criteria\Criteria;

/**
 * Interfaz de la clase CentroEx y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 20/11/2025
 */
interface CentroExRepositoryInterface
{

    /**
     * @return array<int|string, string>
     */
    public function getArrayCentros(string $sCondicion = ''): array;

/* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * CentroEx que cumplen el criterio.
     *
     * @return list<CentroEx>
     */
    public function matching(Criteria $criteria): array;

    /**
     * @deprecated usar matching(Criteria)
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<CentroEx>
     */
    public function getCentros(array $aWhere=[], array $aOperators=[]): array;
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(CentroEx $CentroEx): bool;

	public function Guardar(CentroEx $CentroEx): bool;

	public function getErrorTxt(): string;



	public function getNomTabla(): string;
	
    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     * 
     * @param int $id_ubi
     * @return array<string, mixed>|false
	
     */
    /**
     * @return array<string, mixed>|false
     */
    /**
     * @return array<string, mixed>|false
     */
    /**
     * @return array<string, mixed>|false
     */
    /**
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_ubi): array|false;
	
    /**
     * Busca la clase con id_ubi en el repositorio.
	
     */
    public function findById(int $id_ubi): ?CentroEx;
	
    public function getNewId(): int;
    public function getNewIdUbi(int $id): int;

}