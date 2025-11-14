<?php

namespace src\ubis\domain\contracts;

use PDO;
use src\ubis\domain\entity\TipoCentro;


/**
 * Interfaz de la clase TipoCentro y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 14/11/2025
 */
interface TipoCentroRepositoryInterface
{

    public function getArrayTiposCentro(): array;

/* -------------------- GESTOR BASE ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo TipoCentro
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|FALSE Una colección de objetos de tipo TipoCentro
	
	 */
	public function getTiposCentro(array $aWhere=[], array $aOperators=[]): array|FALSE;
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(TipoCentro $TipoCentro): bool;

	public function Guardar(TipoCentro $TipoCentro): bool;

	public function getErrorTxt(): string;

	public function getoDbl(): PDO;

	public function setoDbl(PDO $oDbl): void;

	public function getNomTabla(): string;
	
    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     * 
     * @param string $tipo_ctr
     * @return array|bool
	
     */
    public function datosById(string $tipo_ctr): array|bool;
	
    /**
     * Busca la clase con tipo_ctr en el repositorio.
	
     */
    public function findById(string $tipo_ctr): ?TipoCentro;
}