<?php

namespace src\inventario\application\repositories;

use PDO;
use src\inventario\domain\contracts\UbiInventarioRepositoryInterface;
use src\inventario\domain\entity\UbiInventario;
use src\inventario\infrastructure\repositories\PgUbiInventarioRepository;


/**
 *
 * Clase para gestionar la lista de objetos tipo UbiInventario
 * 
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 12/3/2025
 */
class UbiInventarioRepository implements UbiInventarioRepositoryInterface
{

    /**$
     * @var UbiInventarioRepositoryInterface
     */
    private UbiInventarioRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = new PgUbiInventarioRepository();
    }

    public function getUbisInventarioLugar($bLugar): false|array
    {
        return $this->repository->getUbisInventarioLugar($bLugar);
    }

    public function getArrayUbisInventario(): array
    {
        return $this->repository->getArrayUbisInventario();
    }


/* -------------------- GESTOR BASE ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo UbiInventario
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|FALSE Una colección de objetos de tipo UbiInventario
	
	 */
	public function getUbisInventario(array $aWhere=[], array $aOperators=[]): array|FALSE
	{
	    return $this->repository->getUbisInventario($aWhere, $aOperators);
	}
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(UbiInventario $UbiInventario): bool
    {
        return $this->repository->Eliminar($UbiInventario);
    }

	public function Guardar(UbiInventario $UbiInventario): bool
    {
        return $this->repository->Guardar($UbiInventario);
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
     * @param int $id_ubi
     * @return array|bool
	
     */
    public function datosById(int $id_ubi): array|bool
    {
        return $this->repository->datosById($id_ubi);
    }
	
    /**
     * Busca la clase con id_ubi en el repositorio.
	
     */
    public function findById(int $id_ubi): ?UbiInventario
    {
        return $this->repository->findById($id_ubi);
    }
	
    public function getNewId()
    {
        return $this->repository->getNewId();
    }
}