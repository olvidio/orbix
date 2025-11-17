<?php

namespace src\ubis\domain\contracts;

use PDO;
use src\ubis\domain\entity\TipoTeleco;


use function core\is_true;
/**
 * Interfaz de la clase TipoTeleco y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 17/11/2025
 */
interface TipoTelecoRepositoryInterface
{

    public function getArrayTiposTelecoPersona(): array;
    public function getArrayTiposTelecoUbi(): array;

/* -------------------- GESTOR BASE ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo TipoTeleco
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|FALSE Una colección de objetos de tipo TipoTeleco
	
	 */
	public function getTiposTeleco(array $aWhere=[], array $aOperators=[]): array|FALSE;
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(TipoTeleco $TipoTeleco): bool;

	public function Guardar(TipoTeleco $TipoTeleco): bool;

	public function getErrorTxt(): string;

	public function getoDbl(): PDO;

	public function setoDbl(PDO $oDbl): void;

	public function getNomTabla(): string;
	
    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     * 
     * @param int $id
     * @return array|bool
	
     */
    public function datosById(int $id): array|bool;
	
    /**
     * Busca la clase con id en el repositorio.
	
     */
    public function findById(int $id): ?TipoTeleco;
    public function getNewId();
}