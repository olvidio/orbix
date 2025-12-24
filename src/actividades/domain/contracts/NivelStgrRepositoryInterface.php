<?php

namespace src\actividades\domain\contracts;

use PDO;
use src\actividades\domain\entity\NivelStgr;
use src\actividades\domain\value_objects\NivelStgrId;

/**
 * Interfaz de la clase NivelStgr y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 18/11/2025
 */
interface NivelStgrRepositoryInterface
{

    public function getArrayIdNiveleStgrActivo(): array;
    public function getArrayNivelesStgrBreve(): array;
    public function getArrayNivelesStgr(): array;

/* -------------------- GESTOR BASE ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo NivelStgr
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|false Una colección de objetos de tipo NivelStgr
	
	 */
	public function getNivelesStgr(array $aWhere=[], array $aOperators=[]): array|false;
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(NivelStgr $NivelStgr): bool;

	public function Guardar(NivelStgr $NivelStgr): bool;

	public function getErrorTxt(): string;

	public function getoDbl(): PDO;

	public function setoDbl(PDO $oDbl): void;

	public function getNomTabla(): string;
	
    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     * 
     * @param int $nivel_stgr
     * @return array|bool

     */
    public function datosById(int $nivel_stgr): array|bool;
    
    /**
     * Nuevos métodos con Value Objects
     */
    public function datosByIdVO(NivelStgrId $id): array|bool;
	
    /**
     * Busca la clase con nivel_stgr en el repositorio.
	
     */
    public function findById(int $nivel_stgr): ?NivelStgr;
    public function findByIdVO(NivelStgrId $id): ?NivelStgr;
    
    public function getNewId();
    public function getNewIdVO(): NivelStgrId;
}