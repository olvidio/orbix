<?php

namespace src\ubis\domain\contracts;

use PDO;
use src\ubis\domain\entity\DescTeleco;

use function core\is_true;
/**
 * Interfaz de la clase DescTeleco y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 17/11/2025
 */
interface DescTelecoRepositoryInterface
{

    public function getArrayDescTelecoPersonas($sdepende): array;

/* -------------------- GESTOR BASE ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo DescTeleco
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|false Una colección de objetos de tipo DescTeleco
	
	 */
	public function getDescsTeleco(array $aWhere=[], array $aOperators=[]): array|false;

    public function getArrayDescTelecoUbis($sdepende): array;

/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(DescTeleco $DescTeleco): bool;

	public function Guardar(DescTeleco $DescTeleco): bool;

	public function getErrorTxt(): string;

	public function getoDbl(): PDO;

	public function setoDbl(PDO $oDbl): void;

	public function getNomTabla(): string;
	
    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     * 
     * @param int $id_item
     * @return array|bool
	
     */
    public function datosById(int $id_item): array|bool;
	
    /**
     * Busca la clase con id_item en el repositorio.
	
     */
    public function findById(int $id_item): ?DescTeleco;

    public function getNewId(): int;
}