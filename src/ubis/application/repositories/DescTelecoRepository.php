<?php

namespace src\ubis\application\repositories;

use PDO;
use src\ubis\domain\entity\DescTeleco;
use src\ubis\domain\contracts\DescTelecoRepositoryInterface;
use src\ubis\infrastructure\repositories\PgDescTelecoRepository;


use function core\is_true;
/**
 *
 * Clase para gestionar la lista de objetos tipo DescTeleco
 * 
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 17/11/2025
 */
class DescTelecoRepository implements DescTelecoRepositoryInterface
{

    /**$
     * @var DescTelecoRepositoryInterface
     */
    private DescTelecoRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = new PgDescTelecoRepository();
    }


    public function getArrayDescTelecoPersonas($sdepende): array
    {
        return $this->repository->getArrayDescTelecoPersonas($sdepende);
    }

    public function getArrayDescTelecoUbis($sdepende): array
    {
        return $this->repository->getArrayDescTelecoUbis($sdepende);
    }

/* -------------------- GESTOR BASE ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo DescTeleco
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|FALSE Una colección de objetos de tipo DescTeleco
	
	 */
	public function getDescsTeleco(array $aWhere=[], array $aOperators=[]): array|FALSE
	{
	    return $this->repository->getDescsTeleco($aWhere, $aOperators);
	}
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(DescTeleco $DescTeleco): bool
    {
        return $this->repository->Eliminar($DescTeleco);
    }

	public function Guardar(DescTeleco $DescTeleco): bool
    {
        return $this->repository->Guardar($DescTeleco);
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
     * @param int $id_item
     * @return array|bool
	
     */
    public function datosById(int $id_item): array|bool
    {
        return $this->repository->datosById($id_item);
    }
	
    /**
     * Busca la clase con id_item en el repositorio.
	
     */
    public function findById(int $id_item): ?DescTeleco
    {
        return $this->repository->findById($id_item);
    }

    public function getNewId(): int
    {
        return $this->repository->getNewId();
    }
}