<?php

namespace src\ubis\domain\contracts;

use PDO;
use src\ubis\domain\entity\TipoCasa;

/**
 * Interfaz de la clase TipoCasa y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 14/11/2025
 */
interface TipoCasaRepositoryInterface
{

    public function getArrayTiposCasa(): array;

/* -------------------- GESTOR BASE ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo TipoCasa
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|false Una colección de objetos de tipo TipoCasa
	
	 */
	public function getTiposCasa(array $aWhere=[], array $aOperators=[]): array|false;
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(TipoCasa $TipoCasa): bool;

	public function Guardar(TipoCasa $TipoCasa): bool;

	public function getErrorTxt(): string;

	public function getoDbl(): PDO;

	public function setoDbl(PDO $oDbl): void;

	public function getNomTabla(): string;
	
    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     * 
     * @param string $tipo_casa
     * @return array|bool
	
     */
    public function datosById(string $tipo_casa): array|bool;
	
    /**
     * Busca la clase con tipo_casa en el repositorio.
	
     */
    public function findById(string $tipo_casa): ?TipoCasa;
}