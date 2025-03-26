<?php

namespace inventario\domain\repositories;

use PDO;
use web\Desplegable;
use inventario\domain\entity\Coleccion;
use inventario\infrastructure\PgColeccionRepository;


use function core\is_true;
/**
 *
 * Clase para gestionar la lista de objetos tipo Coleccion
 * 
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 12/3/2025
 */
class ColeccionRepository implements ColeccionRepositoryInterface
{

    /**$
     * @var ColeccionRepositoryInterface
     */
    private ColeccionRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = new PgColeccionRepository();
    }

    public function getArrayColecciones(): array
    {
        return $this->repository->getArrayColecciones();
    }
/* -------------------- GESTOR BASE ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo Coleccion
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|FALSE Una colección de objetos de tipo Coleccion
	
	 */
	public function getColecciones(array $aWhere=[], array $aOperators=[]): array|FALSE
	{
	    return $this->repository->getColecciones($aWhere, $aOperators);
	}
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(Coleccion $Coleccion): bool
    {
        return $this->repository->Eliminar($Coleccion);
    }

	public function Guardar(Coleccion $Coleccion): bool
    {
        return $this->repository->Guardar($Coleccion);
    }

	public function getErrorTxt(): string
    {
        return $this->repository->getErrorTxt();
    }

	public function getoDbl(): PDO
    {
        return $this->repository->getoDbl();
    }

	public function setoDbl(PDO $oDbl): void
    {
        $this->repository->setoDbl($oDbl);
    }

	public function getNomTabla(): string
    {
        return $this->repository->getNomTabla();
    }
	
    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     * 
     * @param int $id_coleccion
     * @return array|bool
	
     */
    public function datosById(int $id_coleccion): array|bool
    {
        return $this->repository->datosById($id_coleccion);
    }
	
    /**
     * Busca la clase con id_coleccion en el repositorio.
	
     */
    public function findById(int $id_coleccion): ?Coleccion
    {
        return $this->repository->findById($id_coleccion);
    }
	
    public function getNewId()
    {
        return $this->repository->getNewId();
    }
}