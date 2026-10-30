<?php

namespace src\personas\domain\contracts;

use src\shared\domain\criteria\Criteria;
use src\personas\domain\entity\Situacion;

/**
 * Interfaz de la clase Situacion y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 17/11/2025
 */
interface SituacionRepositoryInterface
{

    /**
     * @return array<string, string>
     */
    public function getArraySituaciones(bool $traslado = false): array;

/* --------------------  BASiC SEARCH ---------------------------------------- */

	/**
	 * @return list<Situacion>
	 */
	public function matching(Criteria $criteria): array;

/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(Situacion $Situacion): bool;

	public function Guardar(Situacion $Situacion): bool;

	public function getErrorTxt(): string;

	public function getNomTabla(): string;
	
    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     * 
     * @param string $situacion
     * @return array<string, mixed>|false
	
     */
    public function datosById(string $situacion): array|false;
	
    /**
     * Busca la clase con situacion en el repositorio.
	
     */
    public function findById(string $situacion): ?Situacion;
}
