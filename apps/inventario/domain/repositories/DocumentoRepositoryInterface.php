<?php

namespace inventario\domain\repositories;

use PDO;
use web\Desplegable;
use inventario\domain\entity\Documento;


use web\DateTimeLocal;
use web\NullDateTimeLocal;
use core\ConverterDate;
use function core\is_true;
/**
 * Interfaz de la clase Documento y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 12/3/2025
 */
interface DocumentoRepositoryInterface
{

/* -------------------- GESTOR BASE ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo Documento
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|FALSE Una colección de objetos de tipo Documento
	
	 */
	public function getDocumentos(array $aWhere=[], array $aOperators=[]): array|FALSE;
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(Documento $Documento): bool;

	public function Guardar(Documento $Documento): bool;

	public function getErrorTxt(): string;

	public function getoDbl(): PDO;

	public function setoDbl(PDO $oDbl): void;

	public function getNomTabla(): string;
	
    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     * 
     * @param int $id_doc
     * @return array|bool
	
     */
    public function datosById(int $id_doc): array|bool;
	
    /**
     * Busca la clase con id_doc en el repositorio.
	
     */
    public function findById(int $id_doc): ?Documento;
	
    public function getNewId();
}