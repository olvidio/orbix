<?php

namespace src\ubis\domain\contracts;

use src\ubis\domain\entity\Casa;

/**
 * Interfaz de la clase Casa y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 20/11/2025
 */
interface CasaRepositoryInterface
{

    /**
     * @return array<int|string, string>
     */
    public function getArrayCasas(string $sCondicion = ''): array;

/* --------------------  BASiC SEARCH ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo Casa
	 *
	 * @param array<string, mixed> $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array<string, string> $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return list<Casa> Una colección de objetos de tipo Casa
	
	 */
	public function getCasas(array $aWhere=[], array $aOperators=[]): array;
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(Casa $Casa): bool;

	public function Guardar(Casa $Casa): bool;

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
    public function findById(int $id_ubi): ?Casa;
}