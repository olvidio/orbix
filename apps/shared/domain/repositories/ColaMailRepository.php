<?php

namespace shared\domain\repositories;

use PDO;
use shared\domain\entity\ColaMail;


/**
 *
 * Clase para gestionar la lista de objetos tipo ColaMail
 * 
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 1/3/2024
 */
class ColaMailRepository implements ColaMailRepositoryInterface
{

    /**$
     * @var ColaMailRepositoryInterface
     */
    private ColaMailRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = $GLOBALS['container']->get(ColaMailRepositoryInterface::class);
    }

/* -------------------- GESTOR BASE ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo ColaMail
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|FALSE Una colección de objetos de tipo ColaMail
	
	 */
	public function getColaMails(array $aWhere=[], array $aOperators=[]): array|FALSE
	{
	    return $this->repository->getColaMails($aWhere, $aOperators);
	}

    public function deleteColaMails(string $date_iso): void
    {
        $this->repository->deleteColaMails($date_iso);
    }
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(ColaMail $ColaMail): bool
    {
        return $this->repository->Eliminar($ColaMail);
    }

	public function Guardar(ColaMail $ColaMail): bool
    {
        return $this->repository->Guardar($ColaMail);
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
     * @param  $uuid_item
     * @return array|bool
	
     */
    public function datosById( $uuid_item): array|bool
    {
        return $this->repository->datosById($uuid_item);
    }
	
    /**
     * Busca la clase con uuid_item en el repositorio.
	
     */
    public function findById( $uuid_item): ?ColaMail
    {
        return $this->repository->findById($uuid_item);
    }

}